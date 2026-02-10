<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Lokasi;
use App\Models\Kategori;
use App\Models\Sarpras;
use App\Models\SarprasUnit;
use App\Models\ItemStock;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\Maintenance;

class FeatureTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeder lengkap untuk testing seluruh fitur.
     */
    public function run(): void
    {
        // 1. Reset Database
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PeminjamanDetail::truncate();
        Peminjaman::truncate();
        Maintenance::truncate();
        ItemStock::truncate();
        SarprasUnit::truncate();
        Sarpras::truncate();
        Lokasi::truncate();
        Kategori::truncate();
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Database cleaned. Starting seeding...');

        // 2. Create Users
        $password = Hash::make('password');

        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => $password,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $petugas = User::create([
            'name' => 'Petugas Sarpras',
            'email' => 'petugas@gmail.com',
            'password' => $password,
            'role' => 'petugas',
            'email_verified_at' => now(),
        ]);

        $user1 = User::create([
            'name' => 'User Siswa',
            'email' => 'user@gmail.com',
            'password' => $password,
            'role' => 'peminjam',
            'email_verified_at' => now(),
        ]);
        
        $guru = User::create([
            'name' => 'Ibu Guru',
            'email' => 'guru@gmail.com',
            'password' => $password,
            'role' => 'guru',
            'email_verified_at' => now(),
        ]);

        // 3. Create Locations
        $locGudang = Lokasi::create([
            'nama_lokasi' => 'Gudang Utama (Non-Storefront)',
            'keterangan' => 'Penyimpanan stok cadangan.',
            'is_storefront' => false,
        ]);

        $locLocker = Lokasi::create([
            'nama_lokasi' => 'Locker Depan (Storefront)',
            'keterangan' => 'Lokasi pengambilan barang mandiri.',
            'is_storefront' => true,
        ]);

        $locLab = Lokasi::create([
            'nama_lokasi' => 'Lab Komputer (Storefront)',
            'keterangan' => 'Barang inventaris lab.',
            'is_storefront' => true,
        ]);

        // 4. Create Categories
        $catElektronik = Kategori::create(['nama_kategori' => 'Elektronik']);
        $catFurniture = Kategori::create(['nama_kategori' => 'Furniture']);
        $catATK = Kategori::create(['nama_kategori' => 'ATK (Habis Pakai)']);

        // 5. Create Sarpras Items
        $laptop = Sarpras::create([
            'kode_barang' => 'EL-LPT-001',
            'nama_barang' => 'Laptop ASUS ROG',
            'kategori_id' => $catElektronik->id,
            'tipe' => 'asset',
            'deskripsi' => 'Laptop gaming untuk keperluan desain.',
            'foto' => null, // Bisa diisi path dummy jika ada
        ]);

        $proyektor = Sarpras::create([
            'kode_barang' => 'EL-PRJ-002',
            'nama_barang' => 'Proyektor Epson',
            'kategori_id' => $catElektronik->id,
            'tipe' => 'asset',
            'deskripsi' => 'Proyektor cadangan.',
        ]);

        $kursi = Sarpras::create([
            'kode_barang' => 'FUR-CHR-003',
            'nama_barang' => 'Kursi Kantor',
            'kategori_id' => $catFurniture->id,
            'tipe' => 'asset',
            'deskripsi' => 'Kursi putar.',
        ]);

        $spidol = Sarpras::create([
            'kode_barang' => 'ATK-SPD-004',
            'nama_barang' => 'Spidol Boardmaker Hitam',
            'kategori_id' => $catATK->id,
            'tipe' => 'bahan',
            'deskripsi' => 'Spidol untuk whiteboard.',
        ]);

        // 6. Create Units & Stocks
        // Units for Laptop (Storefront) - Visible (5 Units)
        // 3 Tersedia, 1 Dipinjam Pending, 1 Dipinjam Ongoing
        for ($i = 1; $i <= 5; $i++) {
            SarprasUnit::create([
                'sarpras_id' => $laptop->id,
                'kode_unit' => "LPT-SF-00{$i}",
                'lokasi_id' => $locLocker->id,
                'kondisi' => 'baik',
                'status' => 'tersedia', // Nanti diupdate oleh logic transaksi
                'tanggal_perolehan' => now()->subMonths(2),
            ]);
        }

        // Units for Laptop (Gudang) - Hidden (2 Units)
        for ($i = 1; $i <= 2; $i++) {
            SarprasUnit::create([
                'sarpras_id' => $laptop->id,
                'kode_unit' => "LPT-GD-00{$i}",
                'lokasi_id' => $locGudang->id,
                'kondisi' => 'baik',
                'status' => 'tersedia',
                'tanggal_perolehan' => now()->subMonths(2),
            ]);
        }

        // 1 Maintenance
        $unitMaintenance = SarprasUnit::create([
            'sarpras_id' => $laptop->id,
            'kode_unit' => "LPT-MT-001",
            'lokasi_id' => $locLab->id,
            'kondisi' => 'rusak_ringan',
            'status' => 'maintenance',
            'tanggal_perolehan' => now()->subMonths(6),
        ]);

        // Units for Proyektor (Gudang Only)
        SarprasUnit::create([
            'sarpras_id' => $proyektor->id,
            'kode_unit' => "PRJ-GD-001",
            'lokasi_id' => $locGudang->id,
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'tanggal_perolehan' => now()->subYears(1),
        ]);

        // Stock for Spidol
        ItemStock::create([
            'sarpras_id' => $spidol->id,
            'lokasi_id' => $locLocker->id,
            'quantity' => 50,
        ]);
        ItemStock::create([
            'sarpras_id' => $spidol->id,
            'lokasi_id' => $locGudang->id,
            'quantity' => 100,
        ]);

        // 7. Create Transactions (Peminjaman)

        // -> CASE 1: STATUS MENUNGGU (Pending)
        $pinjamPending = Peminjaman::create([
            'user_id' => $user1->id,
            'status' => 'menunggu',
            'tgl_pinjam' => now()->addDay(),
            'tgl_kembali_rencana' => now()->addDays(2),
            'keterangan' => 'Keperluan tugas akhir.',
        ]);
        
        $unitPending = SarprasUnit::where('kode_unit', 'LPT-SF-001')->first();
        PeminjamanDetail::create([
            'peminjaman_id' => $pinjamPending->id,
            'sarpras_unit_id' => $unitPending->id,
            'sarpras_id' => $laptop->id,
            'quantity' => 1,
        ]);
        // Note: Unit status fisik tetap 'tersedia' saat pending, tapi logic controller memfilter ini.


        // -> CASE 2: STATUS DISETUJUI (Approved, Not picked up)
        $pinjamApproved = Peminjaman::create([
            'user_id' => $user1->id,
            'status' => 'disetujui',
            'petugas_id' => $petugas->id,
            'tgl_pinjam' => now(), // Hari ini
            'tgl_kembali_rencana' => now()->addDays(3),
            'keterangan' => 'Pinjam untuk event.',
            'qr_code' => 'QR-APP-001',
        ]);
        
        $unitApproved = SarprasUnit::where('kode_unit', 'LPT-SF-002')->first();
        $unitApproved->update(['status' => 'dipinjam']); // System marks as borrowed upon approval

        PeminjamanDetail::create([
            'peminjaman_id' => $pinjamApproved->id,
            'sarpras_unit_id' => $unitApproved->id,
            'sarpras_id' => $laptop->id,
            'quantity' => 1,
        ]);


        // -> CASE 3: STATUS DIPINJAM (Ongoing / Handed Over)
        $pinjamOngoing = Peminjaman::create([
            'user_id' => $user1->id,
            'status' => 'dipinjam', // Handed Over
            'petugas_id' => $petugas->id,
            'tgl_pinjam' => now()->subDay(),
            'tgl_kembali_rencana' => now()->addDays(2),
            'keterangan' => 'Sedang dipakai.',
            'qr_code' => 'QR-ON-001',
            'handover_at' => now()->subDay(),
            'handover_by' => $petugas->id,
        ]);
        
        $unitOngoing = SarprasUnit::where('kode_unit', 'LPT-SF-003')->first();
        $unitOngoing->update(['status' => 'dipinjam']);

        PeminjamanDetail::create([
            'peminjaman_id' => $pinjamOngoing->id,
            'sarpras_unit_id' => $unitOngoing->id,
            'sarpras_id' => $laptop->id,
            'quantity' => 1,
            'handed_over_at' => now()->subDay(),
            'handed_over_by' => $petugas->id,
        ]);


        // -> CASE 4: STATUS SELESAI (History)
        $pinjamSelesai = Peminjaman::create([
            'user_id' => $user1->id,
            'status' => 'selesai',
            'petugas_id' => $petugas->id,
            'tgl_pinjam' => now()->subDays(5),
            'tgl_kembali_rencana' => now()->subDays(4),
            'keterangan' => 'Sudah dikembalikan.',
            'qr_code' => 'QR-DONE-001',
            'handover_at' => now()->subDays(5),
            'handover_by' => $petugas->id,
        ]);
        
        // Unit ini sekarang harusnya BERBEDA dengan unit-unit di atas dan harusnya tersedia
        $unitSelesai = SarprasUnit::where('kode_unit', 'LPT-SF-004')->first();
        // Status unit Selesai = Tersedia kembali

        PeminjamanDetail::create([
            'peminjaman_id' => $pinjamSelesai->id,
            'sarpras_unit_id' => $unitSelesai->id,
            'sarpras_id' => $laptop->id,
            'quantity' => 1,
            'handed_over_at' => now()->subDays(5),
            'handed_over_by' => $petugas->id,
        ]);


        // 8. Create Maintenance Record
        Maintenance::create([
            'sarpras_unit_id' => $unitMaintenance->id,
            'petugas_id' => $petugas->id,
            'jenis' => 'perbaikan', // Menambah field jenis karena required default?
            'deskripsi' => 'Layar berkedip.',
            'tanggal_mulai' => now()->subDays(2),
            'status' => 'sedang_berlangsung',
            'biaya' => 150000,
        ]);

        $this->command->info('Seeding Completed!');
        $this->command->info('-------------------------------------------');
        $this->command->info('User 1  : user@gmail.com    | pass: password');
        $this->command->info('Admin   : admin@gmail.com   | pass: password');
        $this->command->info('Petugas : petugas@gmail.com | pass: password');
        $this->command->info('Guru    : guru@gmail.com    | pass: password');
    }
}
