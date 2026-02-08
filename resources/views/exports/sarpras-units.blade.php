@extends('exports.layout')

@section('content')
    @php $title = 'Daftar Unit: ' . $sarpras->nama_barang; @endphp

    <div class="info-box">
        <p><strong>Kode Barang:</strong> {{ $sarpras->kode_barang }}</p>
        <p><strong>Nama Barang:</strong> {{ $sarpras->nama_barang }}</p>
        <p><strong>Kategori:</strong> {{ $sarpras->kategori->nama_kategori ?? '-' }}</p>
    </div>

    <div class="summary-box">
        <h3>Ringkasan Unit</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="number">{{ $units->count() }}</div>
                <div class="label">Total Unit</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $units->where('status', 'tersedia')->count() }}</div>
                <div class="label">Tersedia</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $units->where('status', 'dipinjam')->count() }}</div>
                <div class="label">Dipinjam</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $units->where('status', 'maintenance')->count() }}</div>
                <div class="label">Maintenance</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $units->where('kondisi', 'baik')->count() }}</div>
                <div class="label">Kondisi Baik</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 100px;">Kode Unit</th>
                <th style="width: 150px;">Lokasi</th>
                <th style="width: 80px;">Status</th>
                <th style="width: 80px;">Kondisi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($units as $index => $unit)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="mono">{{ $unit->kode_unit }}</td>
                    <td>{{ $unit->lokasi->nama_lokasi ?? '-' }}</td>
                    <td>
                        <span class="badge 
                                    @if($unit->status == 'tersedia') badge-success
                                    @elseif($unit->status == 'dipinjam') badge-warning
                                    @elseif($unit->status == 'maintenance') badge-info
                                    @elseif($unit->status == 'dihapusbukukan') badge-danger
                                    @else badge-secondary
                                    @endif">
                            {{ ucfirst($unit->status) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge 
                                    @if($unit->kondisi == 'baik') badge-success
                                    @elseif($unit->kondisi == 'rusak_ringan') badge-warning
                                    @elseif($unit->kondisi == 'rusak_berat') badge-danger
                                    @else badge-secondary
                                    @endif">
                            {{ str_replace('_', ' ', ucfirst($unit->kondisi)) }}
                        </span>
                    </td>
                    <td>{{ $unit->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection