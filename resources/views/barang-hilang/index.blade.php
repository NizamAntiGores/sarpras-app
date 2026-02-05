<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Barang Hilang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Statistik Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
                    <p class="text-red-100 text-sm">Total Hilang</p>
                    <p class="text-3xl font-bold">{{ $totalHilang }} unit</p>
                </div>
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-4 text-white">
                    <p class="text-yellow-100 text-sm">Belum Diganti</p>
                    <p class="text-3xl font-bold">{{ $belumDiganti }} unit</p>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                    <p class="text-green-100 text-sm">Sudah Diganti</p>
                    <p class="text-3xl font-bold">{{ $sudahDiganti }} unit</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    {{-- Filter --}}
                    <div class="mb-6 flex gap-4 items-center justify-between">
                        <form method="GET" class="flex gap-2">
                            <select name="status" class="rounded-lg border-gray-300 text-sm" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="belum_diganti" {{ request('status') === 'belum_diganti' ? 'selected' : '' }}>Belum Diganti</option>
                                <option value="sudah_diganti" {{ request('status') === 'sudah_diganti' ? 'selected' : '' }}>Sudah Diganti</option>
                                <option value="diputihkan" {{ request('status') === 'diputihkan' ? 'selected' : '' }}>Diputihkan</option>
                            </select>
                        </form>
                        <a href="{{ route('export.barang-hilang', request()->query()) }}" 
                           class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export PDF
                        </a>
                    </div>

                    @if ($barangHilang->isEmpty())
                        <div class="text-center py-12 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-2">Tidak ada data barang hilang</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Unit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($barangHilang as $item)
                                        @php
                                            $unit = $item->pengembalianDetail?->sarprasUnit;
                                            $sarpras = $unit?->sarpras;
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 text-sm font-mono bg-red-100 text-red-800 rounded font-semibold">
                                                    {{ $unit->kode_unit ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $sarpras->nama_barang ?? '-' }}</div>
                                                <div class="text-sm text-gray-500">{{ $sarpras->kode_barang ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->user->name ?? '-' }}</div>
                                                <div class="text-sm text-gray-500">{{ $item->user->email ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $item->created_at->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                                {{ $item->keterangan ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @switch($item->status)
                                                    @case('belum_diganti')
                                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Belum Diganti</span>
                                                        @break
                                                    @case('sudah_diganti')
                                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sudah Diganti</span>
                                                        @break
                                                    @case('diputihkan')
                                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Diputihkan</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if ($item->status === 'belum_diganti')
                                                    <div class="flex gap-1 justify-center">
                                                        <form action="{{ route('barang-hilang.update', $item) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="sudah_diganti">
                                                            <button type="submit" class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-md hover:bg-green-200" onclick="return confirm('Tandai sebagai sudah diganti?')">
                                                                ✓ Diganti
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('barang-hilang.update', $item) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="diputihkan">
                                                            <button type="submit" class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-md hover:bg-gray-200" onclick="return confirm('Putihkan barang ini?')">
                                                                Putihkan
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <form action="{{ route('barang-hilang.update', $item) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="belum_diganti">
                                                        <button type="submit" class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-md hover:bg-gray-200" onclick="return confirm('Batalkan status?')">
                                                            ↩ Batalkan
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($barangHilang->hasPages())
                            <div class="mt-6 border-t pt-4">{{ $barangHilang->links() }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
