<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ActivityLog;
use App\Models\Peminjaman;
use App\Models\BarangHilang;
use App\Models\Maintenance;
use App\Models\Sarpras;
use App\Models\SarprasUnit;
use Carbon\Carbon;

class ExportPdfController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,petugas']);
    }

    /**
     * Export Log Aktivitas ke PDF
     */
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->limit(500)->get();

        $pdf = Pdf::loadView('exports.activity-logs', [
            'logs' => $logs,
            'filters' => $request->only(['search', 'action', 'user_id', 'date_from', 'date_to']),
            'generatedAt' => Carbon::now()->format('d M Y H:i'),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('log-aktivitas-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Export Daftar Peminjaman ke PDF
     */
    public function peminjaman(Request $request)
    {
        $query = Peminjaman::with(['user', 'details.sarprasUnit.sarpras']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tgl_pinjam', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tgl_pinjam', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })->orWhereHas('details.sarprasUnit.sarpras', function ($sarprasQuery) use ($search) {
                    $sarprasQuery->where('nama_barang', 'like', "%{$search}%");
                });
            });
        }

        $peminjaman = $query->orderBy('created_at', 'desc')->limit(500)->get();

        $pdf = Pdf::loadView('exports.peminjaman', [
            'peminjaman' => $peminjaman,
            'filters' => $request->only(['status', 'tanggal_mulai', 'tanggal_selesai', 'search']),
            'generatedAt' => Carbon::now()->format('d M Y H:i'),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('daftar-peminjaman-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Export Barang Hilang ke PDF
     */
    public function barangHilang(Request $request)
    {
        $query = BarangHilang::with(['user', 'pengembalianDetail.sarprasUnit.sarpras']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $barangHilang = $query->orderBy('created_at', 'desc')->limit(500)->get();

        $pdf = Pdf::loadView('exports.barang-hilang', [
            'barangHilang' => $barangHilang,
            'filters' => $request->only(['status']),
            'generatedAt' => Carbon::now()->format('d M Y H:i'),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('laporan-barang-hilang-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Export Maintenance ke PDF
     */
    public function maintenance(Request $request)
    {
        $query = Maintenance::with(['sarprasUnit.sarpras', 'petugas']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $maintenance = $query->orderBy('created_at', 'desc')->limit(500)->get();

        $pdf = Pdf::loadView('exports.maintenance', [
            'maintenanceData' => $maintenance,
            'filters' => $request->only(['status', 'jenis']),
            'generatedAt' => Carbon::now()->format('d M Y H:i'),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('laporan-maintenance-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Export Inventaris Sarpras ke PDF
     */
    public function sarpras(Request $request)
    {
        $query = Sarpras::with(['kategori'])
            ->withCount([
                'units as total_unit',
                'units as stok_tersedia' => function ($q) {
                    $q->where('status', 'tersedia');
                },
                'units as dipinjam_count' => function ($q) {
                    $q->where('status', 'dipinjam');
                },
                'units as maintenance_count' => function ($q) {
                    $q->where('status', 'maintenance');
                },
            ]);

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        $sarpras = $query->orderBy('nama_barang')->get();

        $pdf = Pdf::loadView('exports.sarpras', [
            'sarpras' => $sarpras,
            'filters' => $request->only(['kategori_id', 'tipe', 'search']),
            'generatedAt' => Carbon::now()->format('d M Y H:i'),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('inventaris-sarpras-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Export Unit Sarpras ke PDF
     */
    public function sarprasUnits(Request $request, Sarpras $sarpras)
    {
        $query = $sarpras->units()->with(['lokasi']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        $units = $query->orderBy('kode_unit')->get();

        $pdf = Pdf::loadView('exports.sarpras-units', [
            'sarpras' => $sarpras,
            'units' => $units,
            'filters' => $request->only(['status', 'kondisi']),
            'generatedAt' => Carbon::now()->format('d M Y H:i'),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('unit-' . $sarpras->kode_barang . '-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Export Asset Health Report ke PDF
     */
    public function assetHealth(Request $request)
    {
        $lokasiId = $request->input('lokasi_id');
        $kategoriId = $request->input('kategori_id');

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

        // Ringkasan Kondisi Aset
        $kondisiSummary = (clone $unitQuery)->select('kondisi', \DB::raw('count(*) as total'))
            ->groupBy('kondisi')
            ->get();

        // Daftar Aset Rusak / Maintenance
        $asetRusak = (clone $unitQuery)->with(['sarpras', 'lokasi'])
            ->where(function ($q) {
                $q->whereIn('kondisi', ['rusak_berat', 'rusak_ringan'])
                    ->orWhere('status', 'maintenance');
            })
            ->get();

        // Daftar semua unit jika filter aktif
        $daftarUnit = null;
        if ($lokasiId || $kategoriId) {
            $daftarUnit = (clone $unitQuery)->with(['sarpras.kategori', 'lokasi'])
                ->orderBy('sarpras_id')
                ->get();
        }

        // Aset Hilang
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

        // Riwayat Maintenance
        $maintenanceQuery = Maintenance::with(['sarprasUnit.sarpras', 'petugas']);
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
        $riwayatMaintenance = $maintenanceQuery->orderBy('tanggal_mulai', 'desc')->limit(20)->get();

        $pdf = Pdf::loadView('exports.asset-health', [
            'kondisiSummary' => $kondisiSummary,
            'asetRusak' => $asetRusak,
            'daftarUnit' => $daftarUnit,
            'asetHilang' => $asetHilang,
            'riwayatMaintenance' => $riwayatMaintenance,
            'selectedLokasi' => $selectedLokasi,
            'selectedKategori' => $selectedKategori,
            'filters' => $request->only(['lokasi_id', 'kategori_id']),
            'generatedAt' => Carbon::now()->format('d M Y H:i'),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('laporan-asset-health-' . date('Y-m-d-His') . '.pdf');
    }

    /**
     * Export Pengaduan ke PDF
     */
    public function pengaduan(Request $request)
    {
        $query = \App\Models\Pengaduan::with(['user', 'sarpras', 'lokasi']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $pengaduan = $query->latest()->limit(500)->get();

        $pdf = Pdf::loadView('exports.pengaduan', [
            'pengaduan' => $pengaduan,
            'filters' => $request->only(['status', 'search']),
            'generatedAt' => Carbon::now()->format('d M Y H:i'),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pengaduan-' . date('Y-m-d-His') . '.pdf');
    }
}
