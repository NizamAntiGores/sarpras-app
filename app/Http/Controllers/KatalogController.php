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
        $lokasiId = $request->query('lokasi');
        $search = $request->query('search');

        // Logic Correction: 
        // 1. Assets count 'units' (bisaDipinjam)
        // 2. Consumables count 'stocks' (quantity)
        // We need 'withSum' for stocks to filter consumables effectively in SQL
        
        $query = Sarpras::with(['kategori'])
            ->withCount([
                'units as available_count' => function ($query) use ($lokasiId) {
                    $query->bisaDipinjam()
                        ->whereHas('lokasi', function($q) use ($lokasiId) {
                            $q->where('is_storefront', true);
                            if ($lokasiId) {
                                $q->where('id', $lokasiId);
                            }
                        });
                }
            ])
            ->withSum(['stocks as available_stock' => function($query) use ($lokasiId) {
                $query->whereHas('lokasi', function($q) use ($lokasiId) {
                    $q->where('is_storefront', true);
                    if ($lokasiId) {
                        $q->where('id', $lokasiId);
                    }
                });
            }], 'quantity') // Sum stock quantity
            ->when(!auth()->user()->isGuru(), function ($query) {
                // If NOT guru (e.g. Student?), maybe filter types?
                $query->where('tipe', '!=', 'bahan');
            })
            // Filter by search (nama items)
            ->when($search, function ($query) use ($search) {
                $query->where('nama_barang', 'like', "%{$search}%");
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
        
        // Ambil lokasi storefront untuk filter
        $lokasiList = \App\Models\Lokasi::where('is_storefront', true)->orderBy('nama_lokasi')->get();

        // Stats untuk header (Corrected for Global Count)
        $totalBarang = Sarpras::count(); 
        $totalKategori = Kategori::count();

        return view('katalog.index', compact('sarpras', 'kategori', 'kategoriId', 'lokasiId', 'search', 'lokasiList', 'totalBarang', 'totalKategori'));
    }
}
