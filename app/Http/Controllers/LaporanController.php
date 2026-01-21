<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sarpras;
use App\Models\SarprasUnit;
use App\Models\BarangHilang;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Display the Asset Health Report.
     */
    public function assetHealth(): View
    {
        // 1. Ringkasan Kondisi Aset (Chart Pie)
        $kondisiSummary = SarprasUnit::select('kondisi', DB::raw('count(*) as total'))
            ->groupBy('kondisi')
            ->get();

        // 2. Daftar Aset Rusak Berat / Butuh Maintenance
        $asetRusak = SarprasUnit::with(['sarpras', 'lokasi'])
            ->whereIn('kondisi', ['rusak_berat', 'rusak_ringan'])
            ->orWhere('status', 'maintenance')
            ->get();

        // 3. Top 5 Aset Paling Sering Rusak (Historical Analysis)
        // Dihitung dari detail pengembalian yang kondisinya != baik
        $seringRusak = DB::table('pengembalian_details')
            ->join('sarpras_units', 'pengembalian_details.sarpras_unit_id', '=', 'sarpras_units.id')
            ->join('sarpras', 'sarpras_units.sarpras_id', '=', 'sarpras.id')
            ->select(
                'sarpras.nama_barang',
                DB::raw('count(*) as total_kerusakan')
            )
            ->where('pengembalian_details.kondisi_akhir', '!=', 'baik')
            ->groupBy('sarpras.id', 'sarpras.nama_barang')
            ->orderByDesc('total_kerusakan')
            ->limit(10)
            ->get();

        // 4. Daftar Aset Hilang
        $asetHilang = BarangHilang::with(['pengembalianDetail.sarprasUnit.sarpras', 'user'])
            ->latest()
            ->get();

        // 5. Riwayat Maintenance Terakhir
        $riwayatMaintenance = Maintenance::with(['sarprasUnit.sarpras'])
            ->orderBy('tanggal_selesai', 'desc')
            ->limit(10) // Terbatas agar tidak memenuhi halaman, bisa dibuat pagination di page khusus jika perlu
            ->get();

        return view('laporan.asset_health', compact(
            'kondisiSummary',
            'asetRusak',
            'seringRusak',
            'asetHilang',
            'riwayatMaintenance'
        ));
    }
}
