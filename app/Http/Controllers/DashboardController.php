<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Maintenance;
use App\Models\Peminjaman;
use App\Models\Sarpras;
use App\Models\SarprasUnit;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $data = [];

        // =============================================
        // PERHITUNGAN STOK (Unit-based)
        // =============================================
        // Rumus:
        // - Total Unit = Semua unit yang tidak dihapusbukukan
        // - Tersedia = Unit dengan status 'tersedia' DAN kondisi 'baik' (Siap Pakai)
        // - Dipinjam = Unit dengan status 'dipinjam'
        // - Rusak = Unit dengan kondisi != 'baik' yang TIDAK sedang dipinjam (untuk menghindari double count di dashboard)

        $totalUnit = SarprasUnit::aktif()->count();
        $tersedia = SarprasUnit::where('status', SarprasUnit::STATUS_TERSEDIA)
            ->where('kondisi', SarprasUnit::KONDISI_BAIK)->count();
        $dipinjam = SarprasUnit::where('status', SarprasUnit::STATUS_DIPINJAM)->count();
        $rusak = SarprasUnit::aktif()->where('kondisi', '!=', SarprasUnit::KONDISI_BAIK)
            ->where('status', '!=', SarprasUnit::STATUS_DIPINJAM)->count();
        $maintenance = SarprasUnit::where('status', SarprasUnit::STATUS_MAINTENANCE)->count();

        if ($user->role === 'admin') {
            // Dashboard Admin: Statistik lengkap

            // Top 5 Barang Paling Sering Dipinjam (berdasarkan detail count)
            $top5Barang = Sarpras::withCount([
                'units as total_dipinjam' => function ($query) {
                    $query->whereHas('peminjamanDetails.peminjaman', function ($q) {
                        $q->whereIn('status', ['disetujui', 'selesai']);
                    });
                }
            ])
                ->orderByDesc('total_dipinjam')
                ->limit(5) // Limit to top 5 specifically
                ->get(['id', 'nama_barang', 'kode_barang']);

            // Statistik Peminjaman Trend Chart
            $trendFilter = request()->get('trend_filter', 'weekly'); // default weekly
            $chartLabels = [];
            $chartData = [];

            if ($trendFilter === 'yearly') {
                // Per Bulan dalam Tahun Ini
                $queryData = Peminjaman::selectRaw('MONTH(tgl_pinjam) as bulan, COUNT(*) as total')
                    ->whereYear('tgl_pinjam', date('Y'))
                    ->groupBy('bulan')
                    ->pluck('total', 'bulan')
                    ->toArray();

                $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', ' Okt', 'Nov', 'Des'];
                for ($i = 1; $i <= 12; $i++) {
                    $chartData[] = $queryData[$i] ?? 0;
                }

            } elseif ($trendFilter === 'monthly') {
                // Per Hari dalam Bulan Ini
                $daysInMonth = now()->daysInMonth;
                $queryData = Peminjaman::selectRaw('DAY(tgl_pinjam) as hari, COUNT(*) as total')
                    ->whereMonth('tgl_pinjam', date('m'))
                    ->whereYear('tgl_pinjam', date('Y'))
                    ->groupBy('hari')
                    ->pluck('total', 'hari')
                    ->toArray();

                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $chartLabels[] = (string) $i;
                    $chartData[] = $queryData[$i] ?? 0;
                }

            } else { // weekly (default)
                // Per Hari dalam Minggu Ini (Senin - Minggu)
                $queryData = Peminjaman::selectRaw('DATE(tgl_pinjam) as tanggal, COUNT(*) as total')
                    ->whereBetween('tgl_pinjam', [now()->startOfWeek(), now()->endOfWeek()])
                    ->groupBy('tanggal')
                    ->pluck('total', 'tanggal')
                    ->toArray();

                $chartLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                $startOfWeek = now()->startOfWeek();
                for ($i = 0; $i < 7; $i++) {
                    $date = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
                    $chartData[] = $queryData[$date] ?? 0;
                }
            }

            // Barang dengan Stok Menipis (unit tersedia <= 3)
            $stokMenipis = Sarpras::withCount([
                'units as available_count' => function ($query) {
                    $query->bisaDipinjam();
                }
            ])
                ->having('available_count', '<=', 3)
                ->having('available_count', '>', 0)
                ->limit(50)
                ->get();

            // Barang Habis (tidak ada unit tersedia)
            $stokHabis = Sarpras::withCount([
                'units as available_count' => function ($query) {
                    $query->bisaDipinjam();
                }
            ])
                ->having('available_count', '=', 0)
                ->limit(50)
                ->get();

            // Maintenance aktif
            $maintenanceAktif = Maintenance::sedangBerlangsung()
                ->with(['sarprasUnit.sarpras', 'petugas'])
                ->orderBy('tanggal_mulai', 'desc')
                ->limit(20)
                ->get();

            $data = [
                // Statistik Inventaris (Unit-based)
                'totalUnit' => $totalUnit,
                'tersedia' => $tersedia,
                'dipinjam' => $dipinjam,
                'maintenance' => $maintenance,
                'rusak' => $rusak,

                // Statistik User
                'totalUsers' => User::count(),
                'totalAdmin' => User::where('role', 'admin')->count(),
                'totalPetugas' => User::where('role', 'petugas')->count(),
                'totalPeminjam' => User::where('role', 'peminjam')->count(),

                // Statistik Sarpras
                'totalJenisSarpras' => Sarpras::count(),
                'totalKategori' => Kategori::count(),

                // Statistik Peminjaman
                'totalPeminjaman' => Peminjaman::count(),
                'peminjamanMenunggu' => Peminjaman::where('status', 'menunggu')->count(),
                'peminjamanDisetujui' => Peminjaman::where('status', 'disetujui')->count(),
                'peminjamanSelesai' => Peminjaman::where('status', 'selesai')->count(),

                // Recent Activity
                'recentPeminjaman' => Peminjaman::with(['user', 'details.sarprasUnit.sarpras'])
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get(),

                // Tier 2: Top 5 Barang & Stok Alert
                'top5Barang' => $top5Barang,
                'chartTrend' => [
                    'labels' => $chartLabels,
                    'data' => $chartData,
                    'filter' => $trendFilter
                ],
                'stokMenipis' => $stokMenipis,
                'stokHabis' => $stokHabis,

                // Maintenance
                'maintenanceAktif' => $maintenanceAktif,
                'totalMaintenanceAktif' => Maintenance::sedangBerlangsung()->count(),
            ];
        } elseif ($user->role === 'petugas') {
            // Dashboard Petugas: Fokus pada verifikasi
            $data = [
                // Statistik Inventaris
                'totalUnit' => $totalUnit,
                'tersedia' => $tersedia,
                'dipinjam' => $dipinjam,
                'maintenance' => $maintenance,

                // Statistik Peminjaman
                'peminjamanMenunggu' => Peminjaman::where('status', 'menunggu')->count(),
                'peminjamanMenungguHariIni' => Peminjaman::where('status', 'menunggu')
                    ->whereDate('created_at', today())
                    ->count(),
                'peminjamanDisetujui' => Peminjaman::where('status', 'disetujui')->count(),

                // Maintenance aktif
                'totalMaintenanceAktif' => Maintenance::sedangBerlangsung()->count(),

                // Recent Activity
                'recentPeminjaman' => Peminjaman::with(['user', 'details.sarprasUnit.sarpras'])
                    ->where('status', 'menunggu')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
            ];
        } else {
            // Dashboard Peminjam/Siswa - dengan katalog dan riwayat lengkap

            // Data katalog barang (untuk ditampilkan langsung)
            $katalogBarang = Sarpras::with(['kategori'])
                ->withCount([
                    'units as available_count' => function ($query) {
                        $query->bisaDipinjam();
                    }
                ])
                ->having('available_count', '>', 0)
                ->orderBy('nama_barang')
                ->limit(8)
                ->get();

            // Riwayat peminjaman terbaru (lebih lengkap)
            $riwayatPeminjaman = Peminjaman::with('details.sarprasUnit.sarpras')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $data = [
                'totalPeminjamanSaya' => Peminjaman::where('user_id', $user->id)->count(),
                'peminjamanMenunggu' => Peminjaman::where('user_id', $user->id)
                    ->where('status', 'menunggu')->count(),
                'peminjamanDisetujui' => Peminjaman::where('user_id', $user->id)
                    ->where('status', 'disetujui')->count(),
                'peminjamanSelesai' => Peminjaman::where('user_id', $user->id)
                    ->where('status', 'selesai')->count(),

                // Katalog barang untuk ditampilkan langsung
                'katalogBarang' => $katalogBarang,
                'totalKatalog' => Sarpras::withCount([
                    'units as available_count' => function ($query) {
                        $query->bisaDipinjam();
                    }
                ])
                    ->having('available_count', '>', 0)
                    ->count(),

                // Riwayat peminjaman
                'riwayatPeminjaman' => $riwayatPeminjaman,
            ];
        }

        return view('dashboard', compact('data'));
    }
}
