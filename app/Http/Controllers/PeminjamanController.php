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
        // Ambil semua sarpras
        // Filter: Hanya Guru yang bisa melihat barang 'bahan' (sekali pakai)
        $user = auth()->user();
        $sarprasList = Sarpras::with(['kategori'])
            ->when(!$user->isGuru(), function ($query) {
                $query->where('tipe', '!=', 'bahan');
            })
            ->orderBy('nama_barang')
            ->get()
            ->filter(function ($sarpras) {
                // Filter barang yang stoknya > 0
                return $sarpras->stok_tersedia > 0;
            });

        // Ambil ID unit yang sedang dalam pengajuan 'menunggu'
        $pendingUnitIds = PeminjamanDetail::whereHas('peminjaman', function ($q) {
            $q->where('status', 'menunggu');
        })->pluck('sarpras_unit_id');

        // Ambil semua unit yang bisa dipinjam, grouped by sarpras
        // Exclude unit yang status fisiknya tidak tersedia, sedang dalam pengajuan menunggu, atau TIDAK di Storefront
        $availableUnits = SarprasUnit::with('sarpras', 'lokasi')
            ->bisaDipinjam()
            ->whereHas('lokasi', function ($q) {
                $q->where('is_storefront', true);
            })
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
    /**
     * Store a newly created resource in storage.
     * Logika untuk Siswa/Peminjam membuat pengajuan peminjaman baru
     * Mendukung Hybrid Inventory:
     * - Assets (Unit Based): via 'unit_ids'
     * - Consumables (Quantity Based): via 'consumables' array
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Input untuk Assets (Array of Unit IDs)
            'unit_ids' => 'nullable|array',
            'unit_ids.*' => 'exists:sarpras_units,id',

            // Input untuk Consumables (Array of {id, qty})
            'consumables' => 'nullable|array',
            'consumables.*.item_id' => 'exists:sarpras,id',
            'consumables.*.qty' => 'integer|min:1',

            'tgl_pinjam' => 'required|date|after_or_equal:today',
            'tgl_kembali_rencana' => 'required|date|after:tgl_pinjam',
            'keterangan' => 'required|string|max:500',
        ]);

        if (empty($validated['unit_ids']) && empty($validated['consumables'])) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu barang (Asset atau Bahan).');
        }

        // Custom Validation: Batas Peminjaman Maksimal 7 Hari
        $tglPinjam = \Carbon\Carbon::parse($validated['tgl_pinjam']);
        $tglKembali = \Carbon\Carbon::parse($validated['tgl_kembali_rencana']);

        if ($tglPinjam->diffInDays($tglKembali) > 7) {
            return redirect()->back()->withInput()->with('error', 'Peminjaman tidak boleh lebih dari 7 hari.');
        }

        DB::beginTransaction();

        try {
            // Create Transaction Header first
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tgl_pinjam' => $validated['tgl_pinjam'],
                'tgl_kembali_rencana' => $validated['tgl_kembali_rencana'],
                'status' => Peminjaman::STATUS_MENUNGGU,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            $totalItems = 0;

            // 1. PROCESS ASSETS (Unit Based)
            if (!empty($validated['unit_ids'])) {
                // Lock & Check Each Unit
                foreach ($validated['unit_ids'] as $unitId) {
                    $unit = SarprasUnit::with('sarpras', 'lokasi')->lockForUpdate()->find($unitId); // Lock row

                    // Validation A: Existence
                    if (!$unit)
                        throw new \Exception("Unit tidak ditemukan.");

                    // Validation B: Status & Condition
                    if (!$unit->canBeBorrowed()) {
                        throw new \Exception("Unit {$unit->kode_unit} ({$unit->sarpras->nama_barang}) tidak tersedia (Status: {$unit->status}).");
                    }

                    // Validation C: Location (Must be in a Storefront)
                    if (!$unit->lokasi || !$unit->lokasi->is_storefront) {
                        throw new \Exception("Unit {$unit->kode_unit} tidak berada di lokasi peminjaman (Storefront).");
                    }

                    // Validation D: Double Booking Check (Range Overlap)
                    // Cek peminjaman lain yang statusnya 'menunggu' atau 'disetujui'
                    // Yang memiliki irisan tanggal
                    $isBooked = PeminjamanDetail::where('sarpras_unit_id', $unit->id)
                        ->whereHas('peminjaman', function ($q) use ($tglPinjam, $tglKembali) {
                            $q->whereIn('status', ['menunggu', 'disetujui'])
                                ->where(function ($sub) use ($tglPinjam, $tglKembali) {
                                    // Logic Overlap: StartA <= EndB AND EndA >= StartB
                                    $sub->where('tgl_pinjam', '<=', $tglKembali)
                                        ->where('tgl_kembali_rencana', '>=', $tglPinjam);
                                });
                        })
                        ->exists();

                    if ($isBooked) {
                        throw new \Exception("Unit {$unit->kode_unit} sudah dibooking pada tanggal tersebut.");
                    }

                    // Insert Detail
                    PeminjamanDetail::create([
                        'peminjaman_id' => $peminjaman->id,
                        'sarpras_unit_id' => $unit->id,
                        'sarpras_id' => $unit->sarpras_id, // Redundant but good for analytics
                        'quantity' => 1
                    ]);
                    $totalItems++;
                }
            }

            // 2. PROCESS CONSUMABLES (Quantity Based)
            if (!empty($validated['consumables'])) {
                foreach ($validated['consumables'] as $itemReq) {
                    $itemId = $itemReq['item_id'];
                    $reqQty = $itemReq['qty'];

                    // Lock Stocks for this Item in ALL Storefront Locs
                    $stocks = \App\Models\ItemStock::where('sarpras_id', $itemId)
                        ->whereHas('lokasi', function ($q) {
                            $q->where('is_storefront', true);
                        })
                        ->lockForUpdate() // Lock these rows
                        ->get();

                    $totalPhysicalQty = $stocks->sum('quantity');
                    $sarpras = \App\Models\Sarpras::find($itemId);
                    $itemName = $sarpras ? $sarpras->nama_barang : "Item #{$itemId}";

                    // Calculate Global Pending Qty
                    $pendingQty = PeminjamanDetail::where('sarpras_id', $itemId)
                        ->whereHas('peminjaman', function ($q) {
                            $q->where('status', 'menunggu');
                        })
                        ->sum('quantity');

                    $available = $totalPhysicalQty - $pendingQty;

                    if ($available < $reqQty) {
                        throw new \Exception("Stok {$itemName} tidak mencukupi di seluruh lokasi storefront. Total Tersedia: {$available}, Diminta: {$reqQty}.");
                    }

                    // Insert Detail 
                    // Note: We do not deduct physical stock yet (wait for approval)
                    // We also don't assign a specific source location yet.
                    PeminjamanDetail::create([
                        'peminjaman_id' => $peminjaman->id,
                        'sarpras_unit_id' => null,
                        'sarpras_id' => $itemId,
                        'quantity' => $reqQty
                    ]);
                    $totalItems++;
                }
            }

            \App\Helpers\LogHelper::record('create', "Membuat pengajuan peminjaman (ID: {$peminjaman->id})");

            DB::commit();

            return redirect()
                ->route('peminjaman.index')
                ->with('success', "Pengajuan berhasil dibuat. Menunggu persetujuan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memproses peminjaman: ' . $e->getMessage());
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
                $allBahan = true;

                foreach ($peminjaman->details as $detail) {
                    // --- HANDLING ASSET (UNIT) ---
                    if ($detail->sarpras_unit_id) {
                        $allBahan = false;
                        $unit = $detail->sarprasUnit;

                        // Cek ketersediaan standard
                        if ($unit->canBeBorrowed()) {
                            // Update status jadi 'dipinjam'
                            $unit->update(['status' => SarprasUnit::STATUS_DIPINJAM]);
                            continue;
                        }

                        // Logic Khusus Perpanjangan (Extension)
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
                                // Aman, Extension
                                continue;
                            }
                        }

                        // Jika sampai sini, berarti tidak tersedia
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with('error', "Unit {$unit->kode_unit} tidak tersedia (Status: {$unit->status}).");
                    }

                    // --- HANDLING CONSUMABLE (BAHAN) ---
                    else {
                        // Logic Deduct Stock for Consumable
                        // Find a storefront location with enough stock
                        $reqQty = $detail->quantity;
                        $itemId = $detail->sarpras_id;

                        // Cari stok di lokasi storefront (Urutkan dari stok terbanyak supaya probability sukses tinggi)
                        $stock = \App\Models\ItemStock::where('sarpras_id', $itemId)
                            ->whereHas('lokasi', function ($q) {
                                $q->where('is_storefront', true); })
                            ->orderBy('quantity', 'desc')
                            ->lockForUpdate()
                            ->first();

                        if (!$stock || $stock->quantity < $reqQty) {
                            $sarprasName = $detail->sarpras ? $detail->sarpras->nama_barang : 'Item';
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with('error', "Stok {$sarprasName} tidak mencukupi untuk disetujui (Butuh: {$reqQty}).");
                        }

                        // Deduct Stock
                        $stock->decrement('quantity', $reqQty);

                        // Kita tidak mengubah status unit karena tidak ada unit ID for consumable
                        // Tapi kita bisa anggap ini 'Done' process for this detail
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
                        // Only add details if they have unit_id? 
                        // Or add dummy logic for consistency?
                        // For consumables, pengembalian detail usually not relevant unless tracking waste.
                        // Let's skip creating PengembalianDetail for consumables as they are gone.
                        if ($detail->sarpras_unit_id) {
                            \App\Models\PengembalianDetail::create([
                                'pengembalian_id' => $pengembalian->id,
                                'sarpras_unit_id' => $detail->sarpras_unit_id,
                                'kondisi_akhir' => 'baik',
                                'denda' => 0,
                            ]);
                        }
                    }
                }

                // Generate QR code
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

    /**
     * Show the handover (serah terima) page.
     * Petugas akan menyerahkan barang kepada peminjam.
     */
    public function handover(Peminjaman $peminjaman): View|RedirectResponse
    {
        $user = auth()->user();

        // Hanya admin dan petugas yang bisa melakukan handover
        if ($user->isPeminjam()) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan serah terima.');
        }

        // Pastikan status 'disetujui' dan belum diambil
        if (!$peminjaman->isReadyForPickup()) {
            return redirect()->route('peminjaman.show', $peminjaman)
                ->with('error', 'Peminjaman ini tidak dalam status siap untuk diambil.');
        }

        $peminjaman->load([
            'user',
            'details.sarprasUnit.sarpras.kategori',
            'details.sarprasUnit.lokasi',
            'petugas',
        ]);

        return view('peminjaman.handover', compact('peminjaman'));
    }

    /**
     * Process the handover (serah terima) - record that items have been handed to borrower.
     */
    public function processHandover(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        $user = auth()->user();

        // Hanya admin dan petugas
        if ($user->isPeminjam()) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan serah terima.');
        }

        // Pastikan status 'disetujui' dan belum diambil
        if (!$peminjaman->isReadyForPickup()) {
            return redirect()->route('peminjaman.show', $peminjaman)
                ->with('error', 'Peminjaman ini tidak dalam status siap untuk diambil.');
        }

        // Update handover info
        $peminjaman->update([
            'handover_at' => now(),
            'handover_by' => $user->id,
        ]);

        \App\Helpers\LogHelper::record('update', "Serah terima peminjaman (ID: {$peminjaman->id}) kepada {$peminjaman->user->name}");

        // Kirim notifikasi ke peminjam
        \App\Models\Notification::send(
            $peminjaman->user_id,
            \App\Models\Notification::TYPE_PEMINJAMAN_APPROVED,
            'Barang Telah Diserahkan 📦',
            'Barang peminjaman telah diserahkan kepada Anda oleh ' . $user->name . '. Jangan lupa kembalikan tepat waktu!',
            route('peminjaman.show', $peminjaman)
        );

        return redirect()
            ->route('peminjaman.show', $peminjaman)
            ->with('success', 'Serah terima berhasil! Barang telah diserahkan kepada ' . $peminjaman->user->name . '.');
    }
}

