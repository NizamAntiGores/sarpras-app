---
description: Langkah-langkah instalasi dan setup awal aplikasi Sarpras di komputer baru
---

# 🚀 Workflow Setup Aplikasi Sarpras

Ikuti langkah ini jika Anda baru saja melakukan clone repository ini di komputer baru.

### 1. Instalasi Dependency
Jalankan perintah berikut untuk menginstal library PHP dan Javascript:

// turbo
```powershell
composer install
npm install
```

### 2. Konfigurasi Environment
Salin file `.env` dan buat kunci aplikasi baru:

// turbo
```powershell
copy .env.example .env
php artisan key:generate
```

*Catatan: Setelah perintah di atas, pastikan Anda sudah membuat database kosong di MySQL dan memperbarui pengaturan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di file `.env`.*

### 3. Migrasi Database & Seeding
Jalankan migrasi untuk membuat tabel dan mengisi data awal (termasuk user):

// turbo
```powershell
php artisan migrate --seed
```

### 4. Link Storage & Jalankan Aplikasi
Buat link untuk folder upload dan jalankan server:

// turbo
```powershell
php artisan storage:link
php artisan serve
```

---

## 🔐 Informasi Akun Default (Rahasia)

Setelah menjalankan `--seed`, Anda bisa login dengan akun berikut:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@smk.sch.id` | `password` |
| **Petugas** | `petugas@smk.sch.id` | `password` |
| **Peminjam** | `siswa@smk.sch.id` | `password` |
