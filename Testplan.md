---
project: Sistem Manajemen Sarana Prasarana (Sarpras)
document_type: Test Case Specification
total_test_cases: 60
version: 1.0
last_updated: 2026-02-04
---

# Test Case Dokumen: Sistem Manajemen Sarpras

Dokumen ini berisi daftar test case untuk pengujian sistem, mencakup fitur Autentikasi, Master Data, Peminjaman, Pengembalian, Pengaduan, Laporan, hingga fitur Tier 2 dan 3 (Advanced).

## Legenda
- **Tier 1:** Fitur Utama / MVP.
- **Tier 2:** Fitur Tambahan / Inspeksi.
- **Tier 3:** Fitur Lanjutan / Integrasi & AI.
- **Bobot:** Kompleksitas atau prioritas test case (1-3).

---

## Tabel Test Case

| No | ID | Tier | Bobot | Fitur | Deskripsi Test Case | Expected Result | Status (Pass/Fail) |
|:--:|:--|:--:|:--:|:--|:--|:--|:--:|
| 1 | T1-AUTH-001 | 1 | 2 | Login | Login dengan username & password valid | Berhasil login, redirect ke dashboard sesuai role | |
| 2 | T1-AUTH-002 | 1 | 1 | Login | Login dengan password salah | Gagal login, pesan error tampil, tidak ada session | |
| 3 | T1-AUTH-003 | 1 | 1 | Logout | Klik logout dari dashboard | Session terhapus, redirect ke halaman login | |
| 4 | T1-AUTH-004 | 1 | 2 | CRUD User | Admin create user baru (username, password, role, email) | User baru tersimpan, password ter-hash, bisa login dengan role yang benar | |
| 5 | T1-AUTH-005 | 1 | 1 | CRUD User | Admin edit data user (nama/role) | Data user update, menu dan hak akses berubah sesuai role baru | |
| 6 | T1-AUTH-006 | 1 | 1 | CRUD User | Admin delete user | User hilang dari list, tidak dapat login lagi | |
| 7 | T1-AUTH-007 | 1 | 1 | Ganti Password | User mengganti password sendiri | Login dengan password lama gagal, dengan password baru berhasil | |
| 8 | T1-MASTER-001 | 1 | 1 | Kategori Sarpras | Admin tambah kategori baru | Kategori tersimpan dan muncul di dropdown saat input alat | |
| 9 | T1-MASTER-002 | 1 | 1 | Kategori Sarpras | Admin edit nama kategori | Nama kategori berubah di database & UI | |
| 10 | T1-MASTER-003 | 1 | 1 | Kategori Sarpras | Admin menghapus kategori tanpa relasi alat | Kategori terhapus, tidak muncul di dropdown | |
| 11 | T1-MASTER-004 | 1 | 2 | Data Alat/Buku | Admin create sarpras (kode, nama, kategori, lokasi, stok, kondisi awal) | Record alat tersimpan lengkap, stok & kondisi awal tercatat | |
| 12 | T1-MASTER-005 | 1 | 1 | Data Alat/Buku | Admin edit data alat | Data alat berubah di database & list | |
| 13 | T1-MASTER-006 | 1 | 1 | Data Alat/Buku | Admin delete alat | Alat hilang dari list dan tidak dapat dipinjam | |
| 14 | T1-MASTER-007 | 1 | 1 | View Sarpras | Admin/Petugas/Pengguna melihat daftar sarpras | List alat tampil dengan kolom: kode, nama, kategori, lokasi, stok, kondisi | |
| 15 | T1-PINJAM-001 | 1 | 1 | View Alat Tersedia | Pengguna umum buka halaman peminjaman | Hanya alat dengan stok > 0 yang muncul / dapat dipilih | |
| 16 | T1-PINJAM-002 | 1 | 2 | Ajukan Peminjaman | Pengguna mengajukan pinjam (pilih alat, jumlah, tgl pinjam, tgl kembali rencana, tujuan) | Peminjaman tersimpan dengan status “Menunggu Persetujuan” | |
| 17 | T1-PINJAM-003 | 1 | 1 | Validasi Tanggal | Pengguna isi tgl kembali < tgl pinjam | Form ditolak, pesan error “tanggal kembali harus >= tanggal pinjam” | |
| 18 | T1-PINJAM-004 | 1 | 1 | Riwayat Peminjaman | Pengguna buka riwayat peminjaman sendiri | Semua peminjaman user tampil dengan status yang benar | |
| 19 | T1-PINJAM-005 | 1 | 2 | Approve Peminjaman | Admin/Petugas menyetujui peminjaman status “Menunggu” | Status → “Disetujui/Aktif”, stok berkurang, data siap dicetak sebagai bukti | |
| 20 | T1-PINJAM-006 | 1 | 1 | Reject Peminjaman | Admin/Petugas menolak peminjaman, mengisi alasan | Status → “Ditolak”, stok tidak berubah, alasan tersimpan dan terlihat peminjam | |
| 21 | T1-PINJAM-007 | 1 | 2 | Bukti + QR Code | Setelah approve, Admin/Petugas mencetak bukti peminjaman | Bukti berisi nomor unik dan QR code yang dapat di-scan untuk proses pengembalian | |
| 22 | T1-PINJAM-008 | 1 | 2 | Double Booking Check | Peminjaman alat X pada tgl 10–15 sudah disetujui, user lain coba ajukan 12–18 | Sistem mencegah booking overlapping (error, atau tanggal tersebut tidak dapat dipilih) | |
| 23 | T1-PINJAM-009 | 1 | 1 | Daftar Peminjaman | Admin/Petugas melihat daftar peminjaman aktif | Hanya peminjaman status aktif yang tampil, bisa filter by user/alats | |
| 24 | T1-PINJAM-010 | 1 | 2 | Update Stok Otomatis | Amati stok sebelum & sesudah approve peminjaman dan pengembalian | Stok otomatis berkurang saat approve, dan bertambah kembali saat pengembalian | |
| 25 | T1-KEMBALI-001 | 1 | 2 | Scan QR Pengembalian | Petugas scan QR di bukti peminjaman saat pengembalian | Form pengembalian otomatis terisi data peminjaman yang benar | |
| 26 | T1-KEMBALI-002 | 1 | 3 | Inspeksi Kondisi Alat | Petugas mengisi status kondisi: Baik / Rusak Ringan / Rusak Berat / Hilang | Kondisi tersimpan dan mengupdate status alat di data master | |
| 27 | T1-KEMBALI-003 | 1 | 2 | Deskripsi Kerusakan | Jika status bukan “Baik”, Petugas mengisi deskripsi kerusakan | Catatan kerusakan tersimpan sebagai riwayat | |
| 28 | T1-KEMBALI-004 | 1 | 1 | Foto Pengembalian | Petugas upload foto kondisi alat saat kembali | Foto tersimpan dan dapat dilihat kembali di riwayat pengembalian | |
| 29 | T1-KEMBALI-005 | 1 | 2 | Riwayat Pengembalian | Admin/Petugas melihat riwayat pengembalian suatu alat | List pengembalian tampil: tanggal, kondisi, deskripsi, foto | |
| 30 | T1-KEMBALI-006 | 1 | 2 | Update Kondisi Master | Setelah pengembalian, cek record alat di master | Field “kondisi_saat_ini” menyesuaikan kondisi terakhir pengembalian | |
| 31 | T1-KEMBALI-007 | 1 | 1 | Alat Hilang | Petugas menandai status pengembalian “Hilang” | Alat ter-flag hilang, tidak bisa dipinjam lagi (atau muncul sebagai item khusus di laporan) | |
| 32 | T1-ADUAN-001 | 1 | 1 | Ajukan Pengaduan | Pengguna ajukan pengaduan (judul, deskripsi, lokasi, jenis sarpras) | Pengaduan tersimpan dengan status awal “Belum Ditindaklanjuti” | |
| 33 | T1-ADUAN-002 | 1 | 1 | Riwayat Aduan | Pengguna melihat daftar pengaduan miliknya | Hanya pengaduan user tersebut yang tampil | |
| 34 | T1-ADUAN-003 | 1 | 1 | List Semua Aduan | Admin/Petugas melihat semua pengaduan | List semua pengaduan dengan opsi filter status/lokasi | |
| 35 | T1-ADUAN-004 | 1 | 1 | Ubah Status Aduan | Admin/Petugas ubah status (Belum → Diproses → Selesai/Ditutup) | Status berubah dan dapat dilihat oleh pelapor | |
| 36 | T1-ADUAN-005 | 1 | 1 | Catatan Aduan | Admin/Petugas menambah catatan tindak lanjut | Catatan tersimpan dengan siapa & kapan | |
| 37 | T1-REPORT-001 | 1 | 2 | Laporan Peminjaman | Admin generate laporan peminjaman dengan filter periode | Tabel peminjaman akurat, bisa export (opsional PDF/Excel) | |
| 38 | T1-REPORT-002 | 1 | 1 | Laporan Pengaduan | Admin generate laporan pengaduan dengan filter status | Tabel pengaduan akurat, bisa filter status | |
| 39 | T1-REPORT-003 | 1 | 3 | Asset Health Report | Admin akses halaman laporan kesehatan aset | Menampilkan: daftar alat rusak, alat sering rusak, alat hilang, dsb. (berbasis data pengembalian & kondisi) | |
| 40 | T1-REPORT-004 | 1 | 2 | Asset Health – Rusak | Bagian khusus daftar alat berstatus rusak | List alat rusak dengan info: kondisi, sejak kapan, lokasi, catatan | |
| 41 | T1-REPORT-005 | 1 | 2 | Asset Health – Top 10 | Bagian Top 10 alat paling sering rusak | Ranking Top 10 dengan jumlah kerusakan per alat | |
| 42 | T1-REPORT-006 | 1 | 1 | Asset Health – Hilang | Bagian khusus alat yang berstatus hilang | List alat hilang dengan peminjam terakhir dan tanggal kejadian | |
| 43 | T1-SYS-001 | 1 | 2 | Privilege Check | Login sebagai Pengguna umum, akses URL menu admin (misal /admin/users) | Akses ditolak (403/redirect), menu admin tidak terlihat | |
| 44 | T1-SYS-002 | 1 | 2 | Role-Based Menu | Login sebagai Admin, Petugas, Pengguna; bandingkan menu | Menu berbeda per role (Admin paling lengkap, Pengguna paling terbatas) | |
| 45 | T1-SYS-003 | 1 | 2 | Activity Log | Lakukan beberapa aksi; Admin buka halaman activity log | Setiap aksi (login, peminjaman, pengembalian, pengaduan) tercatat lengkap dengan waktu & user | |
| 46 | T1-SYS-004 | 1 | 1 | Validasi Input | Uji form dengan data kosong/format salah | Form menolak input, pesan error jelas, data tidak tersimpan | |
| 47 | T1-SYS-005 | 1 | 1 | UI/UX Responsif | Uji tampilan di resolusi berbeda | Layout rapi, navigasi jelas, tidak ada elemen yang rusak | |
| 48 | T2-INSPECT-001 | 2 | 2 | Checklist Inspeksi | Petugas isi checklist kondisi awal alat sebelum dipinjam | Checklist tersimpan per alat & per peminjaman | |
| 49 | T2-INSPECT-002 | 2 | 1 | Foto Baseline | Petugas upload foto kondisi awal | Foto tersimpan dan bisa dibandingkan saat kembali | |
| 50 | T2-INSPECT-003 | 2 | 2 | Perbandingan Kondisi | Saat pengembalian, tampil perbandingan checklist sebelum vs sesudah | Item yang berubah di-highlight | |
| 51 | T3-NOTIF-001 | 3 | 2 | Notifikasi Approval | Saat peminjaman disetujui, kirim notifikasi WA/Email/Telegram | Notifikasi terkirim dengan informasi peminjaman | |
| 52 | T3-NOTIF-002 | 3 | 1 | Reminder Jatuh Tempo | H-1 sebelum tgl kembali, kirim reminder | Reminder terkirim tepat waktu | |
| 53 | T3-QR-001 | 3 | 2 | QR Code Assets | Scan QR alat untuk isi form peminjaman/pengembalian | Data alat auto-terisi dengan benar | |
| 54 | T3-CLOUD-001 | 3 | 1 | Cloud Storage Foto | Upload foto ke cloud storage, simpan URL di database | Foto dapat diakses via URL | |
| 55 | T3-MULTI-LOC-001 | 3 | 2 | Multi-Location | Kelola beberapa lokasi/ruang dengan inventory & petugas masing2 | Data & hak akses terpisah per lokasi | |
| 56 | T3-ANALYTICS-PRED-001 | 3 | 2 | Predict Failure Rate | Laporan prediksi alat berisiko rusak dalam 30 hari | Daftar alat dengan level risiko | |
| 57 | T3-ANALYTICS-LIFECYCLE-001 | 3 | 2 | Asset Lifecycle | Laporan umur, biaya maintenance, rekomendasi | Rekomendasi “keep/repair/replace” per alat | |
| 58 | T3-DASHBOARD-EXEC-001 | 3 | 2 | Executive Dashboard | Dashboard KPI (total aset, % sehat, dsb.) | KPI dan chart tampil akurat | |
| 59 | T3-RBAC-001 | 3 | 2 | Granular RBAC | 6 role dengan hak akses berbeda | Setiap role hanya bisa akses fitur yang diizinkan | |
| 60 | T3-SECURITY-001 | 3 | 2 | Security Hardening | Uji SQLi, XSS, CSRF, proteksi password, dsb. | Serangan umum tertahan, tidak ada vulnerability besar | |