<?php

namespace App\Http\Controllers;

use App\Models\BarangHilang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarangHilangController extends Controller
{
    /**
     * Display a listing of barang hilang.
     */
    public function index(Request $request): View
    {
        $query = BarangHilang::with(['pengembalianDetail.sarprasUnit.sarpras', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $barangHilang = $query->paginate(10)->withQueryString();

        // Statistik
        $totalHilang = BarangHilang::count();
        $belumDiganti = BarangHilang::where('status', BarangHilang::STATUS_BELUM_DIGANTI)->count();
        $sudahDiganti = BarangHilang::where('status', BarangHilang::STATUS_SUDAH_DIGANTI)->count();

        return view('barang-hilang.index', compact(
            'barangHilang',
            'totalHilang',
            'belumDiganti',
            'sudahDiganti'
        ));
    }

    /**
     * Update status barang hilang (sudah diganti).
     */
    public function update(Request $request, BarangHilang $barangHilang): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:belum_diganti,sudah_diganti,diputihkan',
            'keterangan' => 'nullable|string|min:20|max:500',
        ]);

        $barangHilang->update([
            'status' => $validated['status'],
            'keterangan' => $validated['keterangan'] ?? $barangHilang->keterangan,
        ]);

        $message = match ($validated['status']) {
            'sudah_diganti' => 'Barang telah ditandai sebagai sudah diganti.',
            'diputihkan' => 'Barang telah diputihkan.',
            default => 'Status barang dikembalikan ke belum diganti.',
        };

        return redirect()
            ->route('barang-hilang.index')
            ->with('success', $message);
    }
}
