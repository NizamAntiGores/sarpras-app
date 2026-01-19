<?php

namespace App\Http\Controllers;

use App\Models\Sarpras;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BarangRusakController extends Controller
{
    /**
     * Display a listing of barang rusak (sarpras dengan stok_rusak > 0).
     */
    public function index(): View
    {
        $barangRusak = Sarpras::with(['kategori', 'lokasi'])
            ->where('stok_rusak', '>', 0)
            ->orderBy('nama_barang')
            ->paginate(10);

        // Statistik
        $totalJenis = Sarpras::where('stok_rusak', '>', 0)->count();
        $totalUnit = Sarpras::sum('stok_rusak');

        return view('barang-rusak.index', compact('barangRusak', 'totalJenis', 'totalUnit'));
    }

    /**
     * Perbaiki barang - pindahkan dari stok_rusak ke stok.
     */
    public function perbaiki(Request $request, Sarpras $sarpras): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1|max:' . $sarpras->stok_rusak,
        ], [
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.min' => 'Jumlah minimal 1.',
            'jumlah.max' => 'Jumlah tidak boleh melebihi stok rusak (' . $sarpras->stok_rusak . ').',
        ]);

        $sarpras->decrement('stok_rusak', $validated['jumlah']);
        $sarpras->increment('stok', $validated['jumlah']);

        return redirect()
            ->route('barang-rusak.index')
            ->with('success', "{$validated['jumlah']} unit {$sarpras->nama_barang} berhasil diperbaiki dan dipindahkan ke stok tersedia.");
    }

    /**
     * Hapus barang rusak dari stok (tidak bisa diperbaiki).
     */
    public function hapus(Request $request, Sarpras $sarpras): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1|max:' . $sarpras->stok_rusak,
        ], [
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.min' => 'Jumlah minimal 1.',
            'jumlah.max' => 'Jumlah tidak boleh melebihi stok rusak (' . $sarpras->stok_rusak . ').',
        ]);

        $sarpras->decrement('stok_rusak', $validated['jumlah']);

        return redirect()
            ->route('barang-rusak.index')
            ->with('success', "{$validated['jumlah']} unit {$sarpras->nama_barang} telah dihapus dari inventaris.");
    }
}
