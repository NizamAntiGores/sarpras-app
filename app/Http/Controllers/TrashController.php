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
    public function index(Request $request): View
    {
        $units = SarprasUnit::with(['sarpras', 'lokasi'])
            ->where('status', SarprasUnit::STATUS_DIHAPUSBUKUKAN)
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('trash.index', compact('units'));
    }

    /**
     * Restore the specified unit (set status back to available).
     */
    public function restore($id): RedirectResponse
    {
        $unit = SarprasUnit::findOrFail($id);
        
        // Cek apakah statusnya memang dihapusbukukan
        if ($unit->status !== SarprasUnit::STATUS_DIHAPUSBUKUKAN) {
            return redirect()->back()->with('error', 'Unit ini tidak dalam status dihapusbukukan.');
        }

        $unit->update(['status' => SarprasUnit::STATUS_TERSEDIA]);

        \App\Helpers\LogHelper::record('update', "Mengembalikan (restore) unit {$unit->kode_unit} ({$unit->sarpras->nama_barang}) dari sampah.");

        return redirect()->route('trash.index')->with('success', "Unit {$unit->kode_unit} berhasil dikembalikan ke sirkulasi.");
    }
}
