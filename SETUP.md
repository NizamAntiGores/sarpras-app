# 🚀 Panduan Instalasi & Setup

Panduan ini berisi langkah-langkah untuk menjalankan aplikasi Sarpras di komputer lokal Anda.

## Prasyarat
Pastikan komputer Anda sudah terinstall:
- [PHP](https://www.php.net/downloads) (Versi 8.1 atau lebih baru)
- [Composer](https://getcomposer.org/)
- [Node.js & NPM](https://nodejs.org/)
- [MySQL](https://www.mysql.com/) (Bisa via XAMPP/Laragon)
- [Git](https://git-scm.com/)

---

## 🛠️ Langkah Instalasi

### 1. Clone Repository
Jika belum, clone repository ini ke komputer Anda:
```bash
git clone https://github.com/username/sarpras-app.git
cd sarpras-app
```

### 2. Install Dependency
Install semua library PHP dan JavaScript yang dibutuhkan:
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
Salin file konfigurasi `.env` dan generate application key:
```bash
cp .env.example .env
php artisan key:generate
```
*(Untuk Windows CMD, gunakan `copy .env.example .env`)*

### 4. Setup Database
1. Buat database baru di MySQL (misal: `sarpras_db`).
2. Buka file `.env` dan sesuaikan pengaturan berikut:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sarpras_db
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi & Seeding Data
Jalankan perintah ini untuk membuat tabel dan mengisi data awal (dummy data):
```bash
php artisan migrate --seed
```

### 6. Link Storage
Agar file gambar yang diupload bisa diakses publik:
```bash
php artisan storage:link
```

### 7. Jalankan Aplikasi
Jalankan server lokal Laravel dan Vite (untuk asset frontend):
```bash
php artisan serve
npm run dev
```
Akses aplikasi di: [http://localhost:8000](http://localhost:8000)

---

## 🔐 Akun Demo (Default)

Gunakan akun berikut untuk login setelah menjalankan seeding:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@smk.sch.id` | `password` |
| **Petugas** (Lab/Gudang) | `petugas@smk.sch.id` | `password` |
| **Peminjam** (Siswa/Guru) | `siswa@smk.sch.id` | `password` |
