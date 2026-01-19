<?php

namespace App\Http\Controllers;

use App\Models\BarangHilang;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BarangHilangController extends Controller
{
    /**
     * Display a listing of barang hilang.
     */
    public function index(Request $request): View
    {
        $query = BarangHilang::with(['sarpras', 'user', 'pengembalian'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $barangHilang = $query->paginate(10)->withQueryString();

        // Statistik
        $totalHilang = BarangHilang::sum('jumlah');
        $belumDiganti = BarangHilang::where('status', 'belum_diganti')->sum('jumlah');
        $sudahDiganti = BarangHilang::where('status', 'sudah_diganti')->sum('jumlah');

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
            'status' => 'required|in:belum_diganti,sudah_diganti',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $barangHilang->update([
            'status' => $validated['status'],
            'keterangan' => $validated['keterangan'] ?? $barangHilang->keterangan,
        ]);

        $message = $validated['status'] === 'sudah_diganti' 
            ? 'Barang telah ditandai sebagai sudah diganti.'
            : 'Status barang dikembalikan ke belum diganti.';

        return redirect()
            ->route('barang-hilang.index')
            ->with('success', $message);
    }
}
