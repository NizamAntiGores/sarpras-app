@extends('exports.layout')

@section('content')
    @php $title = 'Laporan Maintenance'; @endphp

    <div class="summary-box">
        <h3>Ringkasan</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="number">{{ $maintenanceData->count() }}</div>
                <div class="label">Total</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $maintenanceData->where('status', 'pending')->count() }}</div>
                <div class="label">Pending</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $maintenanceData->where('status', 'in_progress')->count() }}</div>
                <div class="label">In Progress</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $maintenanceData->where('status', 'completed')->count() }}</div>
                <div class="label">Completed</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 80px;">Kode Unit</th>
                <th style="width: 120px;">Nama Barang</th>
                <th style="width: 70px;">Jenis</th>
                <th style="width: 70px;">Status</th>
                <th style="width: 80px;">Tgl Lapor</th>
                <th style="width: 100px;">Pelapor</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenanceData as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="mono">{{ $item->sarprasUnit->kode_unit ?? '-' }}</td>
                    <td>{{ $item->sarprasUnit->sarpras->nama_barang ?? '-' }}</td>
                    <td>
                        <span class="badge 
                                            @if($item->jenis == 'perbaikan') badge-warning
                                            @elseif($item->jenis == 'perawatan') badge-info
                                            @else badge-secondary
                                            @endif">
                            {{ ucfirst($item->jenis) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge 
                                            @if($item->status == 'pending') badge-warning
                                            @elseif($item->status == 'in_progress') badge-info
                                            @elseif($item->status == 'completed') badge-success
                                            @elseif($item->status == 'cancelled') badge-danger
                                            @else badge-secondary
                                            @endif">
                            {{ str_replace('_', ' ', ucfirst($item->status)) }}
                        </span>
                    </td>
                    <td>{{ $item->tanggal_mulai->format('d/m/Y') }}</td>
                    <td>{{ $item->petugas->name ?? '-' }}</td>
                    <td>{{ Str::limit($item->deskripsi, 50) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection