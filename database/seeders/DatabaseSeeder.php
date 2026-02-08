<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Sarpras;
use App\Models\SarprasUnit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Memulai seeding database...');

        // =============================================
        // 1. USERS (Admin & Petugas only)
        // =============================================
        $this->command->info('👥 Membuat user admin & petugas...');

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'nomor_induk' => 'ADM001',
            'role' => 'admin',
            'kontak' => '081234567890',
            'email_verified_at' => now(),
        ]);

        $petugas = User::create([
            'name' => 'Petugas Sarpras',
            'email' => 'petugas@gmail.com',
            'password' => 'password',
            'nomor_induk' => 'PTG001',
            'role' => 'petugas',
            'kontak' => '081234567891',
            'email_verified_at' => now(),
        ]);

        // =============================================
        // 2. LOKASI
        // =============================================
        $this->command->info('📍 Membuat data lokasi...');

        $lokasiList = [
            'Ruang Kelas X-A',
            'Ruang Kelas X-B',
            'Ruang Kelas XI-A',
            'Ruang Kelas XI-B',
            'Ruang Kelas XII-A',
            'Lab Komputer 1',
            'Lab Komputer 2',
            'Lab Multimedia',
            'Perpustakaan',
            'Ruang Guru',
            'Ruang TU' => true, // Storefront
            'Aula',
            'Gudang Utama',
        ];

        $lokasis = [];
        foreach ($lokasiList as $key => $value) {
            $isStorefront = is_bool($value) ? $value : false;
            $nama = is_bool($value) ? $key : $value;

            $lokasis[$nama] = Lokasi::create([
                'nama_lokasi' => $nama,
                'is_storefront' => $isStorefront
            ]);
        }

        // =============================================
        // 3. KATEGORI
        // =============================================
        $this->command->info('📂 Membuat data kategori...');

        $kategoriList = ['Elektronik', 'Furniture', 'Alat Praktik', 'Olahraga', 'Audio Visual', 'ATK'];
        $kategoris = [];
        foreach ($kategoriList as $nama) {
            $kategoris[$nama] = Kategori::create(['nama_kategori' => $nama]);
        }

        // =============================================
        // 4. SARPRAS & UNITS
        // =============================================
        $this->command->info('📦 Membuat data barang & unit...');

        // --- BARANG TIPE ASSET (ada kode unik per unit) ---
        $assetsData = [
            [
                'nama' => 'Laptop ASUS VivoBook',
                'kategori' => 'Elektronik',
                'units' => [
                    ['kode' => 'LPT-001', 'lokasi' => 'Lab Komputer 1'],
                    ['kode' => 'LPT-002', 'lokasi' => 'Lab Komputer 1'],
                    ['kode' => 'LPT-003', 'lokasi' => 'Lab Komputer 1'],
                    ['kode' => 'LPT-004', 'lokasi' => 'Lab Komputer 2'],
                    ['kode' => 'LPT-005', 'lokasi' => 'Ruang TU'], // Available in Storefront
                ],
            ],
            [
                'nama' => 'Proyektor Epson',
                'kategori' => 'Audio Visual',
                'units' => [
                    ['kode' => 'PRJ-001', 'lokasi' => 'Ruang Kelas X-A'],
                    ['kode' => 'PRJ-002', 'lokasi' => 'Ruang Kelas XI-A'],
                    ['kode' => 'PRJ-003', 'lokasi' => 'Ruang TU'], // Available in Storefront
                ],
            ],
            [
                'nama' => 'Kamera DSLR Canon',
                'kategori' => 'Audio Visual',
                'units' => [
                    ['kode' => 'CAM-001', 'lokasi' => 'Lab Multimedia'],
                    ['kode' => 'CAM-002', 'lokasi' => 'Lab Multimedia'],
                ],
            ],
            [
                'nama' => 'Meja Siswa',
                'kategori' => 'Furniture',
                'units' => [
                    ['kode' => 'MJS-001', 'lokasi' => 'Ruang Kelas X-A'],
                    ['kode' => 'MJS-002', 'lokasi' => 'Ruang Kelas X-A'],
                    ['kode' => 'MJS-003', 'lokasi' => 'Ruang Kelas X-B'],
                    ['kode' => 'MJS-004', 'lokasi' => 'Ruang Kelas X-B'],
                ],
            ],
            [
                'nama' => 'Kursi Siswa',
                'kategori' => 'Furniture',
                'units' => [
                    ['kode' => 'KRS-001', 'lokasi' => 'Ruang Kelas X-A'],
                    ['kode' => 'KRS-002', 'lokasi' => 'Ruang Kelas X-A'],
                    ['kode' => 'KRS-003', 'lokasi' => 'Ruang Kelas X-B'],
                    ['kode' => 'KRS-004', 'lokasi' => 'Ruang Kelas X-B'],
                ],
            ],
            [
                'nama' => 'Multimeter Digital',
                'kategori' => 'Alat Praktik',
                'units' => [
                    ['kode' => 'MTR-001', 'lokasi' => 'Lab Komputer 1'],
                    ['kode' => 'MTR-002', 'lokasi' => 'Lab Komputer 1'],
                    ['kode' => 'MTR-003', 'lokasi' => 'Ruang TU'], // Storefront
                ],
            ],
        ];

        $kodeBarang = 1;
        foreach ($assetsData as $asset) {
            $sarpras = Sarpras::create([
                'kode_barang' => 'SRP' . str_pad($kodeBarang++, 4, '0', STR_PAD_LEFT),
                'nama_barang' => $asset['nama'],
                'kategori_id' => $kategoris[$asset['kategori']]->id,
                'tipe' => 'asset',
                'deskripsi' => 'Barang inventaris ' . $asset['nama'],
            ]);

            foreach ($asset['units'] as $unit) {
                SarprasUnit::create([
                    'sarpras_id' => $sarpras->id,
                    'kode_unit' => $unit['kode'],
                    'lokasi_id' => $lokasis[$unit['lokasi']]->id,
                    'kondisi' => SarprasUnit::KONDISI_BAIK,
                    'status' => SarprasUnit::STATUS_TERSEDIA,
                    'tanggal_perolehan' => now()->subYears(rand(0, 4)),
                ]);
            }
        }

        // --- BARANG OLAHRAGA (asset, bukan habis pakai) ---
        $olahragaAssets = [
            [
                'nama' => 'Bola Voli Mikasa',
                'kategori' => 'Olahraga',
                'units' => [
                    ['kode' => 'BLV-001', 'lokasi' => 'Gudang Utama'],
                    ['kode' => 'BLV-002', 'lokasi' => 'Gudang Utama'],
                    ['kode' => 'BLV-003', 'lokasi' => 'Gudang Utama'],
                    ['kode' => 'BLV-004', 'lokasi' => 'Ruang TU'], // Available
                    ['kode' => 'BLV-005', 'lokasi' => 'Ruang TU'], // Available
                ],
            ],
            [
                'nama' => 'Bola Basket Molten',
                'kategori' => 'Olahraga',
                'units' => [
                    ['kode' => 'BLB-001', 'lokasi' => 'Gudang Utama'],
                    ['kode' => 'BLB-002', 'lokasi' => 'Gudang Utama'],
                    ['kode' => 'BLB-003', 'lokasi' => 'Ruang TU'], // Available
                    ['kode' => 'BLB-004', 'lokasi' => 'Ruang TU'], // Available
                ],
            ],
            [
                'nama' => 'Raket Badminton Yonex',
                'kategori' => 'Olahraga',
                'units' => [
                    ['kode' => 'RKT-001', 'lokasi' => 'Gudang Utama'],
                    ['kode' => 'RKT-002', 'lokasi' => 'Gudang Utama'],
                    ['kode' => 'RKT-003', 'lokasi' => 'Gudang Utama'],
                    ['kode' => 'RKT-004', 'lokasi' => 'Ruang TU'], // Available
                    ['kode' => 'RKT-005', 'lokasi' => 'Ruang TU'], // Available
                    ['kode' => 'RKT-006', 'lokasi' => 'Ruang TU'], // Available
                ],
            ],
            [
                'nama' => 'Net Voli',
                'kategori' => 'Olahraga',
                'units' => [
                    ['kode' => 'NTV-001', 'lokasi' => 'Gudang Utama'],
                    ['kode' => 'NTV-002', 'lokasi' => 'Gudang Utama'],
                ],
            ],
        ];

        foreach ($olahragaAssets as $asset) {
            $sarpras = Sarpras::create([
                'kode_barang' => 'SRP' . str_pad($kodeBarang++, 4, '0', STR_PAD_LEFT),
                'nama_barang' => $asset['nama'],
                'kategori_id' => $kategoris[$asset['kategori']]->id,
                'tipe' => 'asset',
                'deskripsi' => 'Peralatan olahraga ' . $asset['nama'],
            ]);

            foreach ($asset['units'] as $unit) {
                SarprasUnit::create([
                    'sarpras_id' => $sarpras->id,
                    'kode_unit' => $unit['kode'],
                    'lokasi_id' => $lokasis[$unit['lokasi']]->id,
                    'kondisi' => SarprasUnit::KONDISI_BAIK,
                    'status' => SarprasUnit::STATUS_TERSEDIA,
                    'tanggal_perolehan' => now()->subYears(rand(0, 2)),
                ]);
            }
        }

        // --- BARANG TIPE BAHAN (consumable, habis pakai) ---
        $bahanData = [
            [
                'nama' => 'Spidol Whiteboard',
                'kategori' => 'ATK',
                'jumlah' => 50,
                'lokasi' => 'Ruang TU', // Storefront
            ],
            [
                'nama' => 'Penghapus Papan Tulis',
                'kategori' => 'ATK',
                'jumlah' => 20,
                'lokasi' => 'Ruang TU', // Storefront
            ],
            [
                'nama' => 'Shuttlecock',
                'kategori' => 'Olahraga',
                'jumlah' => 30,
                'lokasi' => 'Gudang Utama',
            ],
            [
                'nama' => 'Kapur Tulis',
                'kategori' => 'ATK',
                'jumlah' => 100,
                'lokasi' => 'Gudang Utama',
            ],
            [
                'nama' => 'Toner Printer',
                'kategori' => 'ATK',
                'jumlah' => 10,
                'lokasi' => 'Ruang TU',
            ],
            [
                'nama' => 'Kabel HDMI 2m',
                'kategori' => 'Elektronik',
                'jumlah' => 15,
                'lokasi' => 'Lab Multimedia',
            ],
        ];

        foreach ($bahanData as $bahan) {
            $sarpras = Sarpras::create([
                'kode_barang' => 'SRP' . str_pad($kodeBarang++, 4, '0', STR_PAD_LEFT),
                'nama_barang' => $bahan['nama'],
                'kategori_id' => $kategoris[$bahan['kategori']]->id,
                'tipe' => 'bahan',
                'deskripsi' => 'Barang habis pakai ' . $bahan['nama'],
            ]);

            // Create ItemStock for Consumables
            \App\Models\ItemStock::create([
                'sarpras_id' => $sarpras->id,
                'lokasi_id' => $lokasis[$bahan['lokasi']]->id,
                'quantity' => $bahan['jumlah'],
            ]);
        }

        // =============================================
        // SELESAI
        // =============================================
        $this->command->newLine();
        $this->command->info('✅ Seeding selesai!');
        $this->command->table(
            ['Akun', 'Email', 'Password'],
            [
                ['Admin', 'admin@gmail.com', 'password'],
                ['Petugas', 'petugas@gmail.com', 'password'],
            ]
        );
    }
}
