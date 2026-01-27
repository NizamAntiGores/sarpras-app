<?php

use App\Http\Controllers\BarangHilangController;
use App\Http\Controllers\BarangRusakController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SarprasController;
use App\Http\Controllers\SarprasUnitController;
use App\Http\Controllers\UserController;
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
// NOTIFICATION ROUTES (Semua user yang login)
// =============================================
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/read', [
        NotificationController::class,
        'markAsRead'
    ])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [
        NotificationController::class,
        'markAllAsRead'
    ])->name('notifications.mark-all-read');
    Route::get('/notifications/unread-count', [
        NotificationController::class,
        'unreadCount'
    ])->name('notifications.unread-count');

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

        // =============================================
// SARPRAS UNIT ROUTES (Nested under Sarpras)
// =============================================
        Route::get('/sarpras/{sarpras}/units', [SarprasUnitController::class, 'index'])->name('sarpras.units.index');
        Route::get('/sarpras/{sarpras}/units/create', [SarprasUnitController::class, 'create'])->name('sarpras.units.create');
        Route::post('/sarpras/{sarpras}/units', [SarprasUnitController::class, 'store'])->name('sarpras.units.store');
        Route::get('/sarpras/{sarpras}/units/{unit}', [SarprasUnitController::class, 'show'])->name('sarpras.units.show');
        Route::get('/sarpras/{sarpras}/units/{unit}/edit', [SarprasUnitController::class, 'edit'])->name('sarpras.units.edit');
        Route::put('/sarpras/{sarpras}/units/{unit}', [SarprasUnitController::class, 'update'])->name('sarpras.units.update');
        Route::delete('/sarpras/{sarpras}/units/{unit}', [
            SarprasUnitController::class,
            'destroy'
        ])->name('sarpras.units.destroy');
        Route::post('/sarpras/{sarpras}/units/bulk-update-kondisi', [
            SarprasUnitController::class,
            'bulkUpdateKondisi'
        ])->name('sarpras.units.bulk-update-kondisi');

        // =============================================
// MAINTENANCE ROUTES
// =============================================
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
        Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
        Route::get('/maintenance/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenance.show');
        Route::get('/maintenance/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('maintenance.edit');
        Route::put('/maintenance/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenance.update');
        Route::delete('/maintenance/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
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

        // =============================================
// LAPORAN ROUTES
// =============================================
        Route::get('/laporan/asset-health', [
            App\Http\Controllers\LaporanController::class,
            'assetHealth'
        ])->name('laporan.asset-health');
    });

    // =============================================
// PEMINJAMAN ROUTES
// =============================================

    // Create & Store: Khusus Peminjam, Guru, Siswa
    Route::middleware('role:peminjam,guru,siswa')->group(function () {
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
        Route::post('/pengembalian/lookup-qr', [
            PengembalianController::class,
            'lookupByQrCode'
        ])->name('pengembalian.lookup-qr');
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
        Route::patch('/barang-rusak/{sarpras}/perbaiki', [
            BarangRusakController::class,
            'perbaiki'
        ])->name('barang-rusak.perbaiki');
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
// PENGADUAN ROUTES
// =============================================

    // Create & Store: Peminjam, Guru, Siswa Only
    Route::middleware('role:peminjam,guru,siswa')->group(function () {
        Route::get('/pengaduan/create', [PengaduanController::class, 'create'])->name('pengaduan.create');
        Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');
    });

    // Edit & Update: Admin & Petugas Only
    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('/pengaduan/{pengaduan}/edit', [PengaduanController::class, 'edit'])->name('pengaduan.edit');
        Route::put('/pengaduan/{pengaduan}', [PengaduanController::class, 'update'])->name('pengaduan.update');
    });

    // Index & Show: Semua user
    Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/{pengaduan}', [PengaduanController::class, 'show'])->name('pengaduan.show');

    // =============================================
// USER MANAGEMENT ROUTES (Admin only)
// =============================================
    Route::middleware('role:admin')->group(function () {
        // =============================================
// ACTIVITY LOGS (Admin only)
// =============================================
// activity
        Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');

        // =============================================
// TRASH ROUTES (Admin only)
// =============================================
        Route::get('/trash', [App\Http\Controllers\TrashController::class, 'index'])->name('trash.index');
        Route::patch('/trash/unit/{id}/restore', [
            App\Http\Controllers\TrashController::class,
            'restoreUnit'
        ])->name('trash.restore');
        Route::patch('/trash/sarpras/{id}/restore', [
            App\Http\Controllers\TrashController::class,
            'restoreSarpras'
        ])->name('trash.sarpras.restore');
        Route::patch('/trash/lokasi/{id}/restore', [
            App\Http\Controllers\TrashController::class,
            'restoreLokasi'
        ])->name('trash.lokasi.restore');
        Route::patch('/trash/kategori/{id}/restore', [
            App\Http\Controllers\TrashController::class,
            'restoreKategori'
        ])->name('trash.kategori.restore');

        Route::resource('users', UserController::class)->except(['show']);
    });
});

require __DIR__ . '/auth.php';