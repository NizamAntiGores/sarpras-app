@extends('exports.layout')

@section('content')
    @php $title = 'Laporan Kesehatan Aset'; @endphp

    {{-- Filter Info --}}
    @if($selectedLokasi || $selectedKategori)
        <div class="info-box">
            <p><strong>Filter Aktif:</strong></p>
            @if($selectedLokasi)
                <p>• Lokasi: {{ $selectedLokasi->nama_lokasi }}</p>
            @endif
            @if($selectedKategori)
                <p>• Kategori: {{ $selectedKategori->nama_kategori }}</p>
            @endif
        </div>
    @endif

    {{-- Ringkasan Kondisi --}}
    <div class="summary-box">
        <h3>Ringkasan Kondisi Aset</h3>
        <div class="summary-grid">
            @php
                $baik = $kondisiSummary->where('kondisi', 'baik')->first();
                $rusakRingan = $kondisiSummary->where('kondisi', 'rusak_ringan')->first();
                $rusakBerat = $kondisiSummary->where('kondisi', 'rusak_berat')->first();
                $total = $kondisiSummary->sum('total');
            @endphp
            <div class="summary-item">
                <div class="number">{{ $total }}</div>
                <div class="label">Total Unit</div>
            </div>
            <div class="summary-item">
                <div class="number" style="color:#22543d">{{ $baik->total ?? 0 }}</div>
                <div class="label">Baik</div>
            </div>
            <div class="summary-item">
                <div class="number" style="color:#744210">{{ $rusakRingan->total ?? 0 }}</div>
                <div class="label">Rusak Ringan</div>
            </div>
            <div class="summary-item">
                <div class="number" style="color:#742a2a">{{ $rusakBerat->total ?? 0 }}</div>
                <div class="label">Rusak Berat</div>
            </div>
        </div>
    </div>

    {{-- Daftar Aset Rusak / Maintenance --}}
    @if($asetRusak->count() > 0)
        <h3 style="font-size:12px; margin: 15px 0 10px 0; color:#2d3748;">Daftar Aset Rusak / Maintenance
            ({{ $asetRusak->count() }} unit)</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th style="width: 80px;">Kode Unit</th>
                    <th style="width: 150px;">Nama Barang</th>
                    <th style="width: 100px;">Lokasi</th>
                    <th style="width: 70px;">Kondisi</th>
                    <th style="width: 70px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asetRusak as $index => $unit)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="mono">{{ $unit->kode_unit }}</td>
                        <td>{{ $unit->sarpras->nama_barang ?? '-' }}</td>
                        <td>{{ $unit->lokasi->nama_lokasi ?? '-' }}</td>
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
                        <td>
                            <span class="badge 
                                        @if($unit->status == 'tersedia') badge-success
                                        @elseif($unit->status == 'dipinjam') badge-warning
                                        @elseif($unit->status == 'maintenance') badge-info
                                        @else badge-secondary
                                        @endif">
                                {{ ucfirst($unit->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Daftar Semua Unit (jika filter aktif) --}}
    @if($daftarUnit && $daftarUnit->count() > 0)
        <div class="page-break"></div>
        <h3 style="font-size:12px; margin: 15px 0 10px 0; color:#2d3748;">Daftar Detail Unit ({{ $daftarUnit->count() }} unit)
        </h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th style="width: 80px;">Kode Unit</th>
                    <th style="width: 150px;">Nama Barang</th>
                    <th style="width: 80px;">Kategori</th>
                    <th style="width: 100px;">Lokasi</th>
                    <th style="width: 70px;">Kondisi</th>
                    <th style="width: 70px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($daftarUnit as $index => $unit)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="mono">{{ $unit->kode_unit }}</td>
                        <td>{{ $unit->sarpras->nama_barang ?? '-' }}</td>
                        <td>{{ $unit->sarpras->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ $unit->lokasi->nama_lokasi ?? '-' }}</td>
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
                        <td>
                            <span class="badge 
                                        @if($unit->status == 'tersedia') badge-success
                                        @elseif($unit->status == 'dipinjam') badge-warning
                                        @elseif($unit->status == 'maintenance') badge-info
                                        @else badge-secondary
                                        @endif">
                                {{ ucfirst($unit->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Aset Hilang --}}
    @if($asetHilang->count() > 0)
        <h3 style="font-size:12px; margin: 15px 0 10px 0; color:#742a2a;">Aset Hilang ({{ $asetHilang->count() }} unit)</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th style="width: 80px;">Kode Unit</th>
                    <th style="width: 150px;">Nama Barang</th>
                    <th style="width: 100px;">Peminjam</th>
                    <th style="width: 70px;">Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asetHilang as $index => $item)
                    @php
                        $unit = $item->pengembalianDetail?->sarprasUnit;
                        $sarpras = $unit?->sarpras;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="mono">{{ $unit->kode_unit ?? '-' }}</td>
                        <td>{{ $sarpras->nama_barang ?? '-' }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>
                            <span class="badge 
                                        @if($item->status == 'belum_diganti') badge-danger
                                        @elseif($item->status == 'sudah_diganti') badge-success
                                        @else badge-secondary
                                        @endif">
                                {{ str_replace('_', ' ', ucfirst($item->status)) }}
                            </span>
                        </td>
                        <td>{{ Str::limit($item->keterangan, 40) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Riwayat Maintenance --}}
    @if($riwayatMaintenance->count() > 0)
        <h3 style="font-size:12px; margin: 15px 0 10px 0; color:#2d3748;">Riwayat Maintenance Terakhir</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th style="width: 80px;">Kode Unit</th>
                    <th style="width: 150px;">Nama Barang</th>
                    <th style="width: 80px;">Jenis</th>
                    <th style="width: 80px;">Tanggal</th>
                    <th style="width: 70px;">Status</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayatMaintenance as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="mono">{{ $item->sarprasUnit->kode_unit ?? '-' }}</td>
                        <td>{{ $item->sarprasUnit->sarpras->nama_barang ?? '-' }}</td>
                        <td>{{ str_replace('_', ' ', ucfirst($item->jenis)) }}</td>
                        <td>{{ $item->tanggal_mulai?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            <span class="badge 
                                        @if($item->status == 'sedang_berlangsung') badge-warning
                                        @elseif($item->status == 'selesai') badge-success
                                        @elseif($item->status == 'dibatalkan') badge-danger
                                        @else badge-secondary
                                        @endif">
                                {{ str_replace('_', ' ', ucfirst($item->status)) }}
                            </span>
                        </td>
                        <td>{{ $item->petugas->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection