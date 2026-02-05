<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Sarpras</h2>
            <a href="{{ route('sarpras.create') }}"
                class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Barang
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    {{-- Global Stats Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white shadow">
                            <p class="text-blue-100 text-sm">Jenis Barang</p>
                            <p class="text-2xl font-bold">{{ $stats['total_jenis'] }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white shadow">
                            <p class="text-green-100 text-sm">Total Unit Siap Pakai</p>
                            <p class="text-2xl font-bold">{{ $stats['total_tersedia'] }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white shadow">
                            <p class="text-orange-100 text-sm">Total Unit Dipinjam</p>
                            <p class="text-2xl font-bold">{{ $stats['total_dipinjam'] }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white shadow">
                            <p class="text-red-100 text-sm">Total Unit Maintenance</p>
                            <p class="text-2xl font-bold">{{ $stats['total_maintenance'] }}</p>
                        </div>
                    </div>

                    {{-- Search Bar --}}
                    <div class="mb-6">
                        <form action="{{ route('sarpras.index') }}" method="GET"
                            class="flex flex-col md:flex-row gap-4">
                            <div class="min-w-[180px]">
                                <select name="kategori_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm"
                                    onchange="this.form.submit()">
                                    <option value="">Semua Kategori</option>
                                    @foreach($kategoriList as $kat)
                                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                                            {{ $kat->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="min-w-[180px]">
                                <select name="lokasi_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm"
                                    onchange="this.form.submit()">
                                    <option value="">Semua Lokasi (View Barang)</option>
                                    @foreach($lokasiList as $lok)
                                        <option value="{{ $lok->id }}" {{ request('lokasi_id') == $lok->id ? 'selected' : '' }}>
                                            {{ $lok->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="min-w-[150px]">
                                <select name="tipe"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm"
                                    onchange="this.form.submit()">
                                    <option value="">Semua Tipe</option>
                                    <option value="aset" {{ request('tipe') == 'aset' ? 'selected' : '' }}>Aset</option>
                                    <option value="bahan" {{ request('tipe') == 'bahan' ? 'selected' : '' }}>Habis Pakai</option>
                                </select>
                            </div>
                            <div class="flex-grow relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Cari nama atau kode barang..." oninput="debounceSubmit()">
                            </div>
                            <script>
                                let timeout = null;
                                function debounceSubmit() {
                                    clearTimeout(timeout);
                                    timeout = setTimeout(function () {
                                        // Submit form
                                        const form = document.getElementById('search').closest('form');
                                        form.submit();
                                    }, 600); // Wait 600ms after user stops typing
                                }

                                // Auto-focus input after reload if searching
                                document.addEventListener("DOMContentLoaded", function () {
                                    const searchInput = document.getElementById('search');
                                    if (searchInput && searchInput.value) {
                                        searchInput.focus();
                                        // Move cursor to end
                                        const val = searchInput.value;
                                        searchInput.value = '';
                                        searchInput.value = val;
                                    }
                                });
                            </script>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cari
                            </button>
                            @if(request('search') || request('kategori_id') || request('tipe'))
                                <a href="{{ route('sarpras.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150">
                                    Reset
                                </a>
                            @endif
                            <a href="{{ route('export.sarpras', request()->query()) }}"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition ease-in-out duration-150 gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                PDF
                            </a>
                        </form>
                    </div>

                    {{-- Table --}}
                    {{-- Table Content Switching --}}
                    <div class="overflow-x-auto">
                        @if(isset($units))
                            {{-- UNIT VIEW (When Location is Selected) --}}
                            <div class="mb-2 p-2 bg-blue-50 text-blue-700 text-sm rounded border border-blue-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Menampilkan data <strong>&nbsp;Satuan Unit&nbsp;</strong> pada lokasi terpilih.
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Unit</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($units as $unit)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <span class="font-mono text-sm font-medium text-gray-900">{{ $unit->kode_unit }}</span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-sm text-gray-900">{{ $unit->sarpras->nama_barang }}</div>
                                                <div class="text-xs text-gray-500">{{ $unit->sarpras->kode_barang }}</div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                    {{ $unit->sarpras->kategori->nama_kategori ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                @if($unit->kondisi == 'baik')
                                                    <span class="px-2 py-1 text-xs text-green-800 bg-green-100 rounded-full">Baik</span>
                                                @elseif($unit->kondisi == 'rusak_ringan')
                                                    <span class="px-2 py-1 text-xs text-yellow-800 bg-yellow-100 rounded-full">Rusak Ringan</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs text-red-800 bg-red-100 rounded-full">Rusak Berat</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                @if($unit->status == 'tersedia')
                                                    <span class="px-2 py-1 text-xs text-green-800 bg-green-100 rounded-full">Tersedia</span>
                                                @elseif($unit->status == 'dipinjam')
                                                    <span class="px-2 py-1 text-xs text-blue-800 bg-blue-100 rounded-full">Dipinjam</span>
                                                @elseif($unit->status == 'maintenance')
                                                    <span class="px-2 py-1 text-xs text-orange-800 bg-orange-100 rounded-full">Maintenance</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs text-gray-800 bg-gray-100 rounded-full">{{ ucfirst($unit->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <a href="{{ route('sarpras.show', $unit->sarpras_id) }}" class="text-blue-600 hover:text-blue-900 text-sm">Detail Barang</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                                Tidak ada unit ditemukan di lokasi ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                             @if ($units->hasPages())
                                <div class="mt-6 border-t pt-4">{{ $units->links() }}</div>
                            @endif
                        @else
                            {{-- DEFAULT ITEM VIEW --}}
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                        Barang</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total
                                        Unit</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($sarpras as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            @if ($item->foto)
                                                <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama_barang }}"
                                                    class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                                            @else
                                                <div
                                                    class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4"><span
                                                class="px-2 py-1 text-xs font-mono bg-gray-100 rounded">{{ $item->kode_barang }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->nama_barang }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-col items-start gap-1">
                                                <span
                                                    class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $item->kategori->nama_kategori ?? '-' }}</span>
                                                @if($item->tipe == 'bahan')
                                                    <span
                                                        class="px-2 py-0.5 text-[10px] rounded border border-amber-200 bg-amber-50 text-amber-700">Habis
                                                        Pakai</span>
                                                @else
                                                    <span
                                                        class="px-2 py-0.5 text-[10px] rounded border border-gray-200 bg-gray-50 text-gray-600">Aset</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            {{-- Ringkasan Unit --}}
                                            <div class="w-32 mx-auto">
                                                <div class="flex justify-between items-end mb-1">
                                                    <span class="text-xs font-medium text-gray-500">Total</span>
                                                    <span
                                                        class="text-lg font-bold text-gray-800">{{ $item->total_unit ?? 0 }}</span>
                                                </div>

                                                {{-- Progress Bar --}}
                                                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden flex mb-2">
                                                    @php
                                                        $total = $item->total_unit > 0 ? $item->total_unit : 1;
                                                        $tersediaPct = (($item->stok_tersedia ?? 0) / $total) * 100;
                                                        $dipinjamPct = (($item->dipinjam_count ?? 0) / $total) * 100;
                                                        $rusakPct = (($item->maintenance_count ?? 0) + ($item->rusak_berat_count ?? 0)) / $total * 100;
                                                    @endphp
                                                    <div class="bg-green-500" style="width: {{ $tersediaPct }}%"
                                                        title="Tersedia: {{ $item->stok_tersedia ?? 0 }}"></div>
                                                    <div class="bg-yellow-400" style="width: {{ $dipinjamPct }}%"
                                                        title="Dipinjam: {{ $item->dipinjam_count ?? 0 }}"></div>
                                                    <div class="bg-red-500" style="width: {{ $rusakPct }}%"
                                                        title="Tidak Tersedia: {{ ($item->maintenance_count ?? 0) + ($item->rusak_berat_count ?? 0) }}">
                                                    </div>
                                                </div>

                                                {{-- Legend / Mini Stats --}}
                                                <div class="flex justify-between text-[10px] font-medium px-0.5">
                                                    <div class="flex items-center gap-1" title="Tersedia">
                                                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                        <span class="text-green-700">{{ $item->stok_tersedia ?? 0 }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1" title="Dipinjam">
                                                        <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                                                        <span
                                                            class="text-yellow-700">{{ $item->dipinjam_count ?? 0 }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1" title="Rusak/Maintenance">
                                                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                                        <span
                                                            class="text-red-700">{{ ($item->maintenance_count ?? 0) + ($item->rusak_berat_count ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="flex items-center justify-center space-x-3">
                                                    {{-- Shortcut: Lihat Unit (Utama) --}}
                                                    <a href="{{ route('sarpras.units.index', $item) }}"
                                                        class="text-teal-600 hover:text-teal-900 bg-teal-50 hover:bg-teal-100 p-1.5 rounded-lg transition"
                                                        title="Lihat Daftar Unit">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                                        </svg>
                                                    </a>

                                                    {{-- Shortcut: Tambah Unit --}}
                                                    <a href="{{ route('sarpras.units.create', $item) }}"
                                                        class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 p-1.5 rounded-lg transition"
                                                        title="Tambah Unit Baru">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                    </a>

                                                    {{-- Detail Sarpras (General Info) --}}
                                                    <a href="{{ route('sarpras.show', $item) }}"
                                                        class="text-gray-500 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 p-1.5 rounded-lg transition"
                                                        title="Detail Informasi Barang">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </a>

                                                    <a href="{{ route('sarpras.edit', $item) }}"
                                                        class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-lg transition"
                                                        title="Edit Data Barang">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>

                                                    @if (auth()->user()->role === 'admin')
                                                        <form action="{{ route('sarpras.destroy', $item) }}" method="POST"
                                                            class="inline"
                                                            onsubmit="return confirm('Yakin hapus barang ini?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-lg transition"
                                                                title="Hapus Barang">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada data sarpras
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if ($sarpras->hasPages())
                            <div class="mt-6 border-t pt-4">{{ $sarpras->links() }}</div>
                        @endif
                        @endif
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>