<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\Sarpras;
use App\Models\SarprasUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SarprasUnitController extends Controller
{
    /**
     * Display a listing of units for a specific sarpras.
     */
    public function index(Request $request, Sarpras $sarpras): View
    {
        $query = $sarpras->units()->visibleInList()->with('lokasi');

        // Pencarian berdasarkan kode unit
        if ($request->filled('search')) {
            $query->where('kode_unit', 'like', '%'.$request->search.'%');
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan kondisi
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Hitung statistik global untuk kartu (tidak terpengaruh paginasi)
        $stats = [
            'total_unit' => $sarpras->units()->visibleInList()->count(),
            'tersedia' => $sarpras->units()->visibleInList()->where('status', 'tersedia')->count(),
            'dipinjam' => $sarpras->units()->visibleInList()->where('status', 'dipinjam')->count(),
            'maintenance' => $sarpras->units()->visibleInList()->where('status', 'maintenance')->count(),
        ];

        $units = $query->orderBy('kode_unit')
            ->paginate(20)
            ->withQueryString();

        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        return view('sarpras-unit.index', compact('sarpras', 'units', 'stats', 'lokasis'));
    }

    /**
     * Show the form for creating new units.
     */
    public function create(Sarpras $sarpras): View
    {
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();

        return view('sarpras-unit.create', compact('sarpras', 'lokasis'));
    }

    /**
     * Store newly created units in storage.
     * Bisa menambahkan multiple unit sekaligus.
     */
    public function store(Request $request, Sarpras $sarpras): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah_unit' => 'required|integer|min:1|max:100',
            'lokasi_id' => 'required|exists:lokasi,id',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'tanggal_perolehan' => 'nullable|date',
        ], [
            'jumlah_unit.required' => 'Jumlah unit wajib diisi.',
            'jumlah_unit.min' => 'Minimal 1 unit.',
            'jumlah_unit.max' => 'Maksimal 100 unit per pengadaan.',
            'lokasi_id.required' => 'Lokasi wajib dipilih.',
            'kondisi.required' => 'Kondisi wajib dipilih.',
        ]);

        DB::beginTransaction();

        try {
            $unitsCreated = [];

            for ($i = 0; $i < $validated['jumlah_unit']; $i++) {
                $kodeUnit = SarprasUnit::generateKodeUnit($sarpras);

                $unit = SarprasUnit::create([
                    'sarpras_id' => $sarpras->id,
                    'kode_unit' => $kodeUnit,
                    'lokasi_id' => $validated['lokasi_id'],
                    'kondisi' => $validated['kondisi'],
                    'status' => SarprasUnit::STATUS_TERSEDIA,
                    'tanggal_perolehan' => $validated['tanggal_perolehan'] ?? now(),
                ]);

                $unitsCreated[] = $kodeUnit;
            }

            DB::commit();

            $logDescription = count($unitsCreated) === 1
                ? "Menambahkan unit untuk {$sarpras->nama_barang}: ".$unitsCreated[0]
                : 'Menambahkan '.count($unitsCreated)." unit untuk {$sarpras->nama_barang}: ".$unitsCreated[0].' sampai '.end($unitsCreated);

            \App\Helpers\LogHelper::record('create', $logDescription);

            return redirect()
                ->route('sarpras.units.index', $sarpras)
                ->with('success', $logDescription);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Display the specified unit.
     */
    public function show(Sarpras $sarpras, SarprasUnit $unit): View
    {
        // Pastikan unit milik sarpras ini
        if ($unit->sarpras_id !== $sarpras->id) {
            abort(404);
        }

        $unit->load([
            'lokasi',
            'peminjamanDetails.peminjaman.user',
            'pengembalianDetails.pengembalian',
            'maintenances.petugas',
        ]);

        return view('sarpras-unit.show', compact('sarpras', 'unit'));
    }

    /**
     * Show the form for editing the specified unit.
     */
    public function edit(Sarpras $sarpras, SarprasUnit $unit): View
    {
        if ($unit->sarpras_id !== $sarpras->id) {
            abort(404);
        }

        $lokasis = Lokasi::orderBy('nama_lokasi')->get();

        return view('sarpras-unit.edit', compact('sarpras', 'unit', 'lokasis'));
    }

    /**
     * Update the specified unit in storage.
     */
    public function update(Request $request, Sarpras $sarpras, SarprasUnit $unit): RedirectResponse
    {
        if ($unit->sarpras_id !== $sarpras->id) {
            abort(404);
        }

        $validated = $request->validate([
            'lokasi_id' => 'required|exists:lokasi,id',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'status' => 'required|in:tersedia,dipinjam,maintenance,dihapusbukukan',
            'tanggal_perolehan' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500', // Added for logging reason
        ]);

        // Validasi: unit yang sedang dipinjam tidak bisa diubah statusnya secara manual
        if ($unit->status === SarprasUnit::STATUS_DIPINJAM && $validated['status'] !== SarprasUnit::STATUS_DIPINJAM) {
            return redirect()
                ->back()
                ->with('error', 'Unit yang sedang dipinjam tidak bisa diubah statusnya. Selesaikan peminjaman terlebih dahulu.');
        }

        if ($unit->kondisi !== $validated['kondisi']) {
            \App\Models\UnitConditionLog::create([
                'sarpras_unit_id' => $unit->id,
                'kondisi_lama' => $unit->kondisi,
                'kondisi_baru' => $validated['kondisi'],
                'keterangan' => $request->keterangan ?? 'Update manual oleh admin/petugas',
                'user_id' => auth()->id(),
            ]);
        }

        $unit->update($validated);

        \App\Helpers\LogHelper::record('update', "Memperbarui unit {$unit->kode_unit} ({$sarpras->nama_barang})");

        return redirect()
            ->route('sarpras.units.index', $sarpras)
            ->with('success', "Unit {$unit->kode_unit} berhasil diperbarui.");
    }

    /**
     * Remove the specified unit from storage (soft delete via status).
     */
    public function destroy(Sarpras $sarpras, SarprasUnit $unit): RedirectResponse
    {
        if ($unit->sarpras_id !== $sarpras->id) {
            abort(404);
        }

        // Tidak bisa hapus unit yang sedang dipinjam
        if ($unit->status === SarprasUnit::STATUS_DIPINJAM) {
            return redirect()
                ->route('sarpras.units.index', $sarpras)
                ->with('error', 'Tidak dapat menghapus unit yang sedang dipinjam.');
        }

        // Soft delete: ubah status jadi dihapusbukukan
        $unit->update(['status' => SarprasUnit::STATUS_DIHAPUSBUKUKAN]);

        \App\Helpers\LogHelper::record('delete', "Menghapusbukukan unit {$unit->kode_unit} ({$sarpras->nama_barang})");

        return redirect()
            ->route('sarpras.units.index', $sarpras)
            ->with('success', "Unit {$unit->kode_unit} telah dihapusbukukan.");
    }

    /**
     * Handle Bulk Actions (Mass Update/Delete)
     * Supports:
     * - update_lokasi: Pindah lokasi massal
     * - update_kondisi: Ubah kondisi massal
     * - delete: Hapus (dihapusbukukan) massal
     */
    public function bulkAction(Request $request, Sarpras $sarpras): RedirectResponse
    {
        $validated = $request->validate([
            'unit_ids' => 'required|array|min:1',
            'unit_ids.*' => 'exists:sarpras_units,id',
            'action_type' => 'required|in:update_lokasi,update_kondisi,delete',
            'lokasi_id' => 'required_if:action_type,update_lokasi|nullable|exists:lokasi,id',
            'kondisi' => 'required_if:action_type,update_kondisi|nullable|in:baik,rusak_ringan,rusak_berat',
            'keterangan' => 'nullable|string|max:500', // Alasan perubahan (untuk log)
        ]);

        $unitIds = $validated['unit_ids'];
        $count = count($unitIds);
        $action = $validated['action_type'];

        DB::beginTransaction();

        try {
            switch ($action) {
                case 'update_lokasi':
                    // Jangan update unit yang sedang dipinjam
                    $updated = SarprasUnit::whereIn('id', $unitIds)
                        ->where('sarpras_id', $sarpras->id)
                        ->where('status', '!=', SarprasUnit::STATUS_DIPINJAM)
                        ->update(['lokasi_id' => $validated['lokasi_id']]);

                     \App\Helpers\LogHelper::record('update', "Bulk update location for {$updated} units of {$sarpras->nama_barang}");
                    break;

                case 'update_kondisi':
                    $unitsToUpdate = SarprasUnit::whereIn('id', $unitIds)
                        ->where('sarpras_id', $sarpras->id)
                        ->get();
                    
                    $updated = 0;
                    foreach ($unitsToUpdate as $unit) {
                        // Skip jika kondisi sama
                        if ($unit->kondisi === $validated['kondisi']) {
                            continue;
                        }

                        // Catat log kondisi
                        \App\Models\UnitConditionLog::create([
                            'sarpras_unit_id' => $unit->id,
                            'kondisi_lama' => $unit->kondisi,
                            'kondisi_baru' => $validated['kondisi'],
                            'keterangan' => $request->keterangan ?? 'Bulk update by admin',
                            'user_id' => auth()->id(),
                        ]);

                        // Update unit
                        $unit->update(['kondisi' => $validated['kondisi']]);
                        $updated++;
                    }

                    \App\Helpers\LogHelper::record('update', "Bulk update condition for {$updated} units of {$sarpras->nama_barang}");
                    break;

                case 'delete':
                    // Jangan hapus unit yang sedang dipinjam
                     $updated = SarprasUnit::whereIn('id', $unitIds)
                        ->where('sarpras_id', $sarpras->id)
                        ->where('status', '!=', SarprasUnit::STATUS_DIPINJAM)
                        ->update(['status' => SarprasUnit::STATUS_DIHAPUSBUKUKAN]);
                    
                    \App\Helpers\LogHelper::record('delete', "Bulk delete (dihapusbukukan) {$updated} units of {$sarpras->nama_barang}");
                    break;
            }

            DB::commit();

            if ($updated < $count && $action !== 'update_kondisi') {
                return redirect()->route('sarpras.units.index', $sarpras)
                    ->with('success', "{$updated} unit berhasil diproses. Beberapa unit dilewati karena sedang dipinjam.");
            }

            return redirect()->route('sarpras.units.index', $sarpras)
                ->with('success', "{$updated} unit berhasil diproses.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan bulk action: ' . $e->getMessage());
        }
    }
}
