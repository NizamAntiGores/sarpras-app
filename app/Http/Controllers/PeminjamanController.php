<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Sarpras;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     * - Jika Peminjam: hanya data dia sendiri
     * - Jika Admin/Petugas: semua data
     */
    public function index(): View
    {
        $user = auth()->user();
        
        $query = Peminjaman::with(['user', 'sarpras', 'petugas'])
            ->orderBy('created_at', 'desc');

        // Jika user adalah peminjam, hanya tampilkan data miliknya
        if ($user->role === 'peminjam') {
            $query->where('user_id', $user->id);
        }

        $peminjaman = $query->paginate(10);

        return view('peminjaman.index', compact('peminjaman'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Hanya tampilkan sarpras yang stok > 0 (tersedia untuk dipinjam)
        $sarpras = Sarpras::where('stok', '>', 0)
            ->orderBy('nama_barang')
            ->get();

        return view('peminjaman.create', compact('sarpras'));
    }

    /**
     * Store a newly created resource in storage.
     * Logika untuk Siswa/Peminjam membuat pengajuan peminjaman baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sarpras_id' => 'required|exists:sarpras,id',
            'jumlah_pinjam' => 'required|integer|min:1',
            'tgl_pinjam' => 'required|date|after_or_equal:today',
            'tgl_kembali_rencana' => 'required|date|after:tgl_pinjam',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'sarpras_id.required' => 'Barang wajib dipilih.',
            'sarpras_id.exists' => 'Barang tidak ditemukan.',
            'jumlah_pinjam.required' => 'Jumlah pinjam wajib diisi.',
            'jumlah_pinjam.integer' => 'Jumlah pinjam harus berupa angka.',
            'jumlah_pinjam.min' => 'Jumlah pinjam minimal 1.',
            'tgl_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tgl_pinjam.after_or_equal' => 'Tanggal pinjam tidak boleh sebelum hari ini.',
            'tgl_kembali_rencana.required' => 'Tanggal kembali rencana wajib diisi.',
            'tgl_kembali_rencana.after' => 'Tanggal kembali harus setelah tanggal pinjam.',
        ]);

        // Cek ketersediaan stok
        $sarpras = Sarpras::findOrFail($validated['sarpras_id']);
        
        if ($sarpras->stok < $validated['jumlah_pinjam']) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Stok tidak mencukupi. Stok tersedia: {$sarpras->stok}");
        }

        // Cek double booking - apakah ada peminjaman yang overlap tanggalnya
        $tglPinjam = $validated['tgl_pinjam'];
        $tglKembali = $validated['tgl_kembali_rencana'];

        $existingBooking = Peminjaman::where('sarpras_id', $validated['sarpras_id'])
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->where(function ($query) use ($tglPinjam, $tglKembali) {
                // Cek overlap: peminjaman existing overlap dengan tanggal yang diminta
                $query->where(function ($q) use ($tglPinjam, $tglKembali) {
                    // Case 1: Tanggal pinjam baru berada di dalam rentang existing
                    $q->where('tgl_pinjam', '<=', $tglPinjam)
                      ->where('tgl_kembali_rencana', '>=', $tglPinjam);
                })->orWhere(function ($q) use ($tglPinjam, $tglKembali) {
                    // Case 2: Tanggal kembali baru berada di dalam rentang existing
                    $q->where('tgl_pinjam', '<=', $tglKembali)
                      ->where('tgl_kembali_rencana', '>=', $tglKembali);
                })->orWhere(function ($q) use ($tglPinjam, $tglKembali) {
                    // Case 3: Rentang baru mencakup seluruh rentang existing
                    $q->where('tgl_pinjam', '>=', $tglPinjam)
                      ->where('tgl_kembali_rencana', '<=', $tglKembali);
                });
            })
            ->first();

        if ($existingBooking) {
            $tglExisting = $existingBooking->tgl_pinjam->format('d M Y') . ' - ' . $existingBooking->tgl_kembali_rencana->format('d M Y');
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Barang sudah dipinjam pada tanggal {$tglExisting}. Silakan pilih tanggal lain.");
        }

        // Buat peminjaman baru dengan status 'menunggu'
        Peminjaman::create([
            'user_id' => auth()->id(),
            'sarpras_id' => $validated['sarpras_id'],
            'petugas_id' => null,
            'jumlah_pinjam' => $validated['jumlah_pinjam'],
            'tgl_pinjam' => $validated['tgl_pinjam'],
            'tgl_kembali_rencana' => $validated['tgl_kembali_rencana'],
            'status' => 'menunggu',
            'qr_code' => null,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('peminjaman.index')
            ->with('success', 'Pengajuan peminjaman berhasil dibuat. Silakan tunggu persetujuan petugas.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Peminjaman $peminjaman): View
    {
        $user = auth()->user();

        // Peminjam hanya bisa lihat data miliknya
        if ($user->role === 'peminjam' && $peminjaman->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $peminjaman->load(['user', 'sarpras.kategori', 'petugas', 'pengembalian']);

        return view('peminjaman.show', compact('peminjaman'));
    }

    /**
     * Show the form for editing the specified resource.
     * Untuk Admin/Petugas: mengubah status peminjaman
     */
    public function edit(Peminjaman $peminjaman): View
    {
        $user = auth()->user();

        // Hanya admin dan petugas yang bisa edit
        if ($user->role === 'peminjam') {
            abort(403, 'Anda tidak memiliki akses untuk mengedit peminjaman.');
        }

        $peminjaman->load(['user', 'sarpras', 'petugas']);

        return view('peminjaman.edit', compact('peminjaman'));
    }

    /**
     * Update the specified resource in storage.
     * Logika untuk Admin/Petugas mengubah status peminjaman
     * 
     * PENTING: 
     * - Jika status berubah ke 'disetujui' → kurangi stok
     * - Jika status berubah ke 'selesai' → kembalikan stok
     * - Jika status berubah ke 'ditolak' → tidak ada perubahan stok
     */
    public function update(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        $user = auth()->user();

        // Hanya admin dan petugas yang bisa update
        if ($user->role === 'peminjam') {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status peminjaman.');
        }

        $validated = $request->validate([
            'status' => 'required|in:menunggu,disetujui,selesai,ditolak',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        $oldStatus = $peminjaman->status;
        $newStatus = $validated['status'];

        // Jika status tidak berubah, langsung update dan return
        if ($oldStatus === $newStatus) {
            $peminjaman->update([
                'keterangan' => $validated['keterangan'] ?? $peminjaman->keterangan,
            ]);

            return redirect()
                ->route('peminjaman.index')
                ->with('success', 'Data peminjaman berhasil diperbarui.');
        }

        // Gunakan database transaction untuk keamanan
        DB::beginTransaction();

        try {
            $sarpras = $peminjaman->sarpras;

            // LOGIKA PENGURANGAN/PENGEMBALIAN STOK
            
            // Case 1: Status berubah ke 'disetujui' (dari 'menunggu')
            if ($newStatus === 'disetujui' && $oldStatus === 'menunggu') {
                // Validasi stok tersedia
                if ($sarpras->stok < $peminjaman->jumlah_pinjam) {
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with('error', "Stok tidak mencukupi. Stok tersedia: {$sarpras->stok}, Jumlah pinjam: {$peminjaman->jumlah_pinjam}");
                }

                // Kurangi stok
                $sarpras->decrement('stok', $peminjaman->jumlah_pinjam);

                // Generate QR Code (simple unique string)
                $peminjaman->qr_code = 'PJM-' . strtoupper(Str::random(8)) . '-' . $peminjaman->id;
            }

            // Case 2: Status berubah ke 'selesai' (dari 'disetujui')
            // Ini berarti barang sudah dikembalikan
            if ($newStatus === 'selesai' && $oldStatus === 'disetujui') {
                // Kembalikan stok
                $sarpras->increment('stok', $peminjaman->jumlah_pinjam);
            }

            // Case 3: Status berubah ke 'ditolak' (dari 'menunggu')
            // Tidak ada perubahan stok karena belum dipinjam
            
            // Case 4: Rollback dari 'disetujui' ke 'menunggu' (jarang terjadi, tapi handle)
            if ($newStatus === 'menunggu' && $oldStatus === 'disetujui') {
                // Kembalikan stok karena approval dibatalkan
                $sarpras->increment('stok', $peminjaman->jumlah_pinjam);

                // Hapus QR code
                $peminjaman->qr_code = null;
            }

            // Update peminjaman
            $peminjaman->update([
                'status' => $newStatus,
                'petugas_id' => $user->id,
                'keterangan' => $validated['keterangan'] ?? $peminjaman->keterangan,
                'qr_code' => $peminjaman->qr_code,
            ]);

            DB::commit();

            $statusMessages = [
                'disetujui' => 'Peminjaman berhasil disetujui. Stok telah dikurangi.',
                'ditolak' => 'Peminjaman telah ditolak.',
                'selesai' => 'Peminjaman telah selesai. Stok telah dikembalikan.',
                'menunggu' => 'Status peminjaman dikembalikan ke menunggu.',
            ];

            return redirect()
                ->route('peminjaman.index')
                ->with('success', $statusMessages[$newStatus] ?? 'Status peminjaman berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Peminjaman $peminjaman): RedirectResponse
    {
        $user = auth()->user();

        // Hanya admin yang bisa hapus
        if ($user->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menghapus data peminjaman.');
        }

        // Tidak bisa hapus peminjaman yang sedang aktif (disetujui)
        if ($peminjaman->status === 'disetujui') {
            return redirect()
                ->route('peminjaman.index')
                ->with('error', 'Tidak dapat menghapus peminjaman yang sedang aktif. Selesaikan peminjaman terlebih dahulu.');
        }

        $peminjaman->delete();

        return redirect()
            ->route('peminjaman.index')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }

    /**
     * Generate PDF bukti pinjam dengan QR Code
     */
    public function cetak(Peminjaman $peminjaman)
    {
        // Hanya bisa cetak jika status disetujui atau selesai
        if (!in_array($peminjaman->status, ['disetujui', 'selesai'])) {
            return redirect()
                ->back()
                ->with('error', 'Bukti pinjam hanya tersedia untuk peminjaman yang sudah disetujui.');
        }

        $peminjaman->load(['user', 'sarpras.kategori', 'petugas']);

        // Generate QR Code sebagai base64 SVG
        $qrCode = base64_encode(QrCode::format('svg')
            ->size(150)
            ->errorCorrection('M')
            ->generate($peminjaman->qr_code ?? 'PJM-' . $peminjaman->id));

        $pdf = Pdf::loadView('peminjaman.cetak', [
            'peminjaman' => $peminjaman,
            'qrCode' => $qrCode,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'Bukti-Pinjam-' . ($peminjaman->qr_code ?? 'PJM-' . $peminjaman->id) . '.pdf';

        return $pdf->download($filename);
    }
}

