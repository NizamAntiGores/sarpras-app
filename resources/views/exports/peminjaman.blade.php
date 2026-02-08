@extends('exports.layout')

@section('content')
    @php $title = 'Daftar Peminjaman'; @endphp

    <div class="summary-box">
        <h3>Ringkasan</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="number">{{ $peminjaman->count() }}</div>
                <div class="label">Total</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $peminjaman->where('status', 'menunggu')->count() }}</div>
                <div class="label">Menunggu</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $peminjaman->where('status', 'disetujui')->count() }}</div>
                <div class="label">Disetujui</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $peminjaman->where('status', 'selesai')->count() }}</div>
                <div class="label">Selesai</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $peminjaman->where('status', 'ditolak')->count() }}</div>
                <div class="label">Ditolak</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 50px;">ID</th>
                <th style="width: 120px;">Peminjam</th>
                <th>Barang</th>
                <th style="width: 80px;">Tgl Pinjam</th>
                <th style="width: 80px;">Tgl Kembali</th>
                <th style="width: 70px;">Status</th>
                <th style="width: 100px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman as $index => $pinjam)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="mono">#{{ $pinjam->id }}</td>
                    <td>
                        {{ $pinjam->user->name ?? '-' }}
                        <br><small style="color:#666">{{ $pinjam->user->role ?? '' }}</small>
                    </td>
                    <td>
                        @if($pinjam->details && $pinjam->details->count() > 0)
                            {{ $pinjam->details->count() }} unit:
                            @foreach($pinjam->details->take(3) as $detail)
                                <span class="mono"
                                    style="font-size:8px">{{ $detail->sarprasUnit->kode_unit ?? '-' }}</span>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                            @if($pinjam->details->count() > 3)
                                <small>+{{ $pinjam->details->count() - 3 }} lagi</small>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $pinjam->tgl_pinjam?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $pinjam->tgl_kembali_rencana?->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        <span class="badge 
                                    @if($pinjam->status == 'menunggu') badge-warning
                                    @elseif($pinjam->status == 'disetujui') badge-success
                                    @elseif($pinjam->status == 'selesai') badge-info
                                    @elseif($pinjam->status == 'ditolak') badge-danger
                                    @else badge-secondary
                                    @endif">
                            {{ ucfirst($pinjam->status) }}
                        </span>
                    </td>
                    <td>{{ Str::limit($pinjam->keterangan, 40) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection