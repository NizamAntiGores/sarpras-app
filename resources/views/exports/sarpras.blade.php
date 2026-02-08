@extends('exports.layout')

@section('content')
    @php $title = 'Inventaris Sarpras'; @endphp

    <div class="summary-box">
        <h3>Ringkasan Inventaris</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="number">{{ $sarpras->count() }}</div>
                <div class="label">Jenis Barang</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $sarpras->sum('total_unit') }}</div>
                <div class="label">Total Unit</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $sarpras->sum('stok_tersedia') }}</div>
                <div class="label">Tersedia</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $sarpras->sum('dipinjam_count') }}</div>
                <div class="label">Dipinjam</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $sarpras->sum('maintenance_count') }}</div>
                <div class="label">Maintenance</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 80px;">Kode</th>
                <th style="width: 180px;">Nama Barang</th>
                <th style="width: 100px;">Kategori</th>
                <th style="width: 50px;">Tipe</th>
                <th style="width: 50px;" class="text-center">Total</th>
                <th style="width: 50px;" class="text-center">Tersedia</th>
                <th style="width: 50px;" class="text-center">Dipinjam</th>
                <th style="width: 60px;" class="text-center">Maintenance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sarpras as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="mono">{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $item->tipe == 'bahan' ? 'badge-warning' : 'badge-secondary' }}">
                            {{ $item->tipe == 'bahan' ? 'Habis Pakai' : 'Aset' }}
                        </span>
                    </td>
                    <td class="text-center"><strong>{{ $item->total_unit ?? 0 }}</strong></td>
                    <td class="text-center" style="color:#22543d">{{ $item->stok_tersedia ?? 0 }}</td>
                    <td class="text-center" style="color:#744210">{{ $item->dipinjam_count ?? 0 }}</td>
                    <td class="text-center" style="color:#742a2a">{{ $item->maintenance_count ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background:#e2e8f0; font-weight:bold;">
                <td colspan="5" class="text-right">TOTAL:</td>
                <td class="text-center">{{ $sarpras->sum('total_unit') }}</td>
                <td class="text-center">{{ $sarpras->sum('stok_tersedia') }}</td>
                <td class="text-center">{{ $sarpras->sum('dipinjam_count') }}</td>
                <td class="text-center">{{ $sarpras->sum('maintenance_count') }}</td>
            </tr>
        </tfoot>
    </table>
@endsection