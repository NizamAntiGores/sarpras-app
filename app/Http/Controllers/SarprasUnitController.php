<?php

namespace App\Http\Controllers;

use App\Models\Sarpras;
use App\Models\SarprasUnit;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class SarprasUnitController extends Controller
{
    /**
     * Display a listing of units for a specific sarpras.
     */
    public function index(Sarpras $sarpras): View
    {
        $units = $sarpras->units()
            ->with('lokasi')
            ->orderBy('kode_unit')
            ->paginate(20);

        return view('sarpras-unit.index', compact('sarpras', 'units'));
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
            'nilai_perolehan' => 'nullable|integer|min:0',
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
                    'nilai_perolehan' => $validated['nilai_perolehan'] ?? null,
                ]);

                $unitsCreated[] = $kodeUnit;
            }

            DB::commit();

            $message = count($unitsCreated) === 1
                ? "Unit {$unitsCreated[0]} berhasil ditambahkan."
                : count($unitsCreated) . " unit berhasil ditambahkan ({$unitsCreated[0]} - " . end($unitsCreated) . ").";

            \App\Helpers\LogHelper::record('create', "Menambahkan unit untuk {$sarpras->nama_barang}: " . implode(', ', $unitsCreated));

            return redirect()
                ->route('sarpras.units.index', $sarpras)
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
            'nilai_perolehan' => 'nullable|integer|min:0',
        ]);

        // Validasi: unit yang sedang dipinjam tidak bisa diubah statusnya secara manual
        if ($unit->status === SarprasUnit::STATUS_DIPINJAM && $validated['status'] !== SarprasUnit::STATUS_DIPINJAM) {
            return redirect()
                ->back()
                ->with('error', 'Unit yang sedang dipinjam tidak bisa diubah statusnya. Selesaikan peminjaman terlebih dahulu.');
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
     * Bulk update kondisi untuk multiple units.
     */
    public function bulkUpdateKondisi(Request $request, Sarpras $sarpras): RedirectResponse
    {
        $validated = $request->validate([
            'unit_ids' => 'required|array|min:1',
            'unit_ids.*' => 'exists:sarpras_units,id',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
        ]);

        $updated = SarprasUnit::whereIn('id', $validated['unit_ids'])
            ->where('sarpras_id', $sarpras->id)
            ->update(['kondisi' => $validated['kondisi']]);

        return redirect()
            ->route('sarpras.units.index', $sarpras)
            ->with('success', "{$updated} unit berhasil diperbarui kondisinya.");
    }
}
