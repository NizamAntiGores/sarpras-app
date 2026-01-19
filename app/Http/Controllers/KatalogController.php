<?php

namespace App\Http\Controllers;

use App\Models\Sarpras;
use App\Models\Kategori;
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
        
        $query = Sarpras::with('kategori')
            ->where('stok', '>', 0)
            ->orderBy('nama_barang');
        
        // Filter by kategori jika ada
        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }
        
        $sarpras = $query->get();
        $kategori = Kategori::orderBy('nama_kategori')->get();
        
        // Stats untuk header
        $totalBarang = Sarpras::where('stok', '>', 0)->count();
        $totalKategori = Kategori::count();
        
        return view('katalog.index', compact('sarpras', 'kategori', 'kategoriId', 'totalBarang', 'totalKategori'));
    }
}
