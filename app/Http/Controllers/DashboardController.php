<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sarpras;
use App\Models\SarprasUnit;
use App\Models\Peminjaman;
use App\Models\Maintenance;
use App\Models\Kategori;
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
            $top5Barang = Sarpras::withCount(['units as total_dipinjam' => function ($query) {
                    $query->whereHas('peminjamanDetails.peminjaman', function ($q) {
                        $q->whereIn('status', ['disetujui', 'selesai']);
                    });
                }])
                ->orderByDesc('total_dipinjam')
                ->limit(5)
                ->get(['id', 'nama_barang', 'kode_barang']);
            
            // Barang dengan Stok Menipis (unit tersedia <= 3)
            $stokMenipis = Sarpras::withCount(['units as available_count' => function ($query) {
                    $query->bisaDipinjam();
                }])
                ->having('available_count', '<=', 3)
                ->having('available_count', '>', 0)
                ->get();
            
            // Barang Habis (tidak ada unit tersedia)
            $stokHabis = Sarpras::withCount(['units as available_count' => function ($query) {
                    $query->bisaDipinjam();
                }])
                ->having('available_count', '=', 0)
                ->get();
            
            // Maintenance aktif
            $maintenanceAktif = Maintenance::sedangBerlangsung()
                ->with(['sarprasUnit.sarpras', 'petugas'])
                ->orderBy('tanggal_mulai', 'desc')
                ->limit(5)
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
                    ->limit(5)
                    ->get(),
                
                // Tier 2: Top 5 Barang & Stok Alert
                'top5Barang' => $top5Barang,
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
                ->withCount(['units as available_count' => function ($query) {
                    $query->bisaDipinjam();
                }])
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
                'totalKatalog' => Sarpras::withCount(['units as available_count' => function ($query) {
                        $query->bisaDipinjam();
                    }])
                    ->having('available_count', '>', 0)
                    ->count(),
                
                // Riwayat peminjaman
                'riwayatPeminjaman' => $riwayatPeminjaman,
            ];
        }

        return view('dashboard', compact('data'));
    }
}
