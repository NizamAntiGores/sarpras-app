<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Sarpras;
use App\Models\SarprasUnit;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\Pengembalian;
use App\Models\PengembalianDetail;
use App\Models\Maintenance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Updated for unit-based asset management system.
     */
    public function run(): void
    {
        // =============================================
        // USERS SEEDER
        // =============================================
        
        // 1. Admin
        $admin = User::create([
            'name' => 'Administrator',
            'nomor_induk' => 'ADM001',
            'email' => 'admin@smk.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'kontak' => '081234567890',
            'email_verified_at' => now(),
        ]);

        // 2. Petugas
        $petugas1 = User::create([
            'name' => 'Budi Santoso',
            'nomor_induk' => 'PTG001',
            'email' => 'petugas@smk.sch.id',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'kontak' => '081234567891',
            'email_verified_at' => now(),
        ]);

        $petugas2 = User::create([
            'name' => 'Siti Aminah',
            'nomor_induk' => 'PTG002',
            'email' => 'petugas2@smk.sch.id',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'kontak' => '081234567892',
            'email_verified_at' => now(),
        ]);

        // 3. Peminjam/Siswa
        $siswa1 = User::create([
            'name' => 'Ahmad Rizki',
            'nomor_induk' => '20240001',
            'email' => 'ahmad.rizki@smk.sch.id',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
            'kontak' => '081234567893',
            'email_verified_at' => now(),
        ]);

        $siswa2 = User::create([
            'name' => 'Dewi Lestari',
            'nomor_induk' => '20240002',
            'email' => 'dewi.lestari@smk.sch.id',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
            'kontak' => '081234567894',
            'email_verified_at' => now(),
        ]);

        $siswa3 = User::create([
            'name' => 'Rudi Hartono',
            'nomor_induk' => '20240003',
            'email' => 'rudi.hartono@smk.sch.id',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
            'kontak' => '081234567895',
            'email_verified_at' => now(),
        ]);

        $siswa4 = User::create([
            'name' => 'Putri Wulandari',
            'nomor_induk' => '20240004',
            'email' => 'putri.wulandari@smk.sch.id',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
            'kontak' => '081234567896',
            'email_verified_at' => now(),
        ]);

        $siswa5 = User::create([
            'name' => 'Andi Pratama',
            'nomor_induk' => '20240005',
            'email' => 'andi.pratama@smk.sch.id',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
            'kontak' => '081234567897',
            'email_verified_at' => now(),
        ]);

        // =============================================
        // LOKASI SEEDER
        // =============================================

        $lokasiMultimedia = Lokasi::create([
            'nama_lokasi' => 'Ruang Multimedia',
            'keterangan' => 'Ruang untuk presentasi dan kegiatan multimedia',
        ]);

        $lokasiLab = Lokasi::create([
            'nama_lokasi' => 'Lab Komputer',
            'keterangan' => 'Laboratorium komputer utama',
        ]);

        $lokasiGudang = Lokasi::create([
            'nama_lokasi' => 'Gudang Utama',
            'keterangan' => 'Gudang penyimpanan barang-barang sekolah',
        ]);

        $lokasiOlahraga = Lokasi::create([
            'nama_lokasi' => 'Ruang Olahraga',
            'keterangan' => 'Ruang penyimpanan alat olahraga',
        ]);

        $lokasiPerpus = Lokasi::create([
            'nama_lokasi' => 'Perpustakaan',
            'keterangan' => 'Ruang baca dan penyimpanan buku',
        ]);

        $lokasiAula = Lokasi::create([
            'nama_lokasi' => 'Aula Sekolah',
            'keterangan' => 'Aula untuk acara besar sekolah',
        ]);

        // =============================================
        // KATEGORI SEEDER
        // =============================================
        
        $kategoriElektronik = Kategori::create([
            'nama_kategori' => 'Elektronik',
        ]);

        $kategoriFurniture = Kategori::create([
            'nama_kategori' => 'Furniture',
        ]);

        $kategoriOlahraga = Kategori::create([
            'nama_kategori' => 'Alat Olahraga',
        ]);

        $kategoriATK = Kategori::create([
            'nama_kategori' => 'Alat Tulis Kantor',
        ]);

        $kategoriMusik = Kategori::create([
            'nama_kategori' => 'Alat Musik',
        ]);

        // =============================================
        // SARPRAS & UNITS SEEDER (UNIT-BASED)
        // =============================================
        
        // Helper function to create units
        $createUnits = function($sarpras, $lokasi, $count, $rusak = 0) {
            $units = [];
            for ($i = 1; $i <= $count; $i++) {
                $kondisi = $i <= ($count - $rusak) ? 'baik' : 'rusak_ringan';
                $status = 'tersedia';
                
                $units[] = SarprasUnit::create([
                    'sarpras_id' => $sarpras->id,
                    'kode_unit' => $sarpras->kode_barang . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'lokasi_id' => $lokasi->id,
                    'kondisi' => $kondisi,
                    'status' => $status,
                    'tanggal_perolehan' => Carbon::now()->subMonths(rand(1, 24)),
                    'nilai_perolehan' => rand(100000, 5000000),
                ]);
            }
            return $units;
        };

        // Helper to copy seed image
        $getFotoPath = function($filename) {
            $sourcePath = storage_path('seed_images/' . $filename);
            if (file_exists($sourcePath)) {
                $targetDir = 'sarpras';
                $targetPath = storage_path('app/public/' . $targetDir);
                
                if (!file_exists($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
                
                $newFilename = uniqid() . '_' . $filename;
                copy($sourcePath, $targetPath . '/' . $newFilename);
                return $targetDir . '/' . $newFilename;
            }
            return null;
        };

        // Elektronik
        $proyektor = Sarpras::create([
            'kode_barang' => 'PRJ',
            'nama_barang' => 'Proyektor Epson EB-X51',
            'kategori_id' => $kategoriElektronik->id,
            'foto' => $getFotoPath('proyektor.jpg'),
            'tipe' => 'asset',
        ]);
        $proyektorUnits = $createUnits($proyektor, $lokasiMultimedia, 5, 1);

        $laptop = Sarpras::create([
            'kode_barang' => 'LPT',
            'nama_barang' => 'Laptop Lenovo ThinkPad',
            'kategori_id' => $kategoriElektronik->id,
            'foto' => $getFotoPath('laptop.jpg'),
            'tipe' => 'asset',
        ]);
        $laptopUnits = $createUnits($laptop, $lokasiLab, 10, 2);

        $speaker = Sarpras::create([
            'kode_barang' => 'SPK',
            'nama_barang' => 'Speaker Portable JBL',
            'kategori_id' => $kategoriElektronik->id,
            'foto' => $getFotoPath('speaker.jpg'),
            'tipe' => 'asset',
        ]);
        $speakerUnits = $createUnits($speaker, $lokasiAula, 3, 0);

        $kamera = Sarpras::create([
            'kode_barang' => 'CAM',
            'nama_barang' => 'Kamera DSLR Canon EOS',
            'kategori_id' => $kategoriElektronik->id,
            'foto' => $getFotoPath('kamera.jpg'),
            'tipe' => 'asset',
        ]);
        $kameraUnits = $createUnits($kamera, $lokasiMultimedia, 3, 0);

        $mikrofon = Sarpras::create([
            'kode_barang' => 'MIC',
            'nama_barang' => 'Mikrofon Wireless Shure',
            'kategori_id' => $kategoriElektronik->id,
            'foto' => $getFotoPath('mikrofon.jpg'),
            'tipe' => 'asset',
        ]);
        $mikrofonUnits = $createUnits($mikrofon, $lokasiAula, 4, 1);

        // Furniture
        $mejaLipat = Sarpras::create([
            'kode_barang' => 'MJB',
            'nama_barang' => 'Meja Belajar',
            'kategori_id' => $kategoriFurniture->id,
            'foto' => $getFotoPath('meja.jpg'),
            'tipe' => 'asset',
        ]);
        $mejaUnits = $createUnits($mejaLipat, $lokasiGudang, 20, 2);

        $kursiPlastik = Sarpras::create([
            'kode_barang' => 'KRS',
            'nama_barang' => 'Kursi Plastik',
            'kategori_id' => $kategoriFurniture->id,
            'foto' => $getFotoPath('kursi.jpg'),
            'tipe' => 'asset',
        ]);
        $kursiUnits = $createUnits($kursiPlastik, $lokasiGudang, 30, 3);

        // Alat Olahraga
        $bolaVoli = Sarpras::create([
            'kode_barang' => 'BVL',
            'nama_barang' => 'Bola Voli Mikasa',
            'kategori_id' => $kategoriOlahraga->id,
            'foto' => $getFotoPath('voli.jpg'),
            'tipe' => 'bahan', // Contoh bahan habis pakai/tidak perlu maintenance serius
        ]);
        $bolaVoliUnits = $createUnits($bolaVoli, $lokasiOlahraga, 8, 1);

        $bolaBasket = Sarpras::create([
            'kode_barang' => 'BBK',
            'nama_barang' => 'Bola Basket Spalding',
            'kategori_id' => $kategoriOlahraga->id,
            'foto' => $getFotoPath('basket.jpg'),
            'tipe' => 'bahan',
        ]);
        $bolaBasketUnits = $createUnits($bolaBasket, $lokasiOlahraga, 6, 1);

        $raketBadminton = Sarpras::create([
            'kode_barang' => 'RKT',
            'nama_barang' => 'Raket Badminton Yonex',
            'kategori_id' => $kategoriOlahraga->id,
            'foto' => $getFotoPath('raket.jpg'),
            'tipe' => 'asset',
        ]);
        $raketUnits = $createUnits($raketBadminton, $lokasiOlahraga, 10, 2);

        // Alat Musik
        $gitarAkustik = Sarpras::create([
            'kode_barang' => 'GTR',
            'nama_barang' => 'Gitar Akustik Yamaha',
            'kategori_id' => $kategoriMusik->id,
            'foto' => $getFotoPath('gitar.jpg'),
            'tipe' => 'asset',
        ]);
        $gitarUnits = $createUnits($gitarAkustik, $lokasiAula, 4, 1);

        $keyboardYamaha = Sarpras::create([
            'kode_barang' => 'KYB',
            'nama_barang' => 'Keyboard Yamaha PSR',
            'kategori_id' => $kategoriMusik->id,
            'foto' => $getFotoPath('keyboard.jpg'),
            'tipe' => 'asset',
        ]);
        $keyboardUnits = $createUnits($keyboardYamaha, $lokasiAula, 2, 0);

        // =============================================
        // PEMINJAMAN SEEDER (UNIT-BASED)
        // =============================================
        
        // Peminjaman 1: Sudah disetujui dan sedang dipinjam (2 unit laptop)
        $peminjaman1 = Peminjaman::create([
            'user_id' => $siswa1->id,
            'petugas_id' => $petugas1->id,
            'tgl_pinjam' => Carbon::now()->subDays(3),
            'tgl_kembali_rencana' => Carbon::now()->addDays(4),
            'status' => 'disetujui',
            'keterangan' => 'Untuk presentasi kelas X-TKJ',
        ]);
        
        // Set units as dipinjam
        $laptopUnits[0]->update(['status' => 'dipinjam']);
        $laptopUnits[1]->update(['status' => 'dipinjam']);
        
        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman1->id,
            'sarpras_unit_id' => $laptopUnits[0]->id,
        ]);
        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman1->id,
            'sarpras_unit_id' => $laptopUnits[1]->id,
        ]);

        // Peminjaman 2: Sudah dikembalikan
        $peminjaman2 = Peminjaman::create([
            'user_id' => $siswa2->id,
            'petugas_id' => $petugas1->id,
            'tgl_pinjam' => Carbon::now()->subDays(10),
            'tgl_kembali_rencana' => Carbon::now()->subDays(5),
            'status' => 'selesai',
            'keterangan' => 'Untuk praktik pemrograman',
        ]);
        
        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman2->id,
            'sarpras_unit_id' => $proyektorUnits[0]->id,
        ]);
        
        // Create pengembalian for peminjaman2
        $pengembalian1 = Pengembalian::create([
            'peminjaman_id' => $peminjaman2->id,
            'petugas_id' => $petugas1->id,
            'tgl_kembali_aktual' => Carbon::now()->subDays(5),
        ]);
        
        PengembalianDetail::create([
            'pengembalian_id' => $pengembalian1->id,
            'sarpras_unit_id' => $proyektorUnits[0]->id,
            'kondisi_akhir' => 'baik',
            'denda' => 0,
        ]);

        // Peminjaman 3: Menunggu persetujuan
        $peminjaman3 = Peminjaman::create([
            'user_id' => $siswa3->id,
            'petugas_id' => null,
            'tgl_pinjam' => Carbon::now(),
            'tgl_kembali_rencana' => Carbon::now()->addDays(2),
            'status' => 'menunggu',
            'keterangan' => 'Untuk latihan voli kelas XII',
        ]);
        
        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman3->id,
            'sarpras_unit_id' => $bolaVoliUnits[0]->id,
        ]);
        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman3->id,
            'sarpras_unit_id' => $bolaVoliUnits[1]->id,
        ]);

        // Peminjaman 4: Sedang dipinjam (1 speaker)
        $peminjaman4 = Peminjaman::create([
            'user_id' => $siswa4->id,
            'petugas_id' => $petugas2->id,
            'tgl_pinjam' => Carbon::now()->subDays(1),
            'tgl_kembali_rencana' => Carbon::now()->addDays(2),
            'status' => 'disetujui',
            'keterangan' => 'Untuk acara pramuka',
        ]);
        
        $speakerUnits[0]->update(['status' => 'dipinjam']);
        
        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman4->id,
            'sarpras_unit_id' => $speakerUnits[0]->id,
        ]);

        // Peminjaman 5: Ditolak
        $peminjaman5 = Peminjaman::create([
            'user_id' => $siswa5->id,
            'petugas_id' => $petugas1->id,
            'tgl_pinjam' => Carbon::now()->subDays(2),
            'tgl_kembali_rencana' => Carbon::now()->addDays(5),
            'status' => 'ditolak',
            'keterangan' => 'Unit tidak tersedia',
            'catatan_petugas' => 'Semua unit kamera sedang dipinjam',
        ]);

        // Peminjaman 6: Menunggu persetujuan (gitar)
        $peminjaman6 = Peminjaman::create([
            'user_id' => $siswa2->id,
            'petugas_id' => null,
            'tgl_pinjam' => Carbon::now(),
            'tgl_kembali_rencana' => Carbon::now()->addDays(3),
            'status' => 'menunggu',
            'keterangan' => 'Untuk pentas seni',
        ]);
        
        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman6->id,
            'sarpras_unit_id' => $gitarUnits[0]->id,
        ]);
        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman6->id,
            'sarpras_unit_id' => $gitarUnits[1]->id,
        ]);

        // =============================================
        // MAINTENANCE SEEDER
        // =============================================
        
        // Maintenance 1: Sedang berlangsung (mikrofon rusak)
        $mikrofonUnits[3]->update(['status' => 'maintenance']);
        
        Maintenance::create([
            'sarpras_unit_id' => $mikrofonUnits[3]->id,
            'petugas_id' => $petugas1->id,
            'jenis' => 'perbaikan',
            'deskripsi' => 'Tidak ada suara output, perlu cek kabel dan koneksi',
            'tanggal_mulai' => Carbon::now()->subDays(2),
            'biaya' => 150000,
            'status' => 'sedang_berlangsung',
        ]);

        // Maintenance 2: Selesai (laptop)
        Maintenance::create([
            'sarpras_unit_id' => $laptopUnits[8]->id,
            'petugas_id' => $petugas2->id,
            'jenis' => 'servis_rutin',
            'deskripsi' => 'Install ulang OS dan update driver',
            'tanggal_mulai' => Carbon::now()->subDays(7),
            'tanggal_selesai' => Carbon::now()->subDays(5),
            'biaya' => 50000,
            'status' => 'selesai',
        ]);

        // Maintenance 3: Sedang berlangsung (raket)
        $raketUnits[8]->update(['status' => 'maintenance', 'kondisi' => 'rusak_ringan']);
        
        Maintenance::create([
            'sarpras_unit_id' => $raketUnits[8]->id,
            'petugas_id' => $petugas1->id,
            'jenis' => 'penggantian_komponen',
            'deskripsi' => 'Senar putus, perlu ganti senar baru',
            'tanggal_mulai' => Carbon::now()->subDays(1),
            'biaya' => 75000,
            'status' => 'sedang_berlangsung',
        ]);

        // =============================================
        // OUTPUT INFO
        // =============================================
        
        $totalUnits = SarprasUnit::count();
        $tersedia = SarprasUnit::where('status', 'tersedia')->count();
        $dipinjam = SarprasUnit::where('status', 'dipinjam')->count();
        $maintenance = SarprasUnit::where('status', 'maintenance')->count();

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('   SEEDER BERHASIL DIJALANKAN!');
        $this->command->info('   (Unit-Based Asset Management)');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('📊 Data yang telah dibuat:');
        $this->command->info("   - Users         : 8 (1 Admin, 2 Petugas, 5 Siswa)");
        $this->command->info("   - Lokasi        : 6");
        $this->command->info("   - Kategori      : 5");
        $this->command->info("   - Jenis Barang  : " . Sarpras::count());
        $this->command->info("   - Total Unit    : {$totalUnits}");
        $this->command->info("     └─ Tersedia   : {$tersedia}");
        $this->command->info("     └─ Dipinjam   : {$dipinjam}");
        $this->command->info("     └─ Maintenance: {$maintenance}");
        $this->command->info("   - Peminjaman    : " . Peminjaman::count());
        $this->command->info("   - Maintenance   : " . Maintenance::count());
        $this->command->info('');
        $this->command->info('🔑 Akun Login:');
        $this->command->info('   ┌────────────┬────────────────────────────┬──────────┐');
        $this->command->info('   │ Role       │ Email                      │ Password │');
        $this->command->info('   ├────────────┼────────────────────────────┼──────────┤');
        $this->command->info('   │ Admin      │ admin@smk.sch.id           │ password │');
        $this->command->info('   │ Petugas    │ petugas@smk.sch.id         │ password │');
        $this->command->info('   │ Petugas    │ petugas2@smk.sch.id        │ password │');
        $this->command->info('   │ Peminjam   │ ahmad.rizki@smk.sch.id     │ password │');
        $this->command->info('   │ Peminjam   │ dewi.lestari@smk.sch.id    │ password │');
        $this->command->info('   │ Peminjam   │ rudi.hartono@smk.sch.id    │ password │');
        $this->command->info('   │ Peminjam   │ putri.wulandari@smk.sch.id │ password │');
        $this->command->info('   │ Peminjam   │ andi.pratama@smk.sch.id    │ password │');
        $this->command->info('   └────────────┴────────────────────────────┴──────────┘');
        $this->command->info('========================================');
    }
}
