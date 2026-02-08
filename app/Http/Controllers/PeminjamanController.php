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

        // Filter: Hanya Guru yang bisa melihat barang 'bahan' (sekali pakai)
        // Ambil SEMUA barang yang punya stok di lokasi storefront (MANAPUN)
        $user = auth()->user();
        
        $sarprasList = Sarpras::with(['kategori'])
            ->when(!$user->isGuru(), function ($query) {
                $query->where('tipe', '!=', 'bahan');
            })
            // Filter: Must have stock/units in ANY storefront location
            ->where(function($query) use ($user) {
                // 1. Check ASSETS (Unit Based) in Storefronts
                 $query->whereHas('units', function($q) {
                    $q->aktif()->whereHas('lokasi', fn($l) => $l->where('is_storefront', true));
                })
                // 2. Check CONSUMABLES (Quantity Based) in Storefronts
                ->orWhere(function($sub) use ($user) {
                     if ($user->isGuru()) {
                         $sub->where('tipe', 'bahan')
                               ->whereHas('stocks', function($q) {
                                   $q->where('quantity', '>', 0)
                                     ->whereHas('lokasi', fn($l) => $l->where('is_storefront', true));
                               });
                     }
                });
            })
            ->orderBy('nama_barang')
            ->get()
            // Post-query filtering to ensure availability count > 0 after excluding pending items
            ->filter(function ($sarpras) {
                // Reuse existing accessor logic but make sure we only count STOREFRONT stock
                // The accessor `stok_tersedia` already handles logic for "Storefront Only" 
                // as per previous conversation? checking model...
                // Yes, I updated Sarpras::stokTersedia to filter by is_storefront=true.
                return $sarpras->stok_tersedia > 0;
            });


        // Ambil ID unit yang sedang dalam pengajuan 'menunggu'
        $pendingUnitIds = PeminjamanDetail::whereHas('peminjaman', function ($q) {
            $q->where('status', 'menunggu');
        })->pluck('sarpras_unit_id');

        // Ambil semua unit yang bisa dipinjam, grouped by sarpras
        // Exclude unit yang status fisiknya tidak tersedia, sedang dalam pengajuan menunggu
        // Must be in a Storefront location
        $availableUnits = SarprasUnit::with('sarpras', 'lokasi')
            ->bisaDipinjam()
            ->whereHas('lokasi', function($q) {
                $q->where('is_storefront', true);
            })
            ->whereNotIn('id', $pendingUnitIds)
            ->orderBy('kode_unit')
            ->get()
            ->groupBy('sarpras_id');
            
        // Calculate consumable stocks per location for detail view?
        // Let's pass the stock data for consumables to the view
        // Need to know WHICH locations catch have stock
        foreach ($sarprasList as $item) {
            if ($item->tipe == 'bahan') {
                $item->stock_details = $item->stocks()
                    ->where('quantity', '>', 0)
                    ->whereHas('lokasi', fn($l) => $l->where('is_storefront', true))
                    ->with('lokasi')
                    ->get();
            }
        }

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
        // Fix: Re-index 'consumables' array to standard numerical index AND cast qty to integer
        // This prevents validation issues with associative keys (e.g. '12_11') and treats "30.0" as 30
        if ($request->has('consumables') && is_array($request->consumables)) {
            $cleaned = [];
            foreach ($request->consumables as $item) {
                // Ensure item is array and has qty
                if (is_array($item) && isset($item['qty'])) {
                    $item['qty'] = (int) $item['qty']; // Force Check: Cast to integer
                    $cleaned[] = $item;
                }
            }
            $request->merge(['consumables' => $cleaned]);
        }

        // Check if this is a consumable-only request
        $isConsumableOnly = empty($request->unit_ids) && !empty($request->consumables);

        $rules = [
            // Input untuk Assets (Array of Unit IDs)
            'unit_ids' => 'nullable|array',
            'unit_ids.*' => 'exists:sarpras_units,id',

            // Input untuk Consumables (Array of {id, qty})
            'consumables' => 'nullable|array',
            'consumables.*.item_id' => 'exists:sarpras,id',
            'consumables.*.qty' => 'integer|min:1',

            'tgl_pinjam' => 'required|date|after_or_equal:today',
            'keterangan' => 'required|string|max:500',
        ];

        // Conditional Validation: Return Date only required if borrowing Assets (Units)
        if (!$isConsumableOnly) {
            $rules['tgl_kembali_rencana'] = 'required|date|after:tgl_pinjam';
        } else {
            $rules['tgl_kembali_rencana'] = 'nullable|date';
        }

        $validated = $request->validate($rules);

        if (empty($request->unit_ids) && empty($request->consumables)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu barang (Asset atau Bahan).');
        }

        // Auto-fill tgl_kembali_rencana for consumables if not provided
        if ($isConsumableOnly && empty($validated['tgl_kembali_rencana'])) {
            $validated['tgl_kembali_rencana'] = $validated['tgl_pinjam'];
        }

        // Custom Validation: Batas Peminjaman Maksimal 7 Hari (Only for Assets)
        $tglPinjam = \Carbon\Carbon::parse($validated['tgl_pinjam']);
        $tglKembali = \Carbon\Carbon::parse($validated['tgl_kembali_rencana']);

        if (!$isConsumableOnly && $tglPinjam->diffInDays($tglKembali) > 7) {
            return redirect()->back()->withInput()->with('error', 'Peminjaman aset tidak boleh lebih dari 7 hari.');
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

                // Jika SEMUA barang adalah 'bahan' (habis pakai), KITA TIDAK AUTO-COMPLETE
                // User request: "guru kudu ambil aja, trus nanti diserahin barangnya ama petugas nah baru deh selesai"
                // Jadi status tetap 'disetujui' sampai dilakukan handover.
                
                /* 
                // OLD LOGIC (Auto Complete) - REMOVED PER REQUEST
                if ($allBahan) {
                    $newStatus = 'selesai';
                    // ... code removed ...
                } 
                */

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

            // Send notification/email to user
            if ($newStatus === 'disetujui') {
                // Send Email with QR Code
                \Illuminate\Support\Facades\Mail::to($peminjaman->user)->send(new \App\Mail\PeminjamanApproved($peminjaman));
                
                // Old Notification (Disabled)
                /*
                \App\Models\Notification::send(
                    $peminjaman->user_id,
                    \App\Models\Notification::TYPE_PEMINJAMAN_APPROVED,
                    'Peminjaman Disetujui ✅',
                    'Peminjaman Anda telah disetujui oleh ' . auth()->user()->name . '. Silakan ambil barang sesuai jadwal.',
                    route('peminjaman.show', $peminjaman)
                );
                */
            } elseif ($newStatus === 'ditolak') {
                // TODO: Create PeminjamanRejected Mail if needed
                
                // Old Notification (Disabled)
                /*
                \App\Models\Notification::send(
                    $peminjaman->user_id,
                    \App\Models\Notification::TYPE_PEMINJAMAN_REJECTED,
                    'Peminjaman Ditolak ❌',
                    'Peminjaman Anda ditolak. Alasan: ' . ($validated['catatan_petugas'] ?? 'Tidak ada keterangan'),
                    route('peminjaman.show', $peminjaman)
                );
                */
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
    /**
     * Process the granular handover (serah terima per item).
     */
    public function processHandover(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        $user = auth()->user();

        // Hanya admin dan petugas
        if ($user->isPeminjam()) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan serah terima.');
        }

        // Validate selection
        $validated = $request->validate([
            'detail_ids' => 'required|array',
            'detail_ids.*' => 'exists:peminjaman_details,id',
        ]);

        DB::beginTransaction();
        try {
            // 1. Mark selected items as handed over
            $now = now();
            foreach ($validated['detail_ids'] as $detailId) {
                // Ensure detail belongs to this peminjaman
                $detail = $peminjaman->details()->find($detailId);
                if ($detail && is_null($detail->handed_over_at)) {
                    $detail->update([
                        'handed_over_at' => $now,
                        'handed_over_by' => $user->id
                    ]);
                }
            }

            // 2. Determine Loan Status Change
            // If loan is Consumable-Only -> Status becomes 'selesai' immediately upon handover (nothing to return)
            // If loan has Assets -> Status becomes 'dipinjam'
            
            $hasAssets = $peminjaman->details()->whereNotNull('sarpras_unit_id')->exists();
            $allConsumables = !$hasAssets; // Simplified specific check

            // Check if this is the FIRST handover action (Status still 'disetujui')
            if ($peminjaman->status == 'disetujui') {
                if ($allConsumables) {
                    $peminjaman->update([
                        'status' => 'selesai', // Consumables: Done after pickup
                        'petugas_id' => $user->id,
                    ]);
                    
                    // Auto-create Pengembalian record for consistency (optional but good for logs)
                    // We can skip detail creation since no units are returned.
                     \App\Models\Pengembalian::create([
                        'peminjaman_id' => $peminjaman->id,
                        'petugas_id' => $user->id,
                        'tgl_kembali_aktual' => now(),
                    ]);
                    
                } else {
                    $peminjaman->update([
                        'status' => 'dipinjam', // Assets: Ongoing loan
                        'petugas_id' => $user->id,
                    ]);
                }
            }
            
            // Update global handover timestamp metadata if null (start date)
            if (is_null($peminjaman->handover_at)) {
                $peminjaman->update([
                    'handover_at' => $now, 
                    'handover_by' => $user->id
                ]);
            }

            // 3. Log Activity
            $count = count($validated['detail_ids']);
            \App\Helpers\LogHelper::record('update', "Serah terima {$count} item peminjaman (ID: {$peminjaman->id}) oleh {$user->name}");

            // 4. Notification? Maybe only if ALL items are taken?
            // Let's send a notification about "Partial Pickup" or just "Pickup Started"
            // For now, let's notify simply.
             \App\Models\Notification::send(
                $peminjaman->user_id,
                \App\Models\Notification::TYPE_PEMINJAMAN_APPROVED,
                'Barang Diambil 📦',
                "Sebanyak {$count} item telah diserahkan kepada Anda. Cek status peminjaman.",
                route('peminjaman.show', $peminjaman)
            );

            DB::commit();

            return redirect()
                ->route('peminjaman.show', $peminjaman)
                ->with('success', "Berhasil menyerahkan {$count} item.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses serah terima: ' . $e->getMessage());
        }
    }
}

