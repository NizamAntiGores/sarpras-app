<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengaduan</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; }
        .meta { margin-bottom: 15px; }
        .meta table { width: 100%; border: none; }
        .meta td { padding: 2px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data th, table.data td { border: 1px solid #999; padding: 6px; text-align: left; vertical-align: top; }
        table.data th { bg-color: #f2f2f2; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 9px; color: #666; border-top: 1px solid #ccc; padding-top: 5px; }
        .status-badge { padding: 2px 5px; border-radius: 3px; font-weight: bold; color: white; display: inline-block; }
        .status-belum_ditindaklanjuti { color: #d32f2f; }
        .status-sedang_diproses { color: #f57c00; }
        .status-selesai { color: #388e3c; }
        .status-ditutup { color: #616161; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pengaduan & Kerusakan</h1>
        <p>Sistem Informasi Sarana dan Prasarana</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td width="15%"><strong>Tanggal Cetak</strong></td>
                <td>: {{ $generatedAt }}</td>
            </tr>
            @if(isset($filters['status']))
            <tr>
                <td><strong>Filter Status</strong></td>
                <td>: {{ ucwords(str_replace('_', ' ', $filters['status'])) }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="15%">Pelapor</th>
                <th width="15%">Objek</th>
                <th width="20%">Judul & Deskripsi</th>
                <th width="10%">Status</th>
                <th width="15%">Penyelesaian/Catatan</th>
                <th width="8%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengaduan as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $item->user->name ?? 'User Dihapus' }}</td>
                <td>
                    @if($item->jenis == 'tempat')
                        <strong>Tempat:</strong><br>
                        {{ $item->lokasi->nama_lokasi ?? $item->lokasi_lainnya ?? '-' }}
                    @else
                        <strong>Barang:</strong><br>
                        {{ $item->sarpras->nama_barang ?? $item->barang_lainnya ?? '-' }}
                    @endif
                </td>
                <td>
                    <strong>{{ $item->judul }}</strong><br>
                    <span style="color: #444;">{{ Str::limit($item->deskripsi, 100) }}</span>
                </td>
                <td>
                    <span class="status-{{ $item->status }}">
                        {{ ucwords(str_replace('_', ' ', $item->status)) }}
                    </span>
                </td>
                <td>{{ $item->catatan_petugas ?? '-' }}</td>
                <td>{{ $item->petugas->name ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px;">Tidak ada data pengaduan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name }} | Halaman <script type="text/php">if (isset($pdf)) { echo $pdf->get_page_number(); }</script>
    </div>
</body>
</html>
