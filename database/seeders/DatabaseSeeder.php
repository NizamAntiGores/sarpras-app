<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Sarpras;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =============================================
        // USERS SEEDER
        // =============================================
        
        // 1. Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@smk.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'kontak' => '081234567890',
            'email_verified_at' => now(),
        ]);

        // 2. Petugas
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'petugas@smk.sch.id',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'kontak' => '081234567891',
            'email_verified_at' => now(),
        ]);

        // 3. Peminjam/Siswa
        User::create([
            'name' => 'Ahmad Rizki',
            'email' => 'siswa@smk.sch.id',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
            'kontak' => '081234567892',
            'email_verified_at' => now(),
        ]);

        // =============================================
        // KATEGORI SEEDER
        // =============================================
        
        $kategori1 = Kategori::create([
            'nama_kategori' => 'Elektronik',
        ]);

        $kategori2 = Kategori::create([
            'nama_kategori' => 'Furniture',
        ]);

        $kategori3 = Kategori::create([
            'nama_kategori' => 'Alat Olahraga',
        ]);

        // =============================================
        // SARPRAS SEEDER
        // =============================================
        
        Sarpras::create([
            'kode_barang' => 'ELK-001',
            'nama_barang' => 'Proyektor Epson EB-X51',
            'kategori_id' => $kategori1->id,
            'lokasi' => 'Ruang Multimedia',
            'stok' => 5,
            'kondisi_awal' => 'baik',
            'status_barang' => 'tersedia',
        ]);

        Sarpras::create([
            'kode_barang' => 'ELK-002',
            'nama_barang' => 'Laptop Lenovo ThinkPad',
            'kategori_id' => $kategori1->id,
            'lokasi' => 'Lab Komputer',
            'stok' => 20,
            'kondisi_awal' => 'baik',
            'status_barang' => 'tersedia',
        ]);

        Sarpras::create([
            'kode_barang' => 'FRN-001',
            'nama_barang' => 'Meja Lipat Portable',
            'kategori_id' => $kategori2->id,
            'lokasi' => 'Gudang Utama',
            'stok' => 30,
            'kondisi_awal' => 'baik',
            'status_barang' => 'tersedia',
        ]);

        Sarpras::create([
            'kode_barang' => 'FRN-002',
            'nama_barang' => 'Kursi Plastik',
            'kategori_id' => $kategori2->id,
            'lokasi' => 'Gudang Utama',
            'stok' => 100,
            'kondisi_awal' => 'baik',
            'status_barang' => 'tersedia',
        ]);

        Sarpras::create([
            'kode_barang' => 'OLR-001',
            'nama_barang' => 'Bola Voli Mikasa',
            'kategori_id' => $kategori3->id,
            'lokasi' => 'Ruang Olahraga',
            'stok' => 10,
            'kondisi_awal' => 'baik',
            'status_barang' => 'tersedia',
        ]);

        $this->command->info('Seeder berhasil dijalankan!');
        $this->command->info('========================================');
        $this->command->info('Akun yang tersedia:');
        $this->command->info('Admin    : admin@smk.sch.id / password');
        $this->command->info('Petugas  : petugas@smk.sch.id / password');
        $this->command->info('Peminjam : siswa@smk.sch.id / password');
        $this->command->info('========================================');
    }
}
