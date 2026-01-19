# Aplikasi Sarpras Gweh

Sistem Peminjaman Sarana dan Prasarana berbasis Laravel.

## Fitur Utama

- 📦 Manajemen Inventaris (CRUD Sarpras)
- 📋 Peminjaman & Pengembalian Barang
- 👥 Multi-role: Admin, Petugas, Peminjam
- 📊 Dashboard dengan statistik
- 📄 Cetak bukti peminjaman (PDF + QR Code)
- 📸 Upload foto kondisi barang

---

///## Setup

### Syarat

Pastikan komputer sudah terinstall:
1. **PHP 8.1+** (cek dengan: `php -v`)
2. **Composer** (cek dengan: `composer -V`)
3. **Node.js & NPM** (cek dengan: `node -v` dan `npm -v`)
4. **XAMPP/Laragon** (untuk MySQL)

### Langkah-langkah Instalasi

#### 1. Clone Repo

```bash
git clone https://github.com/USERNAME/sarpras-smk.git
cd sarpras-smk
```

#### 2. Install Dependencies

```bash
composer install
npm install
```

#### 3. Setup Environment

```bash
# Copy file konfigurasi
copy .env.example .env

# Generate app key
php artisan key:generate
```

#### 4. Buat Database

Buka **phpMyAdmin** atau command line MySQL:

```sql
CREATE DATABASE db_sarpras_smk;
```

#### 5. Jalankan Migrasi & Seeder

```bash
php artisan migrate --seed
```

Jika berhasil, akan muncul akun default:
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@smk.sch.id | password |
| Petugas | petugas@smk.sch.id | password |
| Peminjam | siswa@smk.sch.id | password |

#### 6. Buat Storage Link

```bash
php artisan storage:link
```

#### 7. Jalankan Aplikasi

Buka **2 terminal** terpisah:

**Terminal 1 - Backend:**
```bash
php artisan serve
```

**Terminal 2 - Frontend (Vite):**
```bash
npm run dev
```

#### 8. Buka Browser

Akses: [http://localhost:8000](http://localhost:8000)


## Teknologi

- Laravel 10
- MySQL
- Vite + Tailwind CSS
- DomPDF + Simple QR Code///
