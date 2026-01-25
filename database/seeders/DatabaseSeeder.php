<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Maintenance;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\Pengembalian;
use App\Models\PengembalianDetail;
use App\Models\Sarpras;
use App\Models\SarprasUnit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        // 1. Admin 1 (Head Admin)
        $admin = User::create([
            'name' => 'Administrator',
            'nomor_induk' => 'ADM001',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'kontak' => '081234567890',
            'email_verified_at' => now(),
        ]);

        // Admin 2
        $admin2 = User::create([
            'name' => 'Admin Sarpras 2',
            'nomor_induk' => 'ADM002',
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'kontak' => '081234567898',
            'email_verified_at' => now(),
        ]);

        // 2. Petugas
        $petugas1 = User::create([
            'name' => 'Budi Santoso',
            'nomor_induk' => 'PTG001',
            'email' => 'petugas@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'kontak' => '081234567891',
            'email_verified_at' => now(),
        ]);

        $petugas2 = User::create([
            'name' => 'Siti Aminah',
            'nomor_induk' => 'PTG002',
            'email' => 'petugas2@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'kontak' => '081234567892',
            'email_verified_at' => now(),
        ]);

        // 3. Peminjam/Siswa
        $siswa1 = User::create([
            'name' => 'Ahmad Rizki',
            'nomor_induk' => '20240001',
            'email' => 'ahmad.rizki@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
            'kontak' => '081234567893',
            'email_verified_at' => now(),
        ]);

        $siswa2 = User::create([
            'name' => 'Dewi Lestari',
            'nomor_induk' => '20240002',
            'email' => 'dewi.lestari@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
            'kontak' => '081234567894',
            'email_verified_at' => now(),
        ]);

        $siswa3 = User::create([
            'name' => 'Rudi Hartono',
            'nomor_induk' => '20240003',
            'email' => 'rudi.hartono@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
            'kontak' => '081234567895',
            'email_verified_at' => now(),
        ]);

        $siswa4 = User::create([
            'name' => 'Putri Wulandari',
            'nomor_induk' => '20240004',
            'email' => 'putri.wulandari@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
            'kontak' => '081234567896',
            'email_verified_at' => now(),
        ]);

        $siswa5 = User::create([
            'name' => 'Andi Pratama',
            'nomor_induk' => '20240005',
            'email' => 'andi.pratama@gmail.com',
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
        $createUnits = function ($sarpras, $lokasi, $count, $rusak = 0) {
            $units = [];
            for ($i = 1; $i <= $count; $i++) {
                $kondisi = $i <= ($count - $rusak) ? 'baik' : 'rusak_ringan';
                $status = 'tersedia';

                $units[] = SarprasUnit::create([
                    'sarpras_id' => $sarpras->id,
                    'kode_unit' => $sarpras->kode_barang.'-'.str_pad($i, 3, '0', STR_PAD_LEFT),
                    'lokasi_id' => $lokasi->id,
                    'kondisi' => $kondisi,
                    'status' => $status,
                    'tanggal_perolehan' => Carbon::now()->subMonths(rand(1, 24)),

                ]);
            }

            return $units;
        };

        // Elektronik
        $proyektor = Sarpras::create([
            'kode_barang' => 'PRJ',
            'nama_barang' => 'Proyektor Epson EB-X51',
            'kategori_id' => $kategoriElektronik->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $proyektorUnits = $createUnits($proyektor, $lokasiMultimedia, 5, 1);

        $laptop = Sarpras::create([
            'kode_barang' => 'LPT',
            'nama_barang' => 'Laptop Lenovo ThinkPad',
            'kategori_id' => $kategoriElektronik->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $laptopUnits = $createUnits($laptop, $lokasiLab, 10, 2);

        $speaker = Sarpras::create([
            'kode_barang' => 'SPK',
            'nama_barang' => 'Speaker Portable JBL',
            'kategori_id' => $kategoriElektronik->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $speakerUnits = $createUnits($speaker, $lokasiAula, 3, 0);

        $kamera = Sarpras::create([
            'kode_barang' => 'CAM',
            'nama_barang' => 'Kamera DSLR Canon EOS',
            'kategori_id' => $kategoriElektronik->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $kameraUnits = $createUnits($kamera, $lokasiMultimedia, 3, 0);

        $mikrofon = Sarpras::create([
            'kode_barang' => 'MIC',
            'nama_barang' => 'Mikrofon Wireless Shure',
            'kategori_id' => $kategoriElektronik->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $mikrofonUnits = $createUnits($mikrofon, $lokasiAula, 4, 1);

        // Furniture
        $mejaLipat = Sarpras::create([
            'kode_barang' => 'MJB',
            'nama_barang' => 'Meja Belajar',
            'kategori_id' => $kategoriFurniture->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $mejaUnits = $createUnits($mejaLipat, $lokasiGudang, 20, 2);

        $kursiPlastik = Sarpras::create([
            'kode_barang' => 'KRS',
            'nama_barang' => 'Kursi Plastik',
            'kategori_id' => $kategoriFurniture->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $kursiUnits = $createUnits($kursiPlastik, $lokasiGudang, 30, 3);

        // Alat Olahraga
        $bolaVoli = Sarpras::create([
            'kode_barang' => 'BVL',
            'nama_barang' => 'Bola Voli Mikasa',
            'kategori_id' => $kategoriOlahraga->id,
            'foto' => null,
            'tipe' => 'asset', // Changed from 'bahan' to 'asset'
        ]);
        $bolaVoliUnits = $createUnits($bolaVoli, $lokasiOlahraga, 8, 1);

        $bolaBasket = Sarpras::create([
            'kode_barang' => 'BBK',
            'nama_barang' => 'Bola Basket Spalding',
            'kategori_id' => $kategoriOlahraga->id,
            'foto' => null,
            'tipe' => 'asset', // Changed from 'bahan' to 'asset'
        ]);
        $bolaBasketUnits = $createUnits($bolaBasket, $lokasiOlahraga, 6, 1);

        $raketBadminton = Sarpras::create([
            'kode_barang' => 'RKT',
            'nama_barang' => 'Raket Badminton Yonex',
            'kategori_id' => $kategoriOlahraga->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $raketUnits = $createUnits($raketBadminton, $lokasiOlahraga, 10, 2);

        // Alat Musik
        $gitarAkustik = Sarpras::create([
            'kode_barang' => 'GTR',
            'nama_barang' => 'Gitar Akustik Yamaha',
            'kategori_id' => $kategoriMusik->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $gitarUnits = $createUnits($gitarAkustik, $lokasiAula, 4, 1);

        $keyboardYamaha = Sarpras::create([
            'kode_barang' => 'KYB',
            'nama_barang' => 'Keyboard Yamaha PSR',
            'kategori_id' => $kategoriMusik->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $keyboardUnits = $createUnits($keyboardYamaha, $lokasiAula, 2, 0);

        // Additional Items

        // More Elektronik
        $printer = Sarpras::create([
            'kode_barang' => 'PRT',
            'nama_barang' => 'Printer Canon Pixma',
            'kategori_id' => $kategoriElektronik->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $printerUnits = $createUnits($printer, $lokasiLab, 2, 0);

        $scanner = Sarpras::create([
            'kode_barang' => 'SCN',
            'nama_barang' => 'Scanner Epson',
            'kategori_id' => $kategoriElektronik->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $scannerUnits = $createUnits($scanner, $lokasiLab, 1, 0);

        // More Furniture
        $lemariArsip = Sarpras::create([
            'kode_barang' => 'LMR',
            'nama_barang' => 'Lemari Arsip Besi',
            'kategori_id' => $kategoriFurniture->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $lemariUnits = $createUnits($lemariArsip, $lokasiGudang, 5, 0);

        $papanTulis = Sarpras::create([
            'kode_barang' => 'WBN',
            'nama_barang' => 'Whiteboard Besar',
            'kategori_id' => $kategoriFurniture->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $papanTulisUnits = $createUnits($papanTulis, $lokasiAula, 2, 0);

        // More Olahraga
        $matrasYoga = Sarpras::create([
            'kode_barang' => 'MTR',
            'nama_barang' => 'Matras Yoga',
            'kategori_id' => $kategoriOlahraga->id,
            'foto' => null,
            'tipe' => 'asset',
        ]);
        $matrasUnits = $createUnits($matrasYoga, $lokasiOlahraga, 15, 0);

        $bolaFutsal = Sarpras::create([
            'kode_barang' => 'BFS',
            'nama_barang' => 'Bola Futsal Specs',
            'kategori_id' => $kategoriOlahraga->id,
            'foto' => null,
            'tipe' => 'asset', // Changed from 'bahan' to 'asset'
        ]);
        $bolaFutsalUnits = $createUnits($bolaFutsal, $lokasiOlahraga, 5, 0);

        // ATK (Previously empty category)
        $spidol = Sarpras::create([
            'kode_barang' => 'SPD',
            'nama_barang' => 'Spidol Boardmarker Hitam',
            'kategori_id' => $kategoriATK->id,
            'foto' => null,
            'tipe' => 'bahan',
        ]);
        $spidolUnits = $createUnits($spidol, $lokasiGudang, 50, 0);

        $kertasA4 = Sarpras::create([
            'kode_barang' => 'HVS',
            'nama_barang' => 'Kertas HVS A4 (Rim)',
            'kategori_id' => $kategoriATK->id,
            'foto' => null,
            'tipe' => 'bahan',
        ]);
        $kertasUnits = $createUnits($kertasA4, $lokasiGudang, 20, 0);

        // =============================================
        // PEMINJAMAN SEEDER (UNIT-BASED)
        // =============================================

        // Peminjaman 1: Disetujui (BESOK TERAKHIR / Warning Kuning)
        $peminjaman1 = Peminjaman::create([
            'user_id' => $siswa1->id,
            'petugas_id' => $petugas1->id,
            'tgl_pinjam' => Carbon::now()->subDays(6), // Pinjam seminggu lalu
            'tgl_kembali_rencana' => Carbon::tomorrow(), // Kembali BESOK
            'status' => 'disetujui',
            'keterangan' => 'Untuk presentasi kelas X-TKJ (Deadline Besok)',
            'qr_code' => 'PJM-'.strtoupper(uniqid()),
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

        // Peminjaman 2: Sudah dikembalikan (Selesai)
        $peminjaman2 = Peminjaman::create([
            'user_id' => $siswa2->id,
            'petugas_id' => $petugas1->id,
            'tgl_pinjam' => Carbon::now()->subDays(10),
            'tgl_kembali_rencana' => Carbon::now()->subDays(5),
            'status' => 'selesai',
            'keterangan' => 'Untuk praktik pemrograman',
            'qr_code' => 'PJM-'.strtoupper(uniqid()), // History QR tetap ada
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

        // Peminjaman 3: Menunggu (Tanpa QR)
        $peminjaman3 = Peminjaman::create([
            'user_id' => $siswa3->id,
            'petugas_id' => null,
            'tgl_pinjam' => Carbon::now(),
            'tgl_kembali_rencana' => Carbon::now()->addDays(2),
            'status' => 'menunggu',
            'keterangan' => 'Untuk latihan voli kelas XII',
            'qr_code' => null,
        ]);

        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman3->id,
            'sarpras_unit_id' => $bolaVoliUnits[0]->id,
        ]);
        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman3->id,
            'sarpras_unit_id' => $bolaVoliUnits[1]->id,
        ]);

        // Peminjaman 4: Disetujui (DEADLINE HARI INI / Warning Orange)
        $peminjaman4 = Peminjaman::create([
            'user_id' => $siswa4->id,
            'petugas_id' => $petugas2->id,
            'tgl_pinjam' => Carbon::now()->subDays(2),
            'tgl_kembali_rencana' => Carbon::today(), // Deadline HARI INI
            'status' => 'disetujui',
            'keterangan' => 'Untuk acara pramuka (Harus kembali hari ini)',
            'qr_code' => 'PJM-'.strtoupper(uniqid()),
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
            'qr_code' => null, // Ditolak tidak dapat QR
        ]);

        // Peminjaman 7: TERLAMBAT (Overdue / Badge Merah)
        // Dipinjam 10 hari lalu, harusnya kembali 5 hari lalu
        $peminjaman7 = Peminjaman::create([
            'user_id' => $siswa5->id, // Andi Pratama
            'petugas_id' => $petugas2->id,
            'tgl_pinjam' => Carbon::now()->subDays(10),
            'tgl_kembali_rencana' => Carbon::now()->subDays(5),
            'status' => 'disetujui',
            'keterangan' => 'Project fotografi sekolah (DATA CONTOH TERLAMBAT)',
            'qr_code' => 'PJM-'.strtoupper(uniqid()),
        ]);

        $kameraUnits[0]->update(['status' => 'dipinjam']);

        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman7->id,
            'sarpras_unit_id' => $kameraUnits[0]->id,
        ]);
        $peminjaman6 = Peminjaman::create([
            'user_id' => $siswa2->id,
            'petugas_id' => null,
            'tgl_pinjam' => Carbon::now(),
            'tgl_kembali_rencana' => Carbon::now()->addDays(3),
            'status' => 'menunggu',
            'keterangan' => 'Untuk pentas seni',
            'qr_code' => null,
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

        // =============================================
        // MASS DATA GENERATION (STRESS TEST)
        // =============================================
        // Menambahkan 40 data random untuk tes pagination & performa

        $faker = \Faker\Factory::create('id_ID');
        $sarprasItems = Sarpras::where('tipe', 'asset')->get(); // Changed to filter by 'asset'

        // Initialize used unit IDs with manually created ones
        $usedUnitIds = [
            $laptopUnits[0]->id, $laptopUnits[1]->id,
            $proyektorUnits[0]->id,
            $bolaVoliUnits[0]->id, $bolaVoliUnits[1]->id,
            $speakerUnits[0]->id,
            $kameraUnits[0]->id,
            $gitarUnits[0]->id, $gitarUnits[1]->id,
            $mikrofonUnits[3]->id,
            $raketUnits[8]->id,
        ];

        $this->command->info('🚀 Generating 40 dummy data...');

        for ($i = 0; $i < 40; $i++) {
            // 1. Buat User Siswa Random (Tanpa gelar/title)
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $nama = $firstName.' '.$lastName;

            $email = strtolower(str_replace([' ', '.', "'"], '', $nama)).rand(1, 99).'@gmail.com';

            $randomUser = User::create([
                'name' => $nama,
                'nomor_induk' => '2025'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'kontak' => $faker->phoneNumber,
                'email_verified_at' => now(),
            ]);

            // 2. Tentukan Status Random
            $statuses = ['menunggu', 'disetujui', 'selesai', 'ditolak'];
            $status = $statuses[array_rand($statuses)];

            // 3. Pilih Barang Random
            $randomSarpras = $sarprasItems->random();
            // Cari unit yang tersedia DAN belum digunakan di seeder ini
            $unit = SarprasUnit::where('sarpras_id', $randomSarpras->id)
                ->where('status', 'tersedia')
                ->whereNotIn('id', $usedUnitIds)
                ->first();

            // Kalau ada unit, gas pinjam
            if ($unit) {
                // Tandai unit sebagai used agar tidak dipilih lagi di loop berikutnya
                $usedUnitIds[] = $unit->id;

                // ... logic same as before ...
                $tglPinjam = Carbon::now()->subDays(rand(1, 30));
                $durasi = rand(1, 7);
                $tglKembaliRencana = (clone $tglPinjam)->addDays($durasi);

                $qrCode = ($status === 'disetujui' || $status === 'selesai')
                    ? 'PJM-'.strtoupper(uniqid())
                    : null;

                $peminjaman = Peminjaman::create([
                    'user_id' => $randomUser->id,
                    'petugas_id' => ($status !== 'menunggu') ? $petugas1->id : null,
                    'tgl_pinjam' => $tglPinjam,
                    'tgl_kembali_rencana' => $tglKembaliRencana,
                    'status' => $status,
                    'keterangan' => 'Keperluan '.$faker->sentence(3),
                    'catatan_petugas' => ($status === 'ditolak') ? 'Unit barang terbatas.' : null,
                    'qr_code' => $qrCode,
                ]);

                PeminjamanDetail::create([
                    'peminjaman_id' => $peminjaman->id,
                    'sarpras_unit_id' => $unit->id,
                ]);

                // Update Status Unit
                if ($status === 'disetujui') {
                    $unit->update(['status' => 'dipinjam']);
                } elseif ($status === 'selesai') {
                    // Buat data pengembalian
                    $kembaliAktual = (clone $tglKembaliRencana)->addDays(rand(-1, 2)); // Bisa telat dikit
                    Pengembalian::create([
                        'peminjaman_id' => $peminjaman->id,
                        'petugas_id' => $petugas1->id,
                        'tgl_kembali_aktual' => $kembaliAktual,
                    ]);
                }
            }
        }

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('   SEEDER BERHASIL DIJALANKAN!');
        $this->command->info('   (Unit-Based Asset Management)');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('📊 Data yang telah dibuat:');
        $this->command->info('   - Users         : 8 (1 Admin, 2 Petugas, 5 Siswa)');
        $this->command->info('   - Lokasi        : 6');
        $this->command->info('   - Kategori      : 5');
        $this->command->info('   - Jenis Barang  : '.Sarpras::count());
        $this->command->info("   - Total Unit    : {$totalUnits}");
        $this->command->info("     └─ Tersedia   : {$tersedia}");
        $this->command->info("     └─ Dipinjam   : {$dipinjam}");
        $this->command->info("     └─ Maintenance: {$maintenance}");
        $this->command->info('   - Peminjaman    : '.Peminjaman::count());
        $this->command->info('   - Maintenance   : '.Maintenance::count());
        $this->command->info('');
        $this->command->info('🔑 Akun Login:');
        $this->command->info('   ┌────────────┬────────────────────────────┬──────────┐');
        $this->command->info('   │ Role       │ Email                      │ Password │');
        $this->command->info('   ├────────────┼────────────────────────────┼──────────┤');
        $this->command->info('   │ Admin      │ admin@gmail.com            │ password │');
        $this->command->info('   │ Petugas    │ petugas@gmail.com          │ password │');
        $this->command->info('   │ Petugas    │ petugas2@gmail.com         │ password │');
        $this->command->info('   │ Peminjam   │ ahmad.rizki@gmail.com      │ password │');
        $this->command->info('   │ Peminjam   │ dewi.lestari@gmail.com     │ password │');
        $this->command->info('   │ Peminjam   │ rudi.hartono@gmail.com     │ password │');
        $this->command->info('   │ Peminjam   │ putri.wulandari@gmail.com  │ password │');
        $this->command->info('   │ Peminjam   │ andi.pratama@gmail.com     │ password │');
        $this->command->info('   └────────────┴────────────────────────────┴──────────┘');
        $this->command->info('========================================');
    }
}
