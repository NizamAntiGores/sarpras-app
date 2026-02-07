<x-mail::message>
# Peminjaman Disetujui ✅

Halo **{{ $user->name }}**,

Peminjaman Anda telah disetujui! Berikut detailnya:

---

## Kode Peminjaman
<x-mail::panel>
<div style="text-align: center;">
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $qrCode }}" alt="QR Code" style="width: 200px; height: 200px;">
    <p style="margin-top: 15px; font-size: 14px; color: #555;">
        <strong>QR Code ini berlaku untuk<br>PENGAMBILAN dan PENGEMBALIAN barang.</strong><br>
        Harap simpan email ini.
    </p>
    <div style="margin-top: 10px; font-size: 18px; font-weight: bold; letter-spacing: 2px; color: #333;">
        {{ $qrCode }}
    </div>
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
@if($detail->sarprasUnit)
- {{ $detail->sarprasUnit->kode_unit }} - {{ $detail->sarprasUnit->sarpras->nama_barang }}
@else
- {{ $detail->quantity }}x {{ $detail->sarpras->nama_barang }}
@endif
@endforeach

---

## Instruksi Pengambilan

1. Tunjukkan **kode peminjaman** di atas kepada petugas
2. Petugas akan memverifikasi dan menyerahkan barang

---

Terima kasih telah menggunakan Sistem Sarpras SMK.

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
