# 🏫 Sarpras SMK - Sistem Informasi Sarana & Prasarana

Sistem manajemen inventaris, peminjaman, dan pengaduan sarana prasarana sekolah berbasis Web. Aplikasi ini dirancang untuk mempermudah pengelolaan aset sekolah secara digital, transparan, dan efisien.

---

## ✨ Fitur Unggulan

### 📦 Manajemen Aset (Sarpras)
- **Katalog Terpadu**: Visualisasi aset dengan filter kategori dan lokasi.
- **Unit Tracking**: Pengelolaan barang berdasarkan unit unik (misal: Laptop-01, Laptop-02).
- **Stok & Kondisi**: Pemantauan real-time stok tersedia, rusak, atau dipinjam.
- **Alert Stok Menipis**: Notifikasi otomatis di dashboard jika stok barang di bawah ambang batas.

### 📋 Peminjaman & Pengembalian
- **Sistem QR Code**: Setiap peminjaman memiliki QR Code unik untuk verifikasi cepat.
- **Validasi Status**: Peminjaman otomatis ditolak jika unit tidak tersedia atau sedang dalam perbaikan.
- **Laporan Kondisi**: Upload bukti foto pada saat pengambilan dan pengembalian barang.
- **Histori Lengkap**: Rekam jejak peminjaman untuk setiap pengguna.

### ⚠️ Pengaduan Kerusakan (Maintenance)
- **Laporan Cepat**: Siswa/Guru dapat melaporkan kerusakan sarpras dengan menyertakan deskripsi dan foto.
- **Workflow Status**: Admin dapat mengubah status laporan dari `pending` ke `proses` hingga `selesai`.

### 📊 Dashboard & Monitoring
- **Statistik Visual**: Grafik jumlah peminjaman, sarpras terpopuler, dan kondisi aset.
- **Ringkasan Cepat**: Widget informatif untuk status hari ini.

### 👤 Manajemen Pengguna (Multi-Role)
- **Admin**: Akses penuh ke semua fitur dan master data.
- **Petugas**: Fokus pada validasi peminjaman dan pengembalian.
- **Peminjam (Siswa/Guru)**: Akses katalog dan pengajuan peminjaman/pengaduan.
- **Soft Deletes**: Pengamanan data user agar tidak terhapus permanen secara tidak sengaja.

---

## 🛠️ Teknologi yang Digunakan

- **Framework**: Laravel 10 (PHP 8.1+)
- **Frontend**: Vite, Tailwind CSS, Blade Templates
- **Database**: MySQL / MariaDB
- **Library Utama**:
  - `simple-qrcode`: Untuk generate QR Code.
  - `Laravel Breeze`: Untuk sistem autentikasi.

---

## 🚀 Setup & Instalasi

### 📋 Prasyarat
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL Server (XAMPP / Laragon)

### 💻 Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/NizamAntiGores/sarpras-app.git
   cd sarpras-app
   ```

2. **Instalasi Dependency**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di file `.env`.*

4. **Migrasi & Seeding**
   ```bash
   php artisan migrate --seed
   ```

5. **Storage Link**
   ```bash
   php artisan storage:link
   ```

6. **Menjalankan Aplikasi**
   Jalankan server Laravel:
   ```bash
   php artisan serve
   ```
   Jalankan asset bundler (Vite):
   ```bash
   npm run dev
   ```

---

## 🔐 Akun Default (Login)

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@smk.sch.id` | `password` |
| **Petugas** | `petugas@smk.sch.id` | `password` |
| **Peminjam** | `siswa@smk.sch.id` | `password` |

---

## 📄 Lisensi
Dibuat untuk keperluan internal SMK. Hak Cipta &copy; 2024.
