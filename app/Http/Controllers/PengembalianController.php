<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\PengembalianDetail;
use App\Models\BarangHilang;
use App\Models\SarprasUnit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengembalianController extends Controller
{
    /**
     * Lookup peminjaman by QR code and redirect to pengembalian form.
     */
    public function lookupByQrCode(Request $request)
    {
        $qrCode = $request->input('qr_code');
        
        if (!$qrCode) {
            return redirect()->route('peminjaman.index')->with('error', 'Kode QR tidak boleh kosong.');
        }

        $peminjaman = Peminjaman::where('qr_code', $qrCode)->first();

        if (!$peminjaman) {
            return redirect()->route('peminjaman.index')->with('error', 'Peminjaman dengan kode QR tersebut tidak ditemukan.');
        }

        if ($peminjaman->status !== 'disetujui') {
            return redirect()->route('peminjaman.show', $peminjaman)->with('error', 'Peminjaman ini tidak dalam status aktif untuk dikembalikan.');
        }

        return redirect()->route('pengembalian.create', $peminjaman);
    }

    /**
     * Show the form for creating a new pengembalian.
     * Setiap unit harus diinspeksi kondisinya.
     */
    public function create(Peminjaman $peminjaman): View
    {
        // Hanya bisa mengembalikan peminjaman yang statusnya 'disetujui'
        if ($peminjaman->status !== 'disetujui') {
            abort(403, 'Peminjaman ini tidak dalam status aktif.');
        }

        // Cek apakah sudah ada pengembalian
        if ($peminjaman->pengembalian) {
            abort(403, 'Peminjaman ini sudah dikembalikan.');
        }

        $peminjaman->load([
            'user',
            'details.sarprasUnit.sarpras',
            'details.sarprasUnit.lokasi',
        ]);

        return view('pengembalian.create', compact('peminjaman'));
    }

    /**
     * Store a newly created pengembalian in storage.
     * Proses inspeksi kondisi setiap unit.
     */
    public function store(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        // Validasi
        if ($peminjaman->status !== 'disetujui') {
            return redirect()
                ->route('peminjaman.index')
                ->with('error', 'Peminjaman ini tidak dalam status aktif.');
        }

        if ($peminjaman->pengembalian) {
            return redirect()
                ->route('peminjaman.index')
                ->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        // Build validation rules dynamically based on units
        $rules = [
            'tgl_kembali_aktual' => 'required|date',
        ];

        $messages = [
            'tgl_kembali_aktual.required' => 'Tanggal pengembalian wajib diisi.',
        ];

        foreach ($peminjaman->details as $detail) {
            $unitId = $detail->sarpras_unit_id;
            $rules["kondisi_{$unitId}"] = 'required|in:baik,rusak_ringan,rusak_berat,hilang';
            $rules["catatan_{$unitId}"] = 'nullable|string|max:500';
            $rules["denda_{$unitId}"] = 'nullable|integer|min:0';
            $rules["foto_{$unitId}"] = 'nullable|image|max:2048';

            $messages["kondisi_{$unitId}.required"] = "Kondisi unit {$detail->sarprasUnit->kode_unit} wajib dipilih.";
        }

        $validated = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // Buat record pengembalian header
            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'petugas_id' => auth()->id(),
                'tgl_kembali_aktual' => $validated['tgl_kembali_aktual'],
            ]);

            // Proses setiap unit
            foreach ($peminjaman->details as $detail) {
                $unitId = $detail->sarpras_unit_id;
                $unit = $detail->sarprasUnit;
                $kondisiAkhir = $validated["kondisi_{$unitId}"];

                // Upload foto jika ada
                $fotoPath = null;
                if ($request->hasFile("foto_{$unitId}")) {
                    $fotoPath = $request->file("foto_{$unitId}")
                        ->store('pengembalian', 'public');
                }

                // Buat detail pengembalian
                $pengembalianDetail = PengembalianDetail::create([
                    'pengembalian_id' => $pengembalian->id,
                    'sarpras_unit_id' => $unitId,
                    'kondisi_akhir' => $kondisiAkhir,
                    'foto_kondisi' => $fotoPath,
                    'catatan' => $validated["catatan_{$unitId}"] ?? null,
                    'denda' => $validated["denda_{$unitId}"] ?? null,
                ]);

                // Update status dan kondisi unit berdasarkan hasil inspeksi
                switch ($kondisiAkhir) {
                    case 'baik':
                        $unit->update([
                            'kondisi' => SarprasUnit::KONDISI_BAIK,
                            'status' => SarprasUnit::STATUS_TERSEDIA,
                        ]);
                        break;

                    case 'rusak_ringan':
                        $unit->update([
                            'kondisi' => SarprasUnit::KONDISI_RUSAK_RINGAN,
                            'status' => SarprasUnit::STATUS_TERSEDIA, // Masih bisa dipakai
                        ]);
                        break;

                    case 'rusak_berat':
                        $unit->update([
                            'kondisi' => SarprasUnit::KONDISI_RUSAK_BERAT,
                            'status' => SarprasUnit::STATUS_MAINTENANCE,
                        ]);
                        break;

                    case 'hilang':
                        $unit->update([
                            'status' => SarprasUnit::STATUS_DIHAPUSBUKUKAN,
                        ]);

                        // Catat ke tabel barang hilang
                        BarangHilang::create([
                            'pengembalian_detail_id' => $pengembalianDetail->id,
                            'user_id' => $peminjaman->user_id,
                            'keterangan' => $validated["catatan_{$unitId}"] ?? 'Barang hilang saat dipinjam.',
                            'status' => BarangHilang::STATUS_BELUM_DIGANTI,
                        ]);
                        break;
                }
            }

            // Update status peminjaman menjadi selesai
            $peminjaman->update(['status' => 'selesai']);

            DB::commit();
            
            \App\Helpers\LogHelper::record('update', "Memproses pengembalian untuk peminjaman ID: {$peminjaman->id}");

            $totalDenda = $pengembalian->total_denda;
            $message = 'Pengembalian berhasil dicatat.';
            if ($totalDenda > 0) {
                $message .= ' Total denda: Rp ' . number_format($totalDenda, 0, ',', '.');
            }

            return redirect()
                ->route('peminjaman.show', $peminjaman)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified pengembalian.
     */
    public function show(Pengembalian $pengembalian): View
    {
        $pengembalian->load([
            'peminjaman.user',
            'petugas',
            'details.sarprasUnit.sarpras',
        ]);

        return view('pengembalian.show', compact('pengembalian'));
    }
}
