<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Sarpras;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KatalogController extends Controller
{
    /**
     * Display katalog sarpras untuk siswa/peminjam
     * Grid view dengan foto, stok indicator, dan filter kategori
     */
    public function index(Request $request): View
    {
        $kategoriId = $request->query('kategori');

        $query = Sarpras::with(['kategori'])
            ->withCount([
                'units as available_count' => function ($query) {
                    $query->bisaDipinjam();
                }
            ])
            ->when(!auth()->user()->isGuru(), function ($query) {
                $query->where('tipe', '!=', 'bahan');
            })
            ->having('available_count', '>', 0)
            ->orderBy('nama_barang');

        // Filter by kategori jika ada
        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        $sarpras = $query->get();
        $kategori = Kategori::orderBy('nama_kategori')->get();

        // Stats untuk header
        $totalBarang = Sarpras::whereHas('units', function ($q) {
            $q->bisaDipinjam();
        })->count();
        $totalKategori = Kategori::count();

        return view('katalog.index', compact('sarpras', 'kategori', 'kategoriId', 'totalBarang', 'totalKategori'));
    }
}
