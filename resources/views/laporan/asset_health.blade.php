<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Kesehatan Aset (Asset Health)') }}
            @if($selectedLokasi)
                <span class="text-sm font-normal text-gray-500">- {{ $selectedLokasi->nama_lokasi }}</span>
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Filter Lokasi --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form method="GET" action="{{ route('laporan.asset-health') }}" class="flex items-center space-x-4">
                    <label for="lokasi_id" class="text-sm font-medium text-gray-700">Filter Lokasi:</label>
                    <select name="lokasi_id" id="lokasi_id" onchange="this.form.submit()" 
                        class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg">
                        <option value="">Semua Lokasi</option>
                        @foreach($lokasiList as $lokasi)
                            <option value="{{ $lokasi->id }}" {{ request('lokasi_id') == $lokasi->id ? 'selected' : '' }}>
                                {{ $lokasi->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                    @if(request('lokasi_id'))
                        <a href="{{ route('laporan.asset-health') }}" class="text-sm text-blue-600 hover:underline">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Per-Location Summary (only when no filter) - COLLAPSIBLE --}}
            @if(!$selectedLokasi && count($lokasiSummary) > 0)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" x-data="{ expanded: false }">
                {{-- Header with Toggle --}}
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-medium text-gray-900">📊 Statistik Per Lokasi</h3>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ count($lokasiSummary) }} lokasi</span>
                    </div>
                    <button type="button" onclick="toggleLokasiSection()" id="toggleLokasiBtn"
                            class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium">
                        <span id="toggleLokasiText">Lihat Semua</span>
                        <svg id="toggleLokasiIcon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                
                {{-- Summary Row (Always Visible) --}}
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
                    <div class="bg-gray-100 rounded-lg p-3 text-center">
                        <p class="text-xl font-bold text-gray-800">{{ $lokasiSummary->sum('total_unit') }}</p>
                        <p class="text-xs text-gray-500">Total Unit</p>
                    </div>
                    <div class="bg-green-100 rounded-lg p-3 text-center">
                        <p class="text-xl font-bold text-green-700">{{ $lokasiSummary->sum('tersedia') }}</p>
                        <p class="text-xs text-green-600">Tersedia</p>
                    </div>
                    <div class="bg-blue-100 rounded-lg p-3 text-center">
                        <p class="text-xl font-bold text-blue-700">{{ $lokasiSummary->sum('dipinjam') }}</p>
                        <p class="text-xs text-blue-600">Dipinjam</p>
                    </div>
                    <div class="bg-red-100 rounded-lg p-3 text-center">
                        <p class="text-xl font-bold text-red-700">{{ $lokasiSummary->sum('rusak') }}</p>
                        <p class="text-xs text-red-600">Rusak</p>
                    </div>
                    <div class="bg-yellow-100 rounded-lg p-3 text-center">
                        <p class="text-xl font-bold text-yellow-700">{{ $lokasiSummary->sum('maintenance') }}</p>
                        <p class="text-xs text-yellow-600">Maintenance</p>
                    </div>
                </div>
                
                {{-- Visual Cards Grid (Collapsible) --}}
                <div id="lokasiCardsSection" class="hidden border-t pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto pr-2">
                        @foreach($lokasiSummary as $lok)
                            @php
                                $healthScore = $lok->total_unit > 0 
                                    ? round(($lok->tersedia / $lok->total_unit) * 100) 
                                    : 0;
                                $healthColor = $healthScore >= 70 ? 'green' : ($healthScore >= 40 ? 'yellow' : 'red');
                            @endphp
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-shadow">
                                {{-- Header --}}
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $lok->nama_lokasi }}</h4>
                                        <p class="text-xs text-gray-500">{{ $lok->total_unit }} unit total</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-{{ $healthColor }}-600">{{ $healthScore }}%</span>
                                        <p class="text-xs text-gray-500">Ketersediaan</p>
                                    </div>
                                </div>
                                
                                {{-- Progress Bar --}}
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                                    <div class="bg-{{ $healthColor }}-500 h-2 rounded-full transition-all" style="width: {{ $healthScore }}%"></div>
                                </div>
                                
                                {{-- Stats Grid --}}
                                <div class="grid grid-cols-4 gap-2 text-center text-xs">
                                    <div class="bg-green-50 rounded p-2">
                                        <p class="font-bold text-green-700">{{ $lok->tersedia }}</p>
                                        <p class="text-green-600">Tersedia</p>
                                    </div>
                                    <div class="bg-blue-50 rounded p-2">
                                        <p class="font-bold text-blue-700">{{ $lok->dipinjam }}</p>
                                        <p class="text-blue-600">Dipinjam</p>
                                    </div>
                                    <div class="bg-red-50 rounded p-2">
                                        <p class="font-bold text-red-700">{{ $lok->rusak }}</p>
                                        <p class="text-red-600">Rusak</p>
                                    </div>
                                    <div class="bg-yellow-50 rounded p-2">
                                        <p class="font-bold text-yellow-700">{{ $lok->maintenance }}</p>
                                        <p class="text-yellow-600">Maint</p>
                                    </div>
                                </div>
                                
                                {{-- Action --}}
                                <a href="{{ route('laporan.asset-health', ['lokasi_id' => $lok->id]) }}" 
                                   class="mt-3 block w-full text-center text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                    Lihat Detail →
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <script>
                function toggleLokasiSection() {
                    const section = document.getElementById('lokasiCardsSection');
                    const text = document.getElementById('toggleLokasiText');
                    const icon = document.getElementById('toggleLokasiIcon');
                    
                    if (section.classList.contains('hidden')) {
                        section.classList.remove('hidden');
                        text.textContent = 'Sembunyikan';
                        icon.classList.add('rotate-180');
                    } else {
                        section.classList.add('hidden');
                        text.textContent = 'Lihat Semua';
                        icon.classList.remove('rotate-180');
                    }
                }
            </script>
            @endif
            
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
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
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
                                    </td>
                                    <td class="px-4 py-2">
                                        @if($unit->status === 'maintenance')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Sedang Maintenance</span>
                                        @else
                                            <a href="{{ route('maintenance.create', ['unit_id' => $unit->id]) }}" 
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Maintenance
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada aset rusak saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
