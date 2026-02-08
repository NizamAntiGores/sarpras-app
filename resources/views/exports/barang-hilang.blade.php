@extends('exports.layout')

@section('content')
    @php $title = 'Laporan Barang Hilang'; @endphp

    <div class="summary-box">
        <h3>Ringkasan</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="number">{{ $barangHilang->count() }}</div>
                <div class="label">Total</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $barangHilang->where('status', 'belum_diganti')->count() }}</div>
                <div class="label">Belum Diganti</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $barangHilang->where('status', 'sudah_diganti')->count() }}</div>
                <div class="label">Sudah Diganti</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $barangHilang->where('status', 'diputihkan')->count() }}</div>
                <div class="label">Diputihkan</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 80px;">Kode Unit</th>
                <th style="width: 150px;">Nama Barang</th>
                <th style="width: 120px;">Peminjam</th>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 70px;">Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barangHilang as $index => $item)
                @php
                    $unit = $item->pengembalianDetail?->sarprasUnit;
                    $sarpras = $unit?->sarpras;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="mono">{{ $unit->kode_unit ?? '-' }}</td>
                    <td>
                        {{ $sarpras->nama_barang ?? '-' }}
                        <br><small style="color:#666">{{ $sarpras->kode_barang ?? '' }}</small>
                    </td>
                    <td>
                        {{ $item->user->name ?? '-' }}
                        <br><small style="color:#666">{{ $item->user->email ?? '' }}</small>
                    </td>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge 
                                    @if($item->status == 'belum_diganti') badge-danger
                                    @elseif($item->status == 'sudah_diganti') badge-success
                                    @else badge-secondary
                                    @endif">
                            {{ str_replace('_', ' ', ucfirst($item->status)) }}
                        </span>
                    </td>
                    <td>{{ Str::limit($item->keterangan, 50) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection