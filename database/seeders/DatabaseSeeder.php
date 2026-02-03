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
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🧹 Membersihkan database dan memulai seeding baru...');

        // =============================================
        // 1. ITEMS & LOCATIONS (DATA MASTER)
        // =============================================

        // --- LOKASI ---
        $lokasiList = [
            ['nama' => 'Gudang Utama', 'desc' => 'Penyimpanan utama barang elektronik dan ATK'],
            ['nama' => 'Lab Komputer 1', 'desc' => 'Laboratorium Rekayasa Perangkat Lunak'],
            ['nama' => 'Lab Komputer 2', 'desc' => 'Laboratorium Multimedia'],
            ['nama' => 'Ruang Guru', 'desc' => 'Ruang staf pengajar'],
            ['nama' => 'Perpustakaan', 'desc' => 'Pusat sumber belajar'],
            ['nama' => 'Ruang OSIS', 'desc' => 'Ruang organisasi siswa'],
        ];

        $lokasis = [];
        foreach ($lokasiList as $l) {
            $lokasis[$l['nama']] = Lokasi::create([
                'nama_lokasi' => $l['nama'],
                'keterangan' => $l['desc']
            ]);
        }

        // --- KATEGORI ---
        $kategoriList = ['Elektronik', 'Furniture', 'Alat Praktik', 'Olahraga', 'Audio Visual'];
        $kategoris = [];
        foreach ($kategoriList as $k) {
            $kategoris[$k] = Kategori::create(['nama_kategori' => $k]);
        }

        // =============================================
        // 2. USERS (PENGGUNA)
        // =============================================

        // --- SUPER ADMIN ---
        $admin = User::create([
            'name' => 'Super Admin',
            'nomor_induk' => 'ADM001',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'role' => 'admin',
            'kontak' => '08111111111',
            'email_verified_at' => now(),
        ]);

        // --- PETUGAS ---
        $petugas1 = User::create([
            'name' => 'Budi Santoso (Petugas)',
            'nomor_induk' => 'PTG001',
            'email' => 'petugas@gmail.com',
            'password' => 'password',
            'role' => 'petugas',
            'kontak' => '08222222222',
            'email_verified_at' => now(),
        ]);

        // Initialize Faker
        $faker = \Faker\Factory::create('id_ID');

        // --- GURU (3 Orang) ---
        $gurus = [];
        $dataGuru = [
            ['nama' => 'Pak Joko (MTK)', 'nip' => '198001012005011001', 'email' => 'joko@guru.com'],
            ['nama' => 'Bu Siti (B.Indo)', 'nip' => '198502022010012002', 'email' => 'siti@guru.com'],
            ['nama' => 'Pak Andi (Produktif)', 'nip' => '199003032015011003', 'email' => 'andi@guru.com'],
        ];

        foreach ($dataGuru as $g) {
            $gurus[] = User::create([
                'name' => $g['nama'],
                'nomor_induk' => $g['nip'],
                'email' => $g['email'],
                'password' => 'password',
                'role' => 'guru',
                'kontak' => '08333333333',
                'email_verified_at' => now(),
            ]);
        }

        // --- SISWA (5 Orang) ---
        $siswas = [];
        $dataSiswa = [
            ['nama' => 'Rizky (XII RPL 1)', 'nis' => '1001', 'kelas' => 'XII RPL 1', 'email' => 'rizky@siswa.com'],
            ['nama' => 'Sarah (XII RPL 2)', 'nis' => '1002', 'kelas' => 'XII RPL 2', 'email' => 'sarah@siswa.com'],
            ['nama' => 'Dimas (XI TKJ 1)', 'nis' => '1003', 'kelas' => 'XI TKJ 1', 'email' => 'dimas@siswa.com'],
            ['nama' => 'Putri (XI DKV 1)', 'nis' => '1004', 'kelas' => 'XI DKV 1', 'email' => 'putri@siswa.com'],
            ['nama' => 'Bayu (X RPL 1)', 'nis' => '1005', 'kelas' => 'X RPL 1', 'email' => 'bayu@siswa.com'],
        ];

        foreach ($dataSiswa as $s) {
            $siswas[] = User::create([
                'name' => $s['nama'],
                'nomor_induk' => $s['nis'],
                'kelas' => $s['kelas'],
                'email' => $s['email'],
                'password' => 'password',
                'role' => 'siswa',
                'kontak' => '08444444444',
                'email_verified_at' => now(),
            ]);
        }

        // =============================================
        // 3. SARPRAS & UNITS (BARANG)
        // =============================================

        $items = [
            [
                'kode' => 'LPT-ASUS',
                'nama' => 'Laptop ASUS ROG',
                'kategori' => $kategoris['Elektronik']->id,
                'count' => 10,
                'lokasi' => $lokasis['Lab Komputer 1']->id,
                'tipe' => 'asset'
            ],
            [
                'kode' => 'PRJ-EPSON',
                'nama' => 'Proyektor Epson EB-X500',
                'kategori' => $kategoris['Audio Visual']->id,
                'count' => 5,
                'lokasi' => $lokasis['Gudang Utama']->id,
                'tipe' => 'asset'
            ],
            [
                'kode' => 'CAM-CANON',
                'nama' => 'Kamera Canon EOS 3000D',
                'kategori' => $kategoris['Alat Praktik']->id,
                'count' => 3,
                'lokasi' => $lokasis['Lab Komputer 2']->id,
                'tipe' => 'asset'
            ],
            [
                'kode' => 'TRP-XL',
                'nama' => 'Tripod Excell',
                'kategori' => $kategoris['Alat Praktik']->id,
                'count' => 5,
                'lokasi' => $lokasis['Lab Komputer 2']->id,
                'tipe' => 'asset'
            ],
            [
                'kode' => 'SPK-JBL',
                'nama' => 'Speaker JBL PartyBox',
                'kategori' => $kategoris['Audio Visual']->id,
                'count' => 2,
                'lokasi' => $lokasis['Ruang OSIS']->id,
                'tipe' => 'asset'
            ],
            // Bahan Habis Pakai
            [
                'kode' => 'KBL-UTP',
                'nama' => 'Kabel UTP Cat6 (Roll)',
                'kategori' => $kategoris['Elektronik']->id,
                'count' => 10,
                'lokasi' => $lokasis['Gudang Utama']->id,
                'tipe' => 'bahan'
            ]
        ];

        $allUnits = []; // Store usable units for loans

        foreach ($items as $item) {
            $sarpras = Sarpras::create([
                'kode_barang' => $item['kode'],
                'nama_barang' => $item['nama'],
                'kategori_id' => $item['kategori'],
                'tipe' => $item['tipe'],
                'foto' => null // Bisa diisi path dummy jika ada
            ]);

            // Create Units
            for ($i = 1; $i <= $item['count']; $i++) {
                $unit = SarprasUnit::create([
                    'sarpras_id' => $sarpras->id,
                    'kode_unit' => $item['kode'] . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'lokasi_id' => $item['lokasi'],
                    'kondisi' => 'baik',
                    'status' => 'tersedia',
                    'tanggal_perolehan' => now()->subMonths(rand(1, 24)),
                ]);
                $allUnits[$item['kode']][] = $unit;
            }
        }

        // =============================================
        // 4. PEMINJAMAN (LOAN HISTORY)
        // =============================================

        // CASE 1: Siswa Pinjam Laptop (SUDAH SELESAI / DIKEMBALIKAN)
        // -----------------------------------------------------------
        $peminjam = $siswas[0]; // Rizky
        $unitLaptop = $allUnits['LPT-ASUS'][0];

        $loan1 = Peminjaman::create([
            'user_id' => $peminjam->id,
            'petugas_id' => $petugas1->id,
            'tgl_pinjam' => now()->subDays(5),
            'tgl_kembali_rencana' => now()->subDays(4),
            'status' => 'selesai',
            'keterangan' => 'Mengerjakan tugas coding',
            'qr_code' => 'QR-' . uniqid(),
        ]);

        PeminjamanDetail::create([
            'peminjaman_id' => $loan1->id,
            'sarpras_unit_id' => $unitLaptop->id,
        ]);

        // Pengembalian
        $return1 = Pengembalian::create([
            'peminjaman_id' => $loan1->id,
            'petugas_id' => $petugas1->id,
            'tgl_kembali_aktual' => now()->subDays(4), // Tepat waktu
        ]);

        PengembalianDetail::create([
            'pengembalian_id' => $return1->id,
            'sarpras_unit_id' => $unitLaptop->id,
            'kondisi_akhir' => 'baik',
        ]);


        // CASE 2: Guru Pinjam Proyektor (SEDANG DIPINJAM - MASIH AMAN)
        // -------------------------------------------------------------
        $peminjam = $gurus[0]; // Pak Joko
        $unitProyektor = $allUnits['PRJ-EPSON'][0];
        $unitProyektor->update(['status' => 'dipinjam']);

        $loan2 = Peminjaman::create([
            'user_id' => $peminjam->id,
            'petugas_id' => $petugas1->id,
            'tgl_pinjam' => now()->subHours(5),
            'tgl_kembali_rencana' => now()->addDays(1), // Besok kembali
            'status' => 'disetujui',
            'keterangan' => 'Mengajar Matematika di X RPL 1',
            'qr_code' => 'QR-' . uniqid(),
        ]);

        PeminjamanDetail::create([
            'peminjaman_id' => $loan2->id,
            'sarpras_unit_id' => $unitProyektor->id,
        ]);


        // CASE 3: Siswa Pinjam Kamera (TERLAMBAT - DANGER)
        // -------------------------------------------------------------
        $peminjam = $siswas[3]; // Putri
        $unitKamera = $allUnits['CAM-CANON'][0];
        $unitKamera->update(['status' => 'dipinjam']);

        $loan3 = Peminjaman::create([
            'user_id' => $peminjam->id,
            'petugas_id' => $petugas1->id,
            'tgl_pinjam' => now()->subDays(10),
            'tgl_kembali_rencana' => now()->subDays(7), // Sudah lewat 3 hari
            'status' => 'disetujui',
            'keterangan' => 'Dokumentasi kegiatan sekolah',
            'qr_code' => 'QR-' . uniqid(),
        ]);

        PeminjamanDetail::create([
            'peminjaman_id' => $loan3->id,
            'sarpras_unit_id' => $unitKamera->id,
        ]);


        // CASE 4: Guru Pinjam Speaker (MENUNGGU PERSETUJUAN)
        // -------------------------------------------------------------
        $peminjam = $gurus[1]; // Bu Siti
        $unitSpeaker = $allUnits['SPK-JBL'][0];

        $loan4 = Peminjaman::create([
            'user_id' => $peminjam->id,
            'petugas_id' => null, // Belum disetujui
            'tgl_pinjam' => now(), // Hari ini
            'tgl_kembali_rencana' => now()->addDays(1),
            'status' => 'menunggu',
            'keterangan' => 'Latihan drama bahasa indonesia',
            'qr_code' => null,
        ]);

        PeminjamanDetail::create([
            'peminjaman_id' => $loan4->id,
            'sarpras_unit_id' => $unitSpeaker->id,
        ]);


        // CASE 5: Siswa Request Alat Tapi Ditolak
        // -------------------------------------------------------------
        $peminjam = $siswas[4]; // Bayu
        $unitLaptop2 = $allUnits['LPT-ASUS'][5];

        $loan5 = Peminjaman::create([
            'user_id' => $peminjam->id,
            'petugas_id' => $petugas1->id,
            'tgl_pinjam' => now()->subDays(1),
            'tgl_kembali_rencana' => now()->addDays(1),
            'status' => 'ditolak',
            'keterangan' => 'Main game',
            'catatan_petugas' => 'Peminjaman hanya untuk keperluan akademik',
            'qr_code' => null,
        ]);

        PeminjamanDetail::create([
            'peminjaman_id' => $loan5->id,
            'sarpras_unit_id' => $unitLaptop2->id,
        ]);

        // =============================================
        // 5. MAINTENANCE
        // =============================================

        $unitRusak = $allUnits['LPT-ASUS'][9];
        $unitRusak->update(['status' => 'maintenance', 'kondisi' => 'rusak_ringan']);

        Maintenance::create([
            'sarpras_unit_id' => $unitRusak->id,
            'petugas_id' => $petugas1->id,
            'jenis' => 'perbaikan',
            'deskripsi' => 'Keyboard tombol "A" lepas',
            'tanggal_mulai' => now()->subDays(2),
            'status' => 'sedang_berlangsung',
            'biaya' => 50000
        ]);

        $this->command->info('✅ Seeding selesai! Database refreshed with minimal data.');
        $this->command->info("   - Admin: admin@gmail.com | password");
        $this->command->info("   - Guru: joko@guru.com | password (NIP: 198001012005011001)");
        $this->command->info("   - Siswa: rizky@siswa.com | password (NIS: 1001)");
        $this->command->info("   - Petugas: petugas@gmail.com | password");
    }
}
