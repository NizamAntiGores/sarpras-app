<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Kesehatan Aset (Asset Health)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Section 1: Ringkasan Kondisi --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Kondisi Unit</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @php
                        $mapKondisi = [
                            'baik' => ['color' => 'green', 'label' => 'Baik'],
                            'rusak_ringan' => ['color' => 'yellow', 'label' => 'Rusak Ringan'],
                            'rusak_berat' => ['color' => 'red', 'label' => 'Rusak Berat'],
                            'hilang' => ['color' => 'gray', 'label' => 'Hilang'],
                        ];
                    @endphp
                    
                    @foreach ($mapKondisi as $key => $data)
                        @php
                            $count = $kondisiSummary->where('kondisi', $key)->first()?->total ?? 0;
                            $bgClass = "bg-{$data['color']}-100";
                            $textClass = "text-{$data['color']}-800";
                            $borderClass = "border-{$data['color']}-200";
                        @endphp
                        <div class="p-4 rounded-lg border {{ $borderClass }} {{ $bgClass }}">
                            <p class="text-sm font-medium {{ $textClass }}">{{ $data['label'] }}</p>
                            <p class="text-3xl font-bold {{ $textClass }}">{{ $count }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Section 2: Barang Rusak / Butuh Perhatian --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Daftar Aset Rusak & Perlu Maintenance
                    </h3>
                    <div class="overflow-y-auto max-h-96">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($asetRusak as $unit)
                                    <tr>
                                        <td class="px-4 py-2">
                                            <div class="text-sm font-medium text-gray-900">{{ $unit->sarpras->nama_barang }}</div>
                                            <div class="text-xs text-gray-500">{{ $unit->lokasi->nama_lokasi ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $unit->kode_unit }}</td>
                                        <td class="px-4 py-2">
                                            @if($unit->kondisi === 'rusak_berat')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rusak Berat</span>
                                            @elseif($unit->kondisi === 'rusak_ringan')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Rusak Ringan</span>
                                            @endif
                                            @if($unit->status === 'maintenance')
                                                <div class="text-xs text-blue-600 mt-1">Sedang Maintenance</div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada aset rusak saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Section 3: Analisis Kerusakan --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        Top 5 Aset Paling Sering Rusak
                    </h3>
                    <div class="space-y-4">
                        @forelse ($seringRusak as $index => $item)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <span class="bg-orange-100 text-orange-800 text-xs font-bold px-2 py-1 rounded-full mr-3">#{{ $index + 1 }}</span>
                                <span class="text-sm font-medium text-gray-700">{{ $item->nama_barang }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-bold text-gray-900 mr-1">{{ $item->total_kerusakan }}</span>
                                <span class="text-xs text-gray-500">x rusak</span>
                            </div>
                        </div>
                        @empty
                            <p class="text-center text-gray-500 py-4">Belum ada data historis kerusakan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Section 4: Aset Hilang --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 text-red-600">Laporan Kehilangan Aset</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Barang / Unit</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Penanggung Jawab</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($asetHilang as $hilang)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $hilang->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $hilang->sarprasUnit->sarpras->nama_barang ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">{{ $hilang->sarprasUnit->kode_unit ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $hilang->user->name ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ Str::limit($hilang->keterangan, 50) }}</td>
                                    <td class="px-4 py-2 w-32">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ str_replace('_', ' ', $hilang->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada laporan kehilangan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Section 5: Riwayat Maintenance Terakhir --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat Maintenance Terakhir</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Biaya</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($riwayatMaintenance as $m)
                                <tr>
                                    <td class="px-4 py-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $m->sarprasUnit->sarpras->nama_barang }}</div>
                                        <div class="text-xs text-gray-500">{{ $m->sarprasUnit->kode_unit }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $m->jenis) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $m->tanggal_selesai ? $m->tanggal_selesai->format('d M Y') : '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">Rp {{ number_format($m->biaya, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2">
                                        @if($m->status === 'selesai')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Proses</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">Belum ada riwayat maintenance.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
