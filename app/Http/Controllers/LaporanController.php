<?php

namespace App\Http\Controllers;

use App\Models\BarangHilang;
use App\Models\Maintenance;
use App\Models\SarprasUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Display the Executive Dashboard.
     */
    public function executive(): View
    {
        // 1. High-Level Metrics
        $totalAssets = SarprasUnit::count();
        $totalValue = 0; // Placeholder if no price field
        
        $activeLoans = \App\Models\Peminjaman::where('status', 'dipinjam')->count();
        $pendingIssues = \App\Models\Pengaduan::whereIn('status', ['belum_ditindaklanjuti', 'sedang_diproses'])->count();
        $criticalAssets = SarprasUnit::where('kondisi', 'rusak_berat')->count();

        // 2. Asset Condition Summary (Pie Chart Data)
        $conditionStats = SarprasUnit::select('kondisi', DB::raw('count(*) as count'))
            ->groupBy('kondisi')
            ->pluck('count', 'kondisi')
            ->toArray();

        // 3. Category Distribution
        $categoryStats = \App\Models\Kategori::withCount('sarpras')
            ->get()
            ->map(function ($cat) {
                // Approximate unit count via sarpras relation logic if needed, 
                // or just use the direct relationship if it existed.
                // Here we count units per category:
                $unitCount = SarprasUnit::whereHas('sarpras', function($q) use ($cat) {
                    $q->where('kategori_id', $cat->id);
                })->count();
                return [
                    'name' => $cat->nama_kategori,
                    'count' => $unitCount
                ];
            });

        // 4. Recent Critical Activities (Lost, Damaged)
        $recentCriticalLogs = \App\Models\ActivityLog::where(function($q) {
                $q->where('description', 'like', '%rusak%')
                  ->orWhere('description', 'like', '%hilang%');
            })
            ->latest()
            ->limit(5)
            ->get();

        // 5. Monthly Borrowing Trend (Last 6 Months)
        $monthlyTrend = \App\Models\Peminjaman::select(
                DB::raw('count(id) as count'), 
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year")
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->get();

        return view('laporan.executive', compact(
            'totalAssets', 
            'activeLoans', 
            'pendingIssues', 
            'criticalAssets',
            'conditionStats',
            'categoryStats',
            'recentCriticalLogs',
            'monthlyTrend'
        ));
    }

    /**
     * Display the Asset Health Report.
     */
    public function assetHealth(Request $request): View
    {
        $lokasiId = $request->input('lokasi_id');
        $kategoriId = $request->input('kategori_id');
        
        $lokasiList = \App\Models\Lokasi::orderBy('nama_lokasi')->get();
        $kategoriList = \App\Models\Kategori::orderBy('nama_kategori')->get();
        
        $selectedLokasi = $lokasiId ? \App\Models\Lokasi::find($lokasiId) : null;
        $selectedKategori = $kategoriId ? \App\Models\Kategori::find($kategoriId) : null;

        // Base query with optional lokasi and kategori filter
        $unitQuery = SarprasUnit::query();
        if ($lokasiId) {
            $unitQuery->where('lokasi_id', $lokasiId);
        }
        if ($kategoriId) {
            $unitQuery->whereHas('sarpras', function ($q) use ($kategoriId) {
                $q->where('kategori_id', $kategoriId);
            });
        }

        // 1. Ringkasan Kondisi Aset (Chart Pie)
        $kondisiSummary = (clone $unitQuery)->select('kondisi', DB::raw('count(*) as total'))
            ->groupBy('kondisi')
            ->get();

        // 2. Daftar Aset Rusak Berat / Butuh Maintenance
        $asetRusak = (clone $unitQuery)->with(['sarpras', 'lokasi'])
            ->where(function ($q) {
                $q->whereIn('kondisi', ['rusak_berat', 'rusak_ringan'])
                    ->orWhere('status', 'maintenance');
            })
            ->get();

        // 2.5 Daftar Semua Unit (untuk detail view - only when filter is applied)
        $daftarUnit = null;
        if ($lokasiId || $kategoriId) {
            $daftarUnit = (clone $unitQuery)->with(['sarpras.kategori', 'lokasi'])
                ->orderBy('sarpras_id')
                ->paginate(15)
                ->appends(request()->query());
        }

        // 3. Top 10 Aset Paling Sering Rusak (Historical Analysis)
        $seringRusakQuery = DB::table('pengembalian_details')
            ->join('sarpras_units', 'pengembalian_details.sarpras_unit_id', '=', 'sarpras_units.id')
            ->join('sarpras', 'sarpras_units.sarpras_id', '=', 'sarpras.id')
            ->where('pengembalian_details.kondisi_akhir', '!=', 'baik');

        if ($lokasiId) {
            $seringRusakQuery->where('sarpras_units.lokasi_id', $lokasiId);
        }
        if ($kategoriId) {
            $seringRusakQuery->where('sarpras.kategori_id', $kategoriId);
        }

        $seringRusak = $seringRusakQuery
            ->select('sarpras.nama_barang', DB::raw('count(*) as total_kerusakan'))
            ->groupBy('sarpras.id', 'sarpras.nama_barang')
            ->orderByDesc('total_kerusakan')
            ->limit(10)
            ->get();

        // 4. Daftar Aset Hilang
        $asetHilangQuery = BarangHilang::with(['pengembalianDetail.sarprasUnit.sarpras', 'user']);
        if ($lokasiId) {
            $asetHilangQuery->whereHas('pengembalianDetail.sarprasUnit', function ($q) use ($lokasiId) {
                $q->where('lokasi_id', $lokasiId);
            });
        }
        if ($kategoriId) {
            $asetHilangQuery->whereHas('pengembalianDetail.sarprasUnit.sarpras', function ($q) use ($kategoriId) {
                $q->where('kategori_id', $kategoriId);
            });
        }
        $asetHilang = $asetHilangQuery->latest()->get();

        // 5. Riwayat Maintenance Terakhir
        $maintenanceQuery = Maintenance::with(['sarprasUnit.sarpras', 'sarprasUnit.lokasi']);
        if ($lokasiId) {
            $maintenanceQuery->whereHas('sarprasUnit', function ($q) use ($lokasiId) {
                $q->where('lokasi_id', $lokasiId);
            });
        }
        if ($kategoriId) {
            $maintenanceQuery->whereHas('sarprasUnit.sarpras', function ($q) use ($kategoriId) {
                $q->where('kategori_id', $kategoriId);
            });
        }
        $riwayatMaintenance = $maintenanceQuery->orderBy('tanggal_selesai', 'desc')->limit(10)->get();

        // 6. Per-Location Summary (only when no lokasi filter applied)
        $lokasiSummary = [];
        if (! $lokasiId) {
            $lokasiSummaryQuery = \App\Models\Lokasi::query();
            
            // Apply kategori filter to lokasi summary
            $kategoriFilterId = $kategoriId;
            $lokasiSummary = $lokasiSummaryQuery->withCount([
                'units as total_unit' => function ($q) use ($kategoriFilterId) {
                    if ($kategoriFilterId) {
                        $q->whereHas('sarpras', fn($sq) => $sq->where('kategori_id', $kategoriFilterId));
                    }
                },
                'units as tersedia' => function ($q) use ($kategoriFilterId) {
                    $q->where('status', 'tersedia')->where('kondisi', 'baik');
                    if ($kategoriFilterId) {
                        $q->whereHas('sarpras', fn($sq) => $sq->where('kategori_id', $kategoriFilterId));
                    }
                },
                'units as dipinjam' => function ($q) use ($kategoriFilterId) {
                    $q->where('status', 'dipinjam');
                    if ($kategoriFilterId) {
                        $q->whereHas('sarpras', fn($sq) => $sq->where('kategori_id', $kategoriFilterId));
                    }
                },
                'units as rusak' => function ($q) use ($kategoriFilterId) {
                    $q->whereIn('kondisi', ['rusak_ringan', 'rusak_berat']);
                    if ($kategoriFilterId) {
                        $q->whereHas('sarpras', fn($sq) => $sq->where('kategori_id', $kategoriFilterId));
                    }
                },
                'units as maintenance' => function ($q) use ($kategoriFilterId) {
                    $q->where('status', 'maintenance');
                    if ($kategoriFilterId) {
                        $q->whereHas('sarpras', fn($sq) => $sq->where('kategori_id', $kategoriFilterId));
                    }
                },
            ])->get();
        }

        // 7. Per-Kategori Summary (only when no kategori filter applied)
        $kategoriSummary = [];
        if (! $kategoriId) {
            $lokasiFilterId = $lokasiId;
            $kategoriSummary = \App\Models\Kategori::withCount([
                'sarpras as total_unit' => function ($q) use ($lokasiFilterId) {
                    // Count units through sarpras
                },
            ])->get()->map(function ($kategori) use ($lokasiFilterId) {
                $unitsQuery = SarprasUnit::whereHas('sarpras', fn($q) => $q->where('kategori_id', $kategori->id));
                if ($lokasiFilterId) {
                    $unitsQuery->where('lokasi_id', $lokasiFilterId);
                }
                
                $kategori->total_unit = (clone $unitsQuery)->count();
                $kategori->tersedia = (clone $unitsQuery)->where('status', 'tersedia')->where('kondisi', 'baik')->count();
                $kategori->dipinjam = (clone $unitsQuery)->where('status', 'dipinjam')->count();
                $kategori->rusak = (clone $unitsQuery)->whereIn('kondisi', ['rusak_ringan', 'rusak_berat'])->count();
                $kategori->maintenance = (clone $unitsQuery)->where('status', 'maintenance')->count();
                
                return $kategori;
            });
        }

        return view('laporan.asset_health', compact(
            'kondisiSummary',
            'asetRusak',
            'daftarUnit',
            'seringRusak',
            'asetHilang',
            'riwayatMaintenance',
            'lokasiList',
            'kategoriList',
            'selectedLokasi',
            'selectedKategori',
            'lokasiSummary',
            'kategoriSummary'
        ));
    }
}
