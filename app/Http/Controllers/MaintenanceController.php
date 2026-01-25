<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\SarprasUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of all maintenance records.
     */
    public function index(Request $request): View
    {
        $query = Maintenance::with(['sarprasUnit.sarpras', 'petugas'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $maintenances = $query->paginate(15)->withQueryString();

        $filters = [
            'status' => $request->status,
            'jenis' => $request->jenis,
        ];

        return view('maintenance.index', compact('maintenances', 'filters'));
    }

    /**
     * Show the form for creating a new maintenance record.
     */
    public function create(Request $request): View
    {
        // Jika unit_id diberikan, pre-select unit tersebut
        $selectedUnit = null;
        if ($request->filled('unit_id')) {
            $selectedUnit = SarprasUnit::with('sarpras')->find($request->unit_id);
        }

        // Ambil unit yang tersedia untuk maintenance (tidak sedang dipinjam)
        $units = SarprasUnit::with('sarpras')
            ->where('status', '!=', SarprasUnit::STATUS_DIPINJAM)
            ->where('status', '!=', SarprasUnit::STATUS_DIHAPUSBUKUKAN)
            ->orderBy('kode_unit')
            ->get();

        $selectedKondisi = $request->query('kondisi');

        return view('maintenance.create', compact('units', 'selectedUnit', 'selectedKondisi'));
    }

    /**
     * Store a newly created maintenance record.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sarpras_unit_id' => 'required|exists:sarpras_units,id',
            'jenis' => 'required|in:perbaikan,servis_rutin,kalibrasi,penggantian_komponen',
            'deskripsi' => 'nullable|string|max:1000',
            'tanggal_mulai' => 'required|date',
            'biaya' => 'nullable|integer|min:0',
        ], [
            'sarpras_unit_id.required' => 'Unit wajib dipilih.',
            'jenis.required' => 'Jenis maintenance wajib dipilih.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
        ]);

        DB::beginTransaction();

        try {
            $unit = SarprasUnit::findOrFail($validated['sarpras_unit_id']);

            // Cek apakah unit sedang dipinjam
            if ($unit->status === SarprasUnit::STATUS_DIPINJAM) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Unit sedang dipinjam, tidak dapat di-maintenance.');
            }

            // Buat record maintenance
            $maintenance = Maintenance::create([
                'sarpras_unit_id' => $validated['sarpras_unit_id'],
                'petugas_id' => auth()->id(),
                'jenis' => $validated['jenis'],
                'deskripsi' => $validated['deskripsi'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'biaya' => $validated['biaya'],
                'status' => Maintenance::STATUS_SEDANG_BERLANGSUNG,
            ]);

            // Update status unit menjadi maintenance
            $unit->update(['status' => SarprasUnit::STATUS_MAINTENANCE]);

            DB::commit();

            return redirect()
                ->route('maintenance.index')
                ->with('success', "Maintenance untuk unit {$unit->kode_unit} berhasil dibuat.");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Display the specified maintenance record.
     */
    public function show(Maintenance $maintenance): View
    {
        $maintenance->load(['sarprasUnit.sarpras', 'petugas']);

        return view('maintenance.show', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified maintenance record.
     */
    public function edit(Maintenance $maintenance): View
    {
        $maintenance->load('sarprasUnit.sarpras');

        return view('maintenance.edit', compact('maintenance'));
    }

    /**
     * Update the specified maintenance record.
     */
    public function update(Request $request, Maintenance $maintenance): RedirectResponse
    {
        $validated = $request->validate([
            'jenis' => 'required|in:perbaikan,servis_rutin,kalibrasi,penggantian_komponen',
            'deskripsi' => 'nullable|string|max:1000',
            'tanggal_selesai' => 'nullable|date|after_or_equal:'.$maintenance->tanggal_mulai->format('Y-m-d'),
            'biaya' => 'nullable|integer|min:0',
            'status' => 'required|in:sedang_berlangsung,selesai,dibatalkan',
            'kondisi_setelah' => 'required_if:status,selesai|in:baik,rusak_ringan,rusak_berat',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'kondisi_setelah.required_if' => 'Kondisi setelah maintenance wajib diisi jika status selesai.',
        ]);

        DB::beginTransaction();

        try {
            $unit = $maintenance->sarprasUnit;

            // Jika status berubah menjadi selesai
            if ($validated['status'] === Maintenance::STATUS_SELESAI) {
                $validated['tanggal_selesai'] = $validated['tanggal_selesai'] ?? now();

                // Update kondisi unit
                $unit->update([
                    'kondisi' => $validated['kondisi_setelah'],
                    'status' => SarprasUnit::STATUS_TERSEDIA,
                ]);
            }

            // Jika status berubah menjadi dibatalkan
            if ($validated['status'] === Maintenance::STATUS_DIBATALKAN) {
                // Kembalikan status unit ke tersedia
                $unit->update(['status' => SarprasUnit::STATUS_TERSEDIA]);
            }

            // Hapus kondisi_setelah dari validated karena bukan kolom maintenance
            unset($validated['kondisi_setelah']);

            $maintenance->update($validated);

            DB::commit();

            return redirect()
                ->route('maintenance.index')
                ->with('success', "Maintenance untuk unit {$unit->kode_unit} berhasil diperbarui.");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified maintenance record.
     */
    public function destroy(Maintenance $maintenance): RedirectResponse
    {
        // Hanya bisa hapus maintenance yang sudah selesai atau dibatalkan
        if ($maintenance->status === Maintenance::STATUS_SEDANG_BERLANGSUNG) {
            return redirect()
                ->route('maintenance.index')
                ->with('error', 'Tidak dapat menghapus maintenance yang sedang berlangsung. Selesaikan atau batalkan terlebih dahulu.');
        }

        $kodeUnit = $maintenance->sarprasUnit->kode_unit;
        $maintenance->delete();

        return redirect()
            ->route('maintenance.index')
            ->with('success', "Record maintenance untuk unit {$kodeUnit} berhasil dihapus.");
    }
}
