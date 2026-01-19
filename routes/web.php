<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SarprasController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BarangHilangController;
use App\Http\Controllers\BarangRusakController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // =============================================
    // PROFILE ROUTES (Semua user yang login)
    // =============================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =============================================
    // SARPRAS ROUTES (Admin & Petugas)
    // =============================================
    Route::middleware('role:admin,petugas')->group(function () {
        // Index, Create, Store, Show, Edit, Update - Admin & Petugas
        Route::get('/sarpras', [SarprasController::class, 'index'])->name('sarpras.index');
        Route::get('/sarpras/create', [SarprasController::class, 'create'])->name('sarpras.create');
        Route::post('/sarpras', [SarprasController::class, 'store'])->name('sarpras.store');
        Route::get('/sarpras/{sarpras}', [SarprasController::class, 'show'])->name('sarpras.show');
        Route::get('/sarpras/{sarpras}/edit', [SarprasController::class, 'edit'])->name('sarpras.edit');
        Route::put('/sarpras/{sarpras}', [SarprasController::class, 'update'])->name('sarpras.update');
        Route::patch('/sarpras/{sarpras}', [SarprasController::class, 'update']);
    });

    // Destroy Sarpras - Hanya Admin
    Route::middleware('role:admin')->group(function () {
        Route::delete('/sarpras/{sarpras}', [SarprasController::class, 'destroy'])->name('sarpras.destroy');
        
        // =============================================
        // LOKASI ROUTES (Admin only)
        // =============================================
        Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
        Route::get('/lokasi/create', [LokasiController::class, 'create'])->name('lokasi.create');
        Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store');
        Route::get('/lokasi/{lokasi}/edit', [LokasiController::class, 'edit'])->name('lokasi.edit');
        Route::put('/lokasi/{lokasi}', [LokasiController::class, 'update'])->name('lokasi.update');
        Route::delete('/lokasi/{lokasi}', [LokasiController::class, 'destroy'])->name('lokasi.destroy');
        
        // =============================================
        // KATEGORI ROUTES (Admin only)
        // =============================================
        Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
        Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::get('/kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    });

    // =============================================
    // PEMINJAMAN ROUTES
    // =============================================
    
    // Create & Store: Khusus Peminjam (siswa)
    Route::middleware('role:peminjam')->group(function () {
        Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
        Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    });
    
    // Edit & Update: Admin & Petugas (untuk approve/reject/selesai)
    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('/peminjaman/{peminjaman}/edit', [PeminjamanController::class, 'edit'])->name('peminjaman.edit');
        Route::put('/peminjaman/{peminjaman}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
        Route::patch('/peminjaman/{peminjaman}', [PeminjamanController::class, 'update']);
        
        // Pengembalian Routes
        Route::get('/pengembalian/{peminjaman}/create', [PengembalianController::class, 'create'])->name('pengembalian.create');
        Route::post('/pengembalian/{peminjaman}', [PengembalianController::class, 'store'])->name('pengembalian.store');

        // =============================================
        // BARANG HILANG ROUTES (Admin & Petugas)
        // =============================================
        Route::get('/barang-hilang', [BarangHilangController::class, 'index'])->name('barang-hilang.index');
        Route::put('/barang-hilang/{barangHilang}', [BarangHilangController::class, 'update'])->name('barang-hilang.update');

        // =============================================
        // BARANG RUSAK ROUTES (Admin & Petugas)
        // =============================================
        Route::get('/barang-rusak', [BarangRusakController::class, 'index'])->name('barang-rusak.index');
        Route::patch('/barang-rusak/{sarpras}/perbaiki', [BarangRusakController::class, 'perbaiki'])->name('barang-rusak.perbaiki');
        Route::patch('/barang-rusak/{sarpras}/hapus', [BarangRusakController::class, 'hapus'])->name('barang-rusak.hapus');
    });
    
    // Delete Peminjaman: Khusus Admin only
    Route::middleware('role:admin')->group(function () {
        Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
    });
    
    // Index & Show: Semua user yang login 
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show'])->name('peminjaman.show');


    // =============================================
    // USER MANAGEMENT ROUTES (Admin only)
    // =============================================
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
