# 🏫 Sarpras SMK - Sistem Informasi Sarana & Prasarana

Sistem manajemen inventaris, peminjaman, dan pengaduan sarana prasarana sekolah berbasis Web. Aplikasi ini dirancang untuk mempermudah pengelolaan aset sekolah secara digital, transparan, dan efisien.

## ✨ Fitur Utama

### 📦 Manajemen Aset (Sarpras)
- **Katalog Terpadu**: Visualisasi aset dengan filter kategori dan lokasi.
- **Unit Tracking**: Pengelolaan barang unit per unit (tracking individual).
- **Stok & Kondisi**: Pemantauan real-time stok tersedia, barang rusak, atau sedang dipinjam.
- **Notifikasi Stok**: Alert otomatis jika stok barang habis pakai menipis.

### � Peminjaman & Pengembalian Digital
- **QR Code System**: Peminjaman dan pengembalian aset menggunakan scan QR Code.
- **Validasi Otomatis**: Sistem menolak peminjaman jika stok habis atau unit sedang maintenance.
- **Bukti Kondisi**: Upload foto kondisi barang saat diambil dan dikembalikan.
- **Riwayat Transaksi**: Rekam jejak lengkap siapa meminjam apa dan kapan.

### 🛠️ Layanan & Perbaikan (Maintenance)
- **Tiket Pengaduan**: Siswa/Guru dapat melaporkan kerusakan fasilitas.
- **Status Tracking**: Pantau progress perbaikan (Pending ➝ Diproses ➝ Selesai).
- **Laporan Kerusakan**: Dokumentasi kerusakan aset beserta foto bukti.

### � Manajemen Pengguna & Keamanan
- **Multi-Role**: Hak akses berbeda untuk Admin, Petugas (Gudang/Lab), dan Peminjam.
- **Keamanan Data**: Menggunakan enkripsi password dan proteksi rute (Auth/Guard).
- **Soft Deletes**: Fitur keamanan untuk memulihkan data yang tidak sengaja terhapus.

### 📄 Laporan & Cetak
- **Cetak Laporan**: Generate laporan inventaris dan peminjaman dalam format PDF.
- **Labeling**: Cetak label QR Code untuk ditempel pada aset fisik.

---

## � Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan teknologi modern untuk memastikan performa yang cepat dan tampilan yang responsif.

### Backend
- **Laravel 10** (PHP 8.1+) - Framework utama aplikasi.
- **MySQL / MariaDB** - Database manajemen.
- **Laravel Breeze** - Implementasi sistem autentikasi yang aman.
- **Laravel Sanctum** - Autentikasi API (jika diperlukan pengembangan mobile).

### Frontend
- **Blade Templates** - Templating engine bawaan Laravel.
- **Tailwind CSS** - Framework CSS untuk desain antarmuka modern.
- **Alpine.js** - Framework JavaScript ringan untuk interaktivitas UI.
- **Vite** - Build tool untuk aset frontend yang super cepat.

### Library Pendukung
- **`simplesoftwareio/simple-qrcode`**: Generator QR Code untuk aset dan transaksi.
- **`barryvdh/laravel-dompdf`**: Pembuatan laporan PDF.
