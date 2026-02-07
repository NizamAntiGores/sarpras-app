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

        // Logic Correction: 
        // 1. Assets count 'units' (bisaDipinjam)
        // 2. Consumables count 'stocks' (quantity)
        // We need 'withSum' for stocks to filter consumables effectively in SQL
        
        $query = Sarpras::with(['kategori'])
            ->withCount([
                'units as available_count' => function ($query) {
                    $query->bisaDipinjam()
                        ->whereHas('lokasi', fn($q) => $q->where('is_storefront', true));
                }
            ])
            ->withSum(['stocks as available_stock' => function($query) {
                $query->whereHas('lokasi', fn($q) => $q->where('is_storefront', true));
            }], 'quantity') // Sum stock quantity
            ->when(!auth()->user()->isGuru(), function ($query) {
                // If NOT guru (e.g. Student?), maybe filter types?
                // Existing logic: Students don't see consumables?
                // User said: "di bagian guru tidak muncul" -> implying Guru SHOULD see it.
                // If logic was: "If NOT guru, hide bahan" -> Then Guru SEES bahan.
                // But the 'having' was blocking it.
                $query->where('tipe', '!=', 'bahan');
            })
            // Fix Filter: Show if (Available Units > 0) OR (Available Stock > 0)
            ->where(function($q) {
                $q->having('available_count', '>', 0)
                  ->orHaving('available_stock', '>', 0);
            })
            ->orderBy('nama_barang');

        // Filter by kategori jika ada
        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        $sarpras = $query->get();
        $kategori = Kategori::orderBy('nama_kategori')->get();

        // Stats untuk header (Corrected for Global Count)
        // This is complex in SQL, let's keep it simple or remove if inconsistent
        // Or just count all sarpras that match general availability?
        // Let's rely on the main query count for accurate display or just basic count.
        $totalBarang = Sarpras::count(); 
        $totalKategori = Kategori::count();

        return view('katalog.index', compact('sarpras', 'kategori', 'kategoriId', 'totalBarang', 'totalKategori'));
    }
}
