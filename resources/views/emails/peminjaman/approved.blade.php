<x-mail::message>
# Peminjaman Disetujui ✅

Halo **{{ $user->name }}**,

Peminjaman Anda telah disetujui! Berikut detailnya:

---

## Kode Peminjaman
<x-mail::panel>
<div style="text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 2px;">
{{ $qrCode }}
</div>
</x-mail::panel>

---

## Detail Peminjaman

| Item | Keterangan |
|:-----|:-----------|
| **Peminjam** | {{ $user->name }} ({{ $user->nomor_induk }}) |
| **Tanggal Pinjam** | {{ $peminjaman->tgl_pinjam->format('d M Y') }} |
| **Rencana Kembali** | {{ $peminjaman->tgl_kembali_rencana->format('d M Y') }} |
| **Jumlah Unit** | {{ $peminjaman->details->count() }} unit |

### Barang yang Dipinjam:
@foreach($peminjaman->details as $detail)
- {{ $detail->sarprasUnit->kode_unit }} - {{ $detail->sarprasUnit->sarpras->nama_barang }}
@endforeach

---

## Instruksi Pengambilan

1. Tunjukkan **kode peminjaman** di atas kepada petugas
2. Atau buka halaman detail peminjaman untuk melihat **QR Code**
3. Petugas akan memverifikasi dan menyerahkan barang

<x-mail::button :url="route('peminjaman.show', $peminjaman)">
Lihat QR Code
</x-mail::button>

---

Terima kasih telah menggunakan Sistem Sarpras SMK.

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
