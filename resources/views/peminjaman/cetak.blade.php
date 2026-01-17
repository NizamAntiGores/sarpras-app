<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bukti Peminjaman - {{ $peminjaman->qr_code ?? 'PJM-' . $peminjaman->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #1a365d;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .header h1 {
            font-size: 18px;
            color: #1a365d;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #4a5568;
        }
        .header p {
            font-size: 10px;
            color: #718096;
            margin-top: 5px;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h3 {
            font-size: 13px;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .info-table .label {
            width: 40%;
            color: #718096;
        }
        .info-table .value {
            width: 60%;
            font-weight: 500;
        }
        .two-columns {
            width: 100%;
            border-collapse: collapse;
        }
        .two-columns td {
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }
        .qr-section {
            text-align: center;
            margin: 25px 0;
            padding: 15px;
            background-color: #f7fafc;
            border-radius: 8px;
        }
        .qr-section p {
            font-size: 10px;
            color: #718096;
            margin-top: 10px;
        }
        .qr-code-text {
            font-family: monospace;
            font-size: 14px;
            font-weight: bold;
            color: #1a365d;
            margin-top: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-disetujui {
            background-color: #c6f6d5;
            color: #22543d;
        }
        .status-selesai {
            background-color: #bee3f8;
            color: #2a4365;
        }
        .notes {
            background-color: #fffbeb;
            border: 1px solid #f6e05e;
            padding: 12px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .notes h4 {
            font-size: 11px;
            color: #744210;
            margin-bottom: 5px;
        }
        .notes p {
            font-size: 10px;
            color: #744210;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-table {
            width: 100%;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
        }
        .signature-box {
            height: 60px;
            border-bottom: 1px solid #333;
            margin: 0 20px 5px 20px;
        }
        .signature-name {
            font-size: 11px;
        }
        .signature-role {
            font-size: 10px;
            color: #718096;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        .footer p {
            font-size: 9px;
            color: #a0aec0;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header Sekolah --}}
        <div class="header">
            <h1>SMKN 1 BOYOLANGU</h1>
            <h2>Unit Sarana dan Prasarana</h2>
            <p>Jl. Pendidikan No. 12, Boyolangu, Kab. Tulungagung, Jawa Timur 66233</p>
            <p>Telp. (0355) 321123 | Email: smkn1boyolangu@sch.id</p>
        </div>

        {{-- Title --}}
        <div class="title">Bukti Peminjaman Barang</div>

        {{-- QR Code Section --}}
        <div class="qr-section">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="120" height="120">
            <p>Scan QR Code untuk verifikasi</p>
            <div class="qr-code-text">{{ $peminjaman->qr_code ?? 'PJM-' . $peminjaman->id }}</div>
        </div>

        {{-- Info Peminjam & Barang --}}
        <table class="two-columns">
            <tr>
                <td>
                    <div class="info-section">
                        <h3>Informasi Peminjam</h3>
                        <table class="info-table">
                            <tr>
                                <td class="label">Nama</td>
                                <td class="value">{{ $peminjaman->user->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Email</td>
                                <td class="value">{{ $peminjaman->user->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Kontak</td>
                                <td class="value">{{ $peminjaman->user->kontak ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td>
                    <div class="info-section">
                        <h3>Informasi Barang</h3>
                        <table class="info-table">
                            <tr>
                                <td class="label">Nama Barang</td>
                                <td class="value">{{ $peminjaman->sarpras->nama_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Kode Barang</td>
                                <td class="value">{{ $peminjaman->sarpras->kode_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Kategori</td>
                                <td class="value">{{ $peminjaman->sarpras->kategori->nama_kategori ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Info Peminjaman --}}
        <div class="info-section">
            <h3>Detail Peminjaman</h3>
            <table class="info-table">
                <tr>
                    <td class="label">Jumlah Dipinjam</td>
                    <td class="value" style="font-size: 16px; color: #3182ce;">{{ $peminjaman->jumlah_pinjam }} unit</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Pinjam</td>
                    <td class="value">{{ $peminjaman->tgl_pinjam->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Rencana Kembali</td>
                    <td class="value">{{ $peminjaman->tgl_kembali_rencana->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Diproses Oleh</td>
                    <td class="value">{{ $peminjaman->petugas->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Status</td>
                    <td class="value">
                        <span class="status-badge status-{{ $peminjaman->status }}">
                            {{ ucfirst($peminjaman->status) }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        @if ($peminjaman->keterangan)
        <div class="notes">
            <h4>Catatan:</h4>
            <p>{{ $peminjaman->keterangan }}</p>
        </div>
        @endif

        {{-- Signature Section --}}
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-box"></div>
                        <div class="signature-name">{{ $peminjaman->user->name ?? '________________' }}</div>
                        <div class="signature-role">Peminjam</div>
                    </td>
                    <td>
                        <div class="signature-box"></div>
                        <div class="signature-name">{{ $peminjaman->petugas->name ?? '________________' }}</div>
                        <div class="signature-role">Petugas Sarpras</div>
                    </td>
                    <td>
                        <div class="signature-box"></div>
                        <div class="signature-name">________________</div>
                        <div class="signature-role">Kepala Unit Sarpras</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Dokumen ini dicetak secara otomatis oleh Sistem Informasi Sarpras pada {{ now()->format('d F Y H:i') }} WIB</p>
            <p>© {{ date('Y') }} SMKN 1 Boyolangu - Unit Sarana dan Prasarana</p>
        </div>
    </div>
</body>
</html>
