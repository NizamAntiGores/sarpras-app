<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sarpras;
use App\Models\Peminjaman;
use App\Models\Kategori;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $data = [];

        // =============================================
        // PERHITUNGAN STOK (Quantity-based)
        // =============================================
        // Rumus:
        // - Stok Tersedia = Sum stok di tabel sarpras (barang bagus di lemari)
        // - Stok Rusak = Sum stok_rusak di tabel sarpras
        // - Sedang Dipinjam = Sum jumlah_pinjam dari peminjaman dengan status 'disetujui'
        // - Total Inventaris = Stok Tersedia + Stok Rusak + Sedang Dipinjam
        
        $stokTersedia = Sarpras::sum('stok');
        $stokRusak = Sarpras::sum('stok_rusak');
        $sedangDipinjam = Peminjaman::where('status', 'disetujui')->sum('jumlah_pinjam');
        $totalInventaris = $stokTersedia + $stokRusak + $sedangDipinjam;

        if ($user->role === 'admin') {
            // Dashboard Admin: Statistik lengkap
            
            // Top 5 Barang Paling Sering Dipinjam
            $top5Barang = Sarpras::withCount(['peminjaman as total_dipinjam' => function ($query) {
                    $query->whereIn('status', ['disetujui', 'selesai']);
                }])
                ->orderByDesc('total_dipinjam')
                ->limit(5)
                ->get(['id', 'nama_barang', 'kode_barang']);
            
            // Barang dengan Stok Menipis (stok <= 5)
            $stokMenipis = Sarpras::with('lokasi')
                ->where('stok', '<=', 5)
                ->where('stok', '>', 0)
                ->orderBy('stok')
                ->get();
            
            // Barang Habis (stok = 0)
            $stokHabis = Sarpras::with('lokasi')
                ->where('stok', 0)
                ->get();
            
            $data = [
                // Statistik Inventaris
                'stokTersedia' => $stokTersedia,
                'stokRusak' => $stokRusak,
                'sedangDipinjam' => $sedangDipinjam,
                'totalInventaris' => $totalInventaris,
                
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
                'recentPeminjaman' => Peminjaman::with(['user', 'sarpras'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
                
                // Tier 2: Top 5 Barang & Stok Alert
                'top5Barang' => $top5Barang,
                'stokMenipis' => $stokMenipis,
                'stokHabis' => $stokHabis,
            ];
        } elseif ($user->role === 'petugas') {
            // Dashboard Petugas: Fokus pada verifikasi
            $data = [
                // Statistik Inventaris
                'stokTersedia' => $stokTersedia,
                'stokRusak' => $stokRusak,
                'sedangDipinjam' => $sedangDipinjam,
                'totalInventaris' => $totalInventaris,
                
                // Statistik Peminjaman
                'peminjamanMenunggu' => Peminjaman::where('status', 'menunggu')->count(),
                'peminjamanMenungguHariIni' => Peminjaman::where('status', 'menunggu')
                    ->whereDate('created_at', today())
                    ->count(),
                'peminjamanDisetujui' => Peminjaman::where('status', 'disetujui')->count(),
                
                // Recent Activity
                'recentPeminjaman' => Peminjaman::with(['user', 'sarpras'])
                    ->where('status', 'menunggu')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
            ];
        } else {
            // Dashboard Peminjam/Siswa - dengan katalog dan riwayat lengkap
            
            // Data katalog barang (untuk ditampilkan langsung)
            $katalogBarang = Sarpras::with(['kategori', 'lokasi'])
                ->where('stok', '>', 0)
                ->orderBy('nama_barang')
                ->limit(8)
                ->get();
            
            // Riwayat peminjaman terbaru (lebih lengkap)
            $riwayatPeminjaman = Peminjaman::with('sarpras')
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
                'totalKatalog' => Sarpras::where('stok', '>', 0)->count(),
                
                // Riwayat peminjaman
                'riwayatPeminjaman' => $riwayatPeminjaman,
            ];
        }

        return view('dashboard', compact('data'));
    }
}
