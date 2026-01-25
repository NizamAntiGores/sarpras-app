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
     * Display the Asset Health Report.
     */
    public function assetHealth(Request $request): View
    {
        $lokasiId = $request->input('lokasi_id');
        $lokasiList = \App\Models\Lokasi::orderBy('nama_lokasi')->get();
        $selectedLokasi = $lokasiId ? \App\Models\Lokasi::find($lokasiId) : null;

        // Base query with optional lokasi filter
        $unitQuery = SarprasUnit::query();
        if ($lokasiId) {
            $unitQuery->where('lokasi_id', $lokasiId);
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

        // 3. Top 10 Aset Paling Sering Rusak (Historical Analysis)
        $seringRusakQuery = DB::table('pengembalian_details')
            ->join('sarpras_units', 'pengembalian_details.sarpras_unit_id', '=', 'sarpras_units.id')
            ->join('sarpras', 'sarpras_units.sarpras_id', '=', 'sarpras.id')
            ->where('pengembalian_details.kondisi_akhir', '!=', 'baik');

        if ($lokasiId) {
            $seringRusakQuery->where('sarpras_units.lokasi_id', $lokasiId);
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
        $asetHilang = $asetHilangQuery->latest()->get();

        // 5. Riwayat Maintenance Terakhir
        $maintenanceQuery = Maintenance::with(['sarprasUnit.sarpras', 'sarprasUnit.lokasi']);
        if ($lokasiId) {
            $maintenanceQuery->whereHas('sarprasUnit', function ($q) use ($lokasiId) {
                $q->where('lokasi_id', $lokasiId);
            });
        }
        $riwayatMaintenance = $maintenanceQuery->orderBy('tanggal_selesai', 'desc')->limit(10)->get();

        // 6. Per-Location Summary (only when no filter applied)
        $lokasiSummary = [];
        if (! $lokasiId) {
            $lokasiSummary = \App\Models\Lokasi::withCount([
                'units as total_unit',
                'units as tersedia' => function ($q) {
                    $q->where('status', 'tersedia')->where('kondisi', 'baik');
                },
                'units as dipinjam' => function ($q) {
                    $q->where('status', 'dipinjam');
                },
                'units as rusak' => function ($q) {
                    $q->whereIn('kondisi', ['rusak_ringan', 'rusak_berat']);
                },
                'units as maintenance' => function ($q) {
                    $q->where('status', 'maintenance');
                },
            ])->get();
        }

        return view('laporan.asset_health', compact(
            'kondisiSummary',
            'asetRusak',
            'seringRusak',
            'asetHilang',
            'riwayatMaintenance',
            'lokasiList',
            'selectedLokasi',
            'lokasiSummary'
        ));
    }
}
