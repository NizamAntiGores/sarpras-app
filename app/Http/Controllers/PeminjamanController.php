<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\Sarpras;
use App\Models\SarprasUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     * - Jika Peminjam: hanya data dia sendiri
     * - Jika Admin/Petugas: semua data
     * - Support filter: status, tanggal_mulai, tanggal_selesai
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $query = Peminjaman::with(['user', 'details.sarprasUnit.sarpras', 'petugas', 'pengembalian'])
            ->orderBy('tgl_pinjam', 'desc')
            ->orderBy('created_at', 'desc');

        // Jika user adalah peminjam, hanya tampilkan data miliknya
        if ($user->isPeminjam()) {
            $query->where('user_id', $user->id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range (berdasarkan tanggal_pinjam)
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tgl_pinjam', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tgl_pinjam', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tgl_pinjam', '<=', $request->tanggal_selesai);
        }

        // Search logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Support pencarian ID secara langsung
                $cleanSearch = ltrim($search, '#');

                // Jika search diawali "QR-", fokus cari di kolom qr_code
                if (str_starts_with(strtoupper($search), 'QR-')) {
                    $q->where('qr_code', 'like', "%{$search}%");
                } elseif (is_numeric($cleanSearch)) {
                    $q->where('id', $cleanSearch)
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%");
                        });
                } else {
                    $q->whereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    })
                        ->orWhereHas('details.sarprasUnit.sarpras', function ($s) use ($search) {
                            $s->where('nama_barang', 'like', "%{$search}%");
                        })
                        ->orWhereHas('details.sarprasUnit', function ($su) use ($search) {
                            $su->where('kode_unit', 'like', "%{$search}%");
                        })
                        ->orWhere('qr_code', 'like', "%{$search}%"); // Fallback search qr_code
                }
            });
        }

        $peminjaman = $query->paginate(10)->withQueryString();

        // Data untuk filter
        $filters = [
            'status' => $request->status,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ];

        return view('peminjaman.index', compact('peminjaman', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $selectedSarprasId = $request->query('sarpras_id');

        // Ambil semua sarpras yang memiliki unit tersedia
        // Filter: Hanya Guru yang bisa melihat barang 'bahan' (sekali pakai)
        $user = auth()->user();
        $sarprasList = Sarpras::withCount([
            'units as available_units_count' => function ($query) {
                $query->bisaDipinjam();
            },
        ])
            ->when(!$user->isGuru(), function ($query) {
                $query->where('tipe', '!=', 'bahan');
            })
            ->having('available_units_count', '>', 0)
            ->orderBy('nama_barang')
            ->get();

        // Ambil ID unit yang sedang dalam pengajuan 'menunggu'
        $pendingUnitIds = PeminjamanDetail::whereHas('peminjaman', function ($q) {
            $q->where('status', 'menunggu');
        })->pluck('sarpras_unit_id');

        // Ambil semua unit yang bisa dipinjam, grouped by sarpras
        // Exclude unit yang status fisiknya tidak tersedia ATAU sedang dalam pengajuan menunggu
        $availableUnits = SarprasUnit::with('sarpras', 'lokasi')
            ->bisaDipinjam()
            ->whereNotIn('id', $pendingUnitIds)
            ->orderBy('kode_unit')
            ->get()
            ->groupBy('sarpras_id');

        return view('peminjaman.create', compact('sarprasList', 'availableUnits', 'selectedSarprasId'));
    }

    /**
     * Store a newly created resource in storage.
     * Logika untuk Siswa/Peminjam membuat pengajuan peminjaman baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'unit_ids' => 'required|array|min:1',
            'unit_ids.*' => 'exists:sarpras_units,id',
            'tgl_pinjam' => 'required|date|after_or_equal:today',
            'tgl_kembali_rencana' => 'required|date|after:tgl_pinjam',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'unit_ids.required' => 'Pilih minimal 1 unit barang.',
            'unit_ids.min' => 'Pilih minimal 1 unit barang.',
            'tgl_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tgl_pinjam.after_or_equal' => 'Tanggal pinjam tidak boleh sebelum hari ini.',
            'tgl_kembali_rencana.required' => 'Tanggal kembali rencana wajib diisi.',
            'tgl_kembali_rencana.after' => 'Tanggal kembali harus setelah tanggal pinjam.',
        ]);

        // Custom Validation: Batas Peminjaman Maksimal 7 Hari
        $tglPinjam = \Carbon\Carbon::parse($validated['tgl_pinjam']);
        $tglKembali = \Carbon\Carbon::parse($validated['tgl_kembali_rencana']);

        if ($tglPinjam->diffInDays($tglKembali) > 7) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Peminjaman tidak boleh lebih dari 7 hari. Silakan buat peminjaman baru jika diperlukan.');
        }

        DB::beginTransaction();

        try {
            // Validasi setiap unit yang dipilih
            $units = SarprasUnit::whereIn('id', $validated['unit_ids'])->get();

            foreach ($units as $unit) {
                // Cek apakah unit bisa dipinjam
                if (!$unit->canBeBorrowed()) {
                    DB::rollBack();

                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', "Unit {$unit->kode_unit} tidak tersedia untuk dipinjam.");
                }

                // Cek validasi Guru dan Bahan
                // Hanya Guru yang boleh meminjam barang tipe 'bahan'
                $user = auth()->user();
                if ($unit->isBahan() && !$user->isGuru()) {
                    DB::rollBack();

                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', "Unit {$unit->kode_unit} adalah barang sekali pakai (bahan). Hanya Guru yang dapat meminjamnya.");
                }

                // Cek double booking - apakah unit sudah ada di peminjaman aktif
                $existingBooking = PeminjamanDetail::whereHas('peminjaman', function ($query) use ($validated) {
                    $query->whereIn('status', ['menunggu', 'disetujui'])
                        ->where(function ($q) use ($validated) {
                            $tglPinjam = $validated['tgl_pinjam'];
                            $tglKembali = $validated['tgl_kembali_rencana'];

                            // Overlap check
                            $q->where(function ($inner) use ($tglPinjam, $tglKembali) {
                                $inner->where('tgl_pinjam', '<=', $tglKembali)
                                    ->where('tgl_kembali_rencana', '>=', $tglPinjam);
                            });
                        });
                })
                    ->where('sarpras_unit_id', $unit->id)
                    ->first();

                if ($existingBooking) {
                    DB::rollBack();

                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', "Unit {$unit->kode_unit} sudah dipinjam pada tanggal tersebut.");
                }
            }

            // Buat peminjaman baru
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'petugas_id' => null,
                'tgl_pinjam' => $validated['tgl_pinjam'],
                'tgl_kembali_rencana' => $validated['tgl_kembali_rencana'],
                'status' => Peminjaman::STATUS_MENUNGGU,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Buat detail peminjaman untuk setiap unit
            foreach ($validated['unit_ids'] as $unitId) {
                PeminjamanDetail::create([
                    'peminjaman_id' => $peminjaman->id,
                    'sarpras_unit_id' => $unitId,
                ]);
            }

            \App\Helpers\LogHelper::record('create', "Membuat pengajuan peminjaman baru (ID: {$peminjaman->id}) untuk " . count($validated['unit_ids']) . ' unit.');

            DB::commit();

            $jumlahUnit = count($validated['unit_ids']);

            return redirect()
                ->route('peminjaman.index')
                ->with('success', "Pengajuan peminjaman {$jumlahUnit} unit berhasil dibuat. Silakan tunggu persetujuan petugas.");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Peminjaman $peminjaman): View
    {
        $user = auth()->user();

        // Peminjam hanya bisa lihat data miliknya
        if ($user->isPeminjam() && $peminjaman->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $peminjaman->load([
            'user',
            'details.sarprasUnit.sarpras.kategori',
            'details.sarprasUnit.lokasi',
            'petugas',
            'pengembalian.details.sarprasUnit',
        ]);

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
        if ($user->isPeminjam()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit peminjaman.');
        }

        $peminjaman->load([
            'user',
            'details.sarprasUnit.sarpras',
            'petugas',
        ]);

        return view('peminjaman.edit', compact('peminjaman'));
    }

    /**
     * Update the specified resource in storage.
     * Logika untuk Admin/Petugas mengubah status peminjaman
     *
     * PENTING:
     * - Jika status berubah ke 'disetujui' → update status unit menjadi 'dipinjam'
     * - Jika status berubah ke 'selesai' → handled by PengembalianController
     * - Jika status berubah ke 'ditolak' → tidak ada perubahan unit
     */
    public function update(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        $user = auth()->user();

        // Hanya admin dan petugas yang bisa update
        if ($user->isPeminjam()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status peminjaman.');
        }

        $validated = $request->validate([
            'status' => 'required|in:menunggu,disetujui,selesai,ditolak',
            'catatan_petugas' => 'required_if:status,ditolak|nullable|string|max:500',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'catatan_petugas.required_if' => 'Catatan/alasan penolakan wajib diisi jika menolak peminjaman.',
        ]);

        $oldStatus = $peminjaman->status;
        $newStatus = $validated['status'];

        // Jika status tidak berubah, langsung update dan return
        if ($oldStatus === $newStatus) {
            $peminjaman->update([
                'catatan_petugas' => $validated['catatan_petugas'] ?? $peminjaman->catatan_petugas,
            ]);

            $queryParams = $request->only(['status', 'page', 'tanggal_mulai', 'tanggal_selesai', 'search']);

            return redirect()
                ->route('peminjaman.index', $queryParams)
                ->with('success', 'Data peminjaman berhasil diperbarui.');
        }

        // Gunakan database transaction untuk keamanan
        DB::beginTransaction();

        try {
            // LOGIKA PERUBAHAN STATUS UNIT

            // Case 1: Status berubah ke 'disetujui' (dari 'menunggu')
            if ($newStatus === 'disetujui' && $oldStatus === 'menunggu') {
                // Validasi semua unit masih tersedia
                foreach ($peminjaman->details as $detail) {
                    $unit = $detail->sarprasUnit;

                    // Cek ketersediaan standard
                    if ($unit->canBeBorrowed()) {
                        continue;
                    }

                    // Logic Khusus Perpanjangan (Extension):
                    // Jika unit statusnya 'dipinjam' tapi peminjaman ini yang sedang memegangnya (atau status menggantung dari peminjaman ini)
                    // Maka boleh disetujui kembali.

                    // Syarat:
                    // 1. Status 'dipinjam'
                    // 2. Kondisi tidak rusak berat
                    // 3. TIDAK ADA peminjaman LAIN yang sedang 'disetujui' untuk unit ini.

                    $isDipinjam = $unit->status === SarprasUnit::STATUS_DIPINJAM;
                    $isLayak = $unit->kondisi !== SarprasUnit::KONDISI_RUSAK_BERAT;

                    if ($isDipinjam && $isLayak) {
                        // Cek apakah ada peminjaman 'disetujui' LAIN yang menggunakan unit ini
                        $otherActiveLoan = Peminjaman::where('status', 'disetujui')
                            ->where('id', '!=', $peminjaman->id) // Bukan peminjaman ini
                            ->whereHas('details', function ($q) use ($unit) {
                                $q->where('sarpras_unit_id', $unit->id);
                            })
                            ->exists();

                        if (!$otherActiveLoan) {
                            // Aman, ini adalah perpanjangan untuk barang yang memang sedang dibawa dia sendiri
                            continue;
                        }
                    }

                    // Jika sampai sini, berarti benar-benar tidak tersedia
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with('error', "Unit {$unit->kode_unit} tidak tersedia (Status: {$unit->status}).");
                }

                $allBahan = true;

                // Update status unit
                foreach ($peminjaman->details as $detail) {
                    $unit = $detail->sarprasUnit;

                    if ($unit->isBahan()) {
                        // Jika tipe 'bahan', maka status jadi TERPAKAI (Consumed)
                        // Barang hilang dari stok, tidak muncul di Restore, tidak perlu dikembalikan
                        $unit->update([
                            'status' => SarprasUnit::STATUS_TERPAKAI,
                            'kondisi' => 'baik',
                        ]);
                    } else {
                        // Jika tipe 'asset', status jadi 'dipinjam' dan HARUS dikembalikan
                        $unit->update(['status' => SarprasUnit::STATUS_DIPINJAM]);
                        $allBahan = false;
                    }
                }

                // Jika SEMUA barang adalah 'bahan' (habis pakai), maka transaksi langsung SELESAI
                // Tidak perlu menunggu pengembalian
                if ($allBahan) {
                    $newStatus = 'selesai';
                    // Auto-create record pengembalian (formalitas sistem)
                    $pengembalian = \App\Models\Pengembalian::create([
                        'peminjaman_id' => $peminjaman->id,
                        'petugas_id' => $user->id,
                        'tgl_kembali_aktual' => now(),
                    ]);

                    foreach ($peminjaman->details as $detail) {
                        \App\Models\PengembalianDetail::create([
                            'pengembalian_id' => $pengembalian->id,
                            'sarpras_unit_id' => $detail->sarpras_unit_id,
                            'kondisi_akhir' => 'baik',
                            'denda' => 0,
                        ]);
                    }
                }

                // Generate QR code (tapi jika selesai langsung, QR mungkin tidak terlalu krusial, tapi tetap generate untuk history)
                $peminjaman->qr_code = 'QR-' . strtoupper(Str::random(10));
            }

            // Case 2: Status berubah ke 'ditolak' (dari 'menunggu')
            // Tidak ada perubahan unit karena belum dipinjam

            // Case 3: Rollback dari 'disetujui' ke 'menunggu' (jarang terjadi)
            if ($newStatus === 'menunggu' && $oldStatus === 'disetujui') {
                // Kembalikan status unit ke 'tersedia'
                foreach ($peminjaman->details as $detail) {
                    $detail->sarprasUnit->update(['status' => SarprasUnit::STATUS_TERSEDIA]);
                }
                $peminjaman->qr_code = null;
            }

            // Update peminjaman
            $peminjaman->update([
                'status' => $newStatus,
                'petugas_id' => $user->id,
                'catatan_petugas' => $validated['catatan_petugas'] ?? $peminjaman->catatan_petugas,
                'qr_code' => $peminjaman->qr_code,
            ]);

            \App\Helpers\LogHelper::record('update', "Mengubah status peminjaman (ID: {$peminjaman->id}) menjadi: {$newStatus} oleh " . auth()->user()->name);

            // Send notification to user
            if ($newStatus === 'disetujui') {
                \App\Models\Notification::send(
                    $peminjaman->user_id,
                    \App\Models\Notification::TYPE_PEMINJAMAN_APPROVED,
                    'Peminjaman Disetujui ✅',
                    'Peminjaman Anda telah disetujui oleh ' . auth()->user()->name . '. Silakan ambil barang sesuai jadwal.',
                    route('peminjaman.show', $peminjaman)
                );
            } elseif ($newStatus === 'ditolak') {
                \App\Models\Notification::send(
                    $peminjaman->user_id,
                    \App\Models\Notification::TYPE_PEMINJAMAN_REJECTED,
                    'Peminjaman Ditolak ❌',
                    'Peminjaman Anda ditolak. Alasan: ' . ($validated['catatan_petugas'] ?? 'Tidak ada keterangan'),
                    route('peminjaman.show', $peminjaman)
                );
            }

            DB::commit();

            $statusMessages = [
                'disetujui' => 'Peminjaman berhasil disetujui. Status unit telah diperbarui.',
                'ditolak' => 'Peminjaman telah ditolak.',
                'selesai' => 'Peminjaman telah selesai.',
                'menunggu' => 'Status peminjaman dikembalikan ke menunggu.',
            ];

            $queryParams = $request->only(['page', 'tanggal_mulai', 'tanggal_selesai', 'search']);
            if ($request->has('redirect_status') && $request->redirect_status != null) {
                // Kembalikan filter 'status' seperti semula
                $queryParams['status'] = $request->redirect_status;
            }

            return redirect()
                ->route('peminjaman.index', $queryParams)
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

        \App\Helpers\LogHelper::record('delete', "Menghapus data peminjaman (ID: {$peminjaman->id})");

        return redirect()
            ->route('peminjaman.index')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }
}
