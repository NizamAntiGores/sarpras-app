<?php

namespace App\Http\Controllers;

use App\Models\SarprasUnit;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TrashController extends Controller
{
    /**
     * Display a listing of the deleted units.
     */
    /**
     * Display a listing of the deleted items (Sarpras & Units).
     */
    public function index(Request $request): View
    {
        // Deleted Master Barang
        $deletedSarpras = \App\Models\Sarpras::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(5, ['*'], 'sarpras_page');

        // Deleted Units
        $units = SarprasUnit::with(['sarpras', 'lokasi'])
            ->where('status', SarprasUnit::STATUS_DIHAPUSBUKUKAN)
            ->orderBy('updated_at', 'desc')
            ->paginate(15, ['*'], 'unit_page');

        return view('trash.index', compact('units', 'deletedSarpras'));
    }

    /**
     * Restore the specified unit.
     */
    public function restoreUnit($id): RedirectResponse
    {
        $unit = SarprasUnit::findOrFail($id);
        
        if ($unit->status !== SarprasUnit::STATUS_DIHAPUSBUKUKAN) {
            return redirect()->back()->with('error', 'Unit ini tidak dalam status dihapusbukukan.');
        }

        // Cek apakah parent sarpras-nya ada (tidak terhapus)
        if ($unit->sarpras && $unit->sarpras->trashed()) {
            return redirect()->back()->with('error', 'Gagal restore: Master Barang (Parent) masih ada di sampah. Restore Master Barang terlebih dahulu.');
        }

        $unit->update(['status' => SarprasUnit::STATUS_TERSEDIA]);

        \App\Helpers\LogHelper::record('update', "Mengembalikan (restore) unit {$unit->kode_unit} dari sampah.");

        return redirect()->route('trash.index')->with('success', "Unit {$unit->kode_unit} berhasil dikembalikan ke sirkulasi.");
    }

    /**
     * Restore the specified Master Sarpras.
     */
    public function restoreSarpras($id): RedirectResponse
    {
        $sarpras = \App\Models\Sarpras::withTrashed()->findOrFail($id);
        
        if (!$sarpras->trashed()) {
            return redirect()->back()->with('error', 'Data ini sudah aktif.');
        }

        $sarpras->restore();

        \App\Helpers\LogHelper::record('update', "Mengembalikan (restore) Master Sarpras {$sarpras->nama_barang} dari sampah.");

        return redirect()->route('trash.index')->with('success', "Master Barang {$sarpras->nama_barang} berhasil dikembalikan. Silakan cek unit-nya.");
    }
}
