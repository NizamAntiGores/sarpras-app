<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Sarpras;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

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
        // SARPRAS SEEDER
        // =============================================
        
        // Elektronik
        $proyektor = Sarpras::create([
            'kode_barang' => 'ELK-001',
            'nama_barang' => 'Proyektor Epson EB-X51',
            'kategori_id' => $kategoriElektronik->id,
            'lokasi_id' => $lokasiMultimedia->id,
            'stok' => 5,
            'stok_rusak' => 1,
            'kondisi_awal' => 'baik',
        ]);

        $laptop = Sarpras::create([
            'kode_barang' => 'ELK-002',
            'nama_barang' => 'Laptop Lenovo ThinkPad',
            'kategori_id' => $kategoriElektronik->id,
            'lokasi_id' => $lokasiLab->id,
            'stok' => 20,
            'stok_rusak' => 2,
            'kondisi_awal' => 'baik',
        ]);

        $speaker = Sarpras::create([
            'kode_barang' => 'ELK-003',
            'nama_barang' => 'Speaker Portable JBL',
            'kategori_id' => $kategoriElektronik->id,
            'lokasi_id' => $lokasiAula->id,
            'stok' => 3,
            'stok_rusak' => 0,
            'kondisi_awal' => 'baik',
        ]);

        $kamera = Sarpras::create([
            'kode_barang' => 'ELK-004',
            'nama_barang' => 'Kamera DSLR Canon EOS',
            'kategori_id' => $kategoriElektronik->id,
            'lokasi_id' => $lokasiMultimedia->id,
            'stok' => 2,
            'stok_rusak' => 0,
            'kondisi_awal' => 'baik',
        ]);

        $mikrofon = Sarpras::create([
            'kode_barang' => 'ELK-005',
            'nama_barang' => 'Mikrofon Wireless Shure',
            'kategori_id' => $kategoriElektronik->id,
            'lokasi_id' => $lokasiAula->id,
            'stok' => 4,
            'stok_rusak' => 1,
            'kondisi_awal' => 'baik',
        ]);

        // Furniture
        $mejaLipat = Sarpras::create([
            'kode_barang' => 'FRN-001',
            'nama_barang' => 'Meja Lipat Portable',
            'kategori_id' => $kategoriFurniture->id,
            'lokasi_id' => $lokasiGudang->id,
            'stok' => 30,
            'stok_rusak' => 3,
            'kondisi_awal' => 'baik',
        ]);

        $kursiPlastik = Sarpras::create([
            'kode_barang' => 'FRN-002',
            'nama_barang' => 'Kursi Plastik',
            'kategori_id' => $kategoriFurniture->id,
            'lokasi_id' => $lokasiGudang->id,
            'stok' => 100,
            'stok_rusak' => 5,
            'kondisi_awal' => 'baik',
        ]);

        $papanTulis = Sarpras::create([
            'kode_barang' => 'FRN-003',
            'nama_barang' => 'Papan Tulis Portable',
            'kategori_id' => $kategoriFurniture->id,
            'lokasi_id' => $lokasiGudang->id,
            'stok' => 5,
            'stok_rusak' => 0,
            'kondisi_awal' => 'baik',
        ]);

        // Alat Olahraga
        $bolaVoli = Sarpras::create([
            'kode_barang' => 'OLR-001',
            'nama_barang' => 'Bola Voli Mikasa',
            'kategori_id' => $kategoriOlahraga->id,
            'lokasi_id' => $lokasiOlahraga->id,
            'stok' => 10,
            'stok_rusak' => 2,
            'kondisi_awal' => 'baik',
        ]);

        $bolaBasket = Sarpras::create([
            'kode_barang' => 'OLR-002',
            'nama_barang' => 'Bola Basket Spalding',
            'kategori_id' => $kategoriOlahraga->id,
            'lokasi_id' => $lokasiOlahraga->id,
            'stok' => 8,
            'stok_rusak' => 1,
            'kondisi_awal' => 'baik',
        ]);

        $bolaFutsal = Sarpras::create([
            'kode_barang' => 'OLR-003',
            'nama_barang' => 'Bola Futsal Molten',
            'kategori_id' => $kategoriOlahraga->id,
            'lokasi_id' => $lokasiOlahraga->id,
            'stok' => 6,
            'stok_rusak' => 1,
            'kondisi_awal' => 'baik',
        ]);

        $raketBadminton = Sarpras::create([
            'kode_barang' => 'OLR-004',
            'nama_barang' => 'Raket Badminton Yonex',
            'kategori_id' => $kategoriOlahraga->id,
            'lokasi_id' => $lokasiOlahraga->id,
            'stok' => 12,
            'stok_rusak' => 2,
            'kondisi_awal' => 'baik',
        ]);

        $matrasYoga = Sarpras::create([
            'kode_barang' => 'OLR-005',
            'nama_barang' => 'Matras Yoga',
            'kategori_id' => $kategoriOlahraga->id,
            'lokasi_id' => $lokasiOlahraga->id,
            'stok' => 15,
            'stok_rusak' => 0,
            'kondisi_awal' => 'baik',
        ]);

        // Alat Tulis Kantor
        $staplerBesar = Sarpras::create([
            'kode_barang' => 'ATK-001',
            'nama_barang' => 'Stapler Besar',
            'kategori_id' => $kategoriATK->id,
            'lokasi_id' => $lokasiGudang->id,
            'stok' => 10,
            'stok_rusak' => 0,
            'kondisi_awal' => 'baik',
        ]);

        $pemotongKertas = Sarpras::create([
            'kode_barang' => 'ATK-002',
            'nama_barang' => 'Pemotong Kertas',
            'kategori_id' => $kategoriATK->id,
            'lokasi_id' => $lokasiGudang->id,
            'stok' => 3,
            'stok_rusak' => 0,
            'kondisi_awal' => 'baik',
        ]);

        // Alat Musik
        $gitarAkustik = Sarpras::create([
            'kode_barang' => 'MSK-001',
            'nama_barang' => 'Gitar Akustik Yamaha',
            'kategori_id' => $kategoriMusik->id,
            'lokasi_id' => $lokasiAula->id,
            'stok' => 5,
            'stok_rusak' => 1,
            'kondisi_awal' => 'baik',
        ]);

        $keyboardYamaha = Sarpras::create([
            'kode_barang' => 'MSK-002',
            'nama_barang' => 'Keyboard Yamaha PSR',
            'kategori_id' => $kategoriMusik->id,
            'lokasi_id' => $lokasiAula->id,
            'stok' => 2,
            'stok_rusak' => 0,
            'kondisi_awal' => 'baik',
        ]);

        // =============================================
        // PEMINJAMAN SEEDER
        // =============================================
        
        // Peminjaman 1: Sudah disetujui dan sedang dipinjam
        $peminjaman1 = Peminjaman::create([
            'user_id' => $siswa1->id,
            'sarpras_id' => $proyektor->id,
            'petugas_id' => $petugas1->id,
            'jumlah_pinjam' => 1,
            'tgl_pinjam' => Carbon::now()->subDays(3),
            'tgl_kembali_rencana' => Carbon::now()->addDays(4),
            'status' => 'disetujui',
            'keterangan' => 'Untuk presentasi kelas X-TKJ',
        ]);

        // Peminjaman 2: Sudah dikembalikan
        $peminjaman2 = Peminjaman::create([
            'user_id' => $siswa2->id,
            'sarpras_id' => $laptop->id,
            'petugas_id' => $petugas1->id,
            'jumlah_pinjam' => 2,
            'tgl_pinjam' => Carbon::now()->subDays(10),
            'tgl_kembali_rencana' => Carbon::now()->subDays(5),
            'status' => 'selesai',
            'keterangan' => 'Untuk praktik pemrograman',
        ]);

        // Peminjaman 3: Menunggu persetujuan
        $peminjaman3 = Peminjaman::create([
            'user_id' => $siswa3->id,
            'sarpras_id' => $bolaVoli->id,
            'petugas_id' => null,
            'jumlah_pinjam' => 3,
            'tgl_pinjam' => Carbon::now(),
            'tgl_kembali_rencana' => Carbon::now()->addDays(1),
            'status' => 'menunggu',
            'keterangan' => 'Untuk latihan voli kelas XII',
        ]);

        // Peminjaman 4: Sudah disetujui, sedang dipinjam
        $peminjaman4 = Peminjaman::create([
            'user_id' => $siswa4->id,
            'sarpras_id' => $speaker->id,
            'petugas_id' => $petugas2->id,
            'jumlah_pinjam' => 1,
            'tgl_pinjam' => Carbon::now()->subDays(1),
            'tgl_kembali_rencana' => Carbon::now()->addDays(2),
            'status' => 'disetujui',
            'keterangan' => 'Untuk acara pramuka',
        ]);

        // Peminjaman 5: Ditolak
        $peminjaman5 = Peminjaman::create([
            'user_id' => $siswa5->id,
            'sarpras_id' => $kamera->id,
            'petugas_id' => $petugas1->id,
            'jumlah_pinjam' => 2,
            'tgl_pinjam' => Carbon::now()->subDays(2),
            'tgl_kembali_rencana' => Carbon::now()->addDays(5),
            'status' => 'ditolak',
            'keterangan' => 'Stok tidak mencukupi',
        ]);

        // Peminjaman 6: Sudah dikembalikan
        $peminjaman6 = Peminjaman::create([
            'user_id' => $siswa1->id,
            'sarpras_id' => $mejaLipat->id,
            'petugas_id' => $petugas2->id,
            'jumlah_pinjam' => 10,
            'tgl_pinjam' => Carbon::now()->subDays(14),
            'tgl_kembali_rencana' => Carbon::now()->subDays(7),
            'status' => 'selesai',
            'keterangan' => 'Untuk acara class meeting',
        ]);

        // Peminjaman 7: Menunggu persetujuan
        $peminjaman7 = Peminjaman::create([
            'user_id' => $siswa2->id,
            'sarpras_id' => $gitarAkustik->id,
            'petugas_id' => null,
            'jumlah_pinjam' => 2,
            'tgl_pinjam' => Carbon::now(),
            'tgl_kembali_rencana' => Carbon::now()->addDays(3),
            'status' => 'menunggu',
            'keterangan' => 'Untuk pentas seni',
        ]);

        // Peminjaman 8: Sedang dipinjam
        $peminjaman8 = Peminjaman::create([
            'user_id' => $siswa3->id,
            'sarpras_id' => $raketBadminton->id,
            'petugas_id' => $petugas1->id,
            'jumlah_pinjam' => 4,
            'tgl_pinjam' => Carbon::now()->subDays(2),
            'tgl_kembali_rencana' => Carbon::now()->addDays(1),
            'status' => 'disetujui',
            'keterangan' => 'Untuk pertandingan badminton antar kelas',
        ]);

        // =============================================
        // PENGEMBALIAN SEEDER
        // =============================================
        
        // Pengembalian untuk peminjaman 2
        Pengembalian::create([
            'peminjaman_id' => $peminjaman2->id,
            'petugas_id' => $petugas1->id,
            'tgl_kembali_aktual' => Carbon::now()->subDays(5),
            'kondisi_akhir' => 'baik',
            'denda' => 0,
        ]);

        // Pengembalian untuk peminjaman 6
        Pengembalian::create([
            'peminjaman_id' => $peminjaman6->id,
            'petugas_id' => $petugas2->id,
            'tgl_kembali_aktual' => Carbon::now()->subDays(7),
            'kondisi_akhir' => 'baik',
            'denda' => 0,
        ]);

        // =============================================
        // OUTPUT INFO
        // =============================================
        
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('   SEEDER BERHASIL DIJALANKAN!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('📊 Data yang telah dibuat:');
        $this->command->info('   - Users       : 8 (1 Admin, 2 Petugas, 5 Siswa)');
        $this->command->info('   - Lokasi      : 6');
        $this->command->info('   - Kategori    : 5');
        $this->command->info('   - Sarpras     : 17');
        $this->command->info('   - Peminjaman  : 8');
        $this->command->info('   - Pengembalian: 2');
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
