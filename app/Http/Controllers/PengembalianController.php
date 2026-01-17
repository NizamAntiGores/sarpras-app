<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\BarangHilang;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengembalianController extends Controller
{
    /**
     * Show the form for creating a new pengembalian (inspeksi).
     */
    public function create(Peminjaman $peminjaman): View
    {
        // Hanya bisa proses pengembalian jika status = disetujui
        if ($peminjaman->status !== 'disetujui') {
            abort(403, 'Peminjaman ini tidak dalam status aktif.');
        }

        $peminjaman->load(['user', 'sarpras']);

        return view('pengembalian.create', compact('peminjaman'));
    }

    /**
     * Store a newly created pengembalian in storage.
     * Proses inspeksi kondisi barang saat dikembalikan
     */
    public function store(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        // Validasi status peminjaman
        if ($peminjaman->status !== 'disetujui') {
            return redirect()
                ->back()
                ->with('error', 'Peminjaman ini tidak dalam status aktif.');
        }

        $validated = $request->validate([
            'kondisi_akhir' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'foto_kondisi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'keterangan' => 'nullable|string|max:500',
        ], [
            'kondisi_akhir.required' => 'Kondisi akhir barang wajib dipilih.',
            'kondisi_akhir.in' => 'Kondisi akhir tidak valid.',
            'foto_kondisi.image' => 'File harus berupa gambar.',
            'foto_kondisi.mimes' => 'Format gambar harus JPEG, PNG, atau JPG.',
            'foto_kondisi.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        DB::beginTransaction();

        try {
            $sarpras = $peminjaman->sarpras;
            $fotoPath = null;

            // Upload foto jika ada
            if ($request->hasFile('foto_kondisi')) {
                $fotoPath = $request->file('foto_kondisi')->store('pengembalian', 'public');
            }

            // Buat record pengembalian
            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'petugas_id' => auth()->id(),
                'tgl_kembali_aktual' => now(),
                'kondisi_akhir' => $validated['kondisi_akhir'],
                'foto_kondisi' => $fotoPath,
                'denda' => 0, // Tidak menggunakan sistem denda
            ]);

            // Update stok berdasarkan kondisi
            switch ($validated['kondisi_akhir']) {
                case 'baik':
                    // Kembalikan ke stok tersedia
                    $sarpras->increment('stok', $peminjaman->jumlah_pinjam);
                    break;

                case 'rusak_ringan':
                case 'rusak_berat':
                    // Pindahkan ke stok rusak
                    $sarpras->increment('stok_rusak', $peminjaman->jumlah_pinjam);
                    break;

                case 'hilang':
                    // Catat ke tabel barang_hilang (tidak menambah stok apapun)
                    BarangHilang::create([
                        'pengembalian_id' => $pengembalian->id,
                        'sarpras_id' => $sarpras->id,
                        'user_id' => $peminjaman->user_id,
                        'jumlah' => $peminjaman->jumlah_pinjam,
                        'keterangan' => $validated['keterangan'] ?? 'Barang hilang saat peminjaman',
                        'status' => 'belum_diganti',
                    ]);
                    break;
            }

            // Update status peminjaman menjadi selesai
            $peminjaman->update([
                'status' => 'selesai',
                'keterangan' => $validated['keterangan'] ?? $peminjaman->keterangan,
            ]);

            DB::commit();

            $kondisiMessages = [
                'baik' => 'Barang dikembalikan dalam kondisi baik. Stok telah diperbarui.',
                'rusak_ringan' => 'Barang dikembalikan dengan kerusakan ringan. Stok rusak telah diperbarui.',
                'rusak_berat' => 'Barang dikembalikan dengan kerusakan berat. Stok rusak telah diperbarui.',
                'hilang' => 'Barang dinyatakan hilang dan telah dicatat dalam sistem.',
            ];

            return redirect()
                ->route('peminjaman.show', $peminjaman)
                ->with('success', $kondisiMessages[$validated['kondisi_akhir']]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus foto jika sudah terupload
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
