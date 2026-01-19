<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Katalog Barang</h2>
            <div class="mt-2 sm:mt-0 text-sm text-gray-500">
                {{ $totalBarang }} barang tersedia dari {{ $totalKategori }} kategori
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Filter Section --}}
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <form method="GET" action="{{ route('katalog.index') }}" class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2">
                        <label for="kategori" class="text-sm font-medium text-gray-700">Filter Kategori:</label>
                        <select name="kategori" id="kategori" onchange="this.form.submit()"
                                class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategori as $kat)
                                <option value="{{ $kat->id }}" {{ $kategoriId == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if ($kategoriId)
                        <a href="{{ route('katalog.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            &times; Reset Filter
                        </a>
                    @endif
                </form>
            </div>

            {{-- Grid Cards --}}
            @if ($sarpras->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($sarpras as $item)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            {{-- Foto --}}
                            <div class="aspect-square bg-gray-100 relative">
                                @if ($item->foto)
                                    <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama_barang }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                
                                {{-- Stok Badge --}}
                                <div class="absolute top-2 right-2">
                                    @if ($item->stok > 5)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-500 text-white">
                                            Stok: {{ $item->stok }}
                                        </span>
                                    @elseif ($item->stok > 0)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-500 text-white">
                                            Stok: {{ $item->stok }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-500 text-white">
                                            Habis
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- Content --}}
                            <div class="p-4">
                                <span class="text-xs text-indigo-600 font-medium">{{ $item->kategori->nama_kategori ?? '-' }}</span>
                                <h3 class="font-semibold text-gray-900 mt-1 line-clamp-2">{{ $item->nama_barang }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $item->lokasi->nama_lokasi ?? '-' }}</p>
                                <p class="text-xs text-gray-400 mt-1">Kode: {{ $item->kode_barang }}</p>
                                
                                {{-- Tombol Pinjam --}}
                                <div class="mt-4">
                                    @if ($item->stok > 0)
                                        <a href="{{ route('peminjaman.create', ['sarpras_id' => $item->id]) }}" 
                                           class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                            Pinjam Barang
                                        </a>
                                    @else
                                        <button disabled 
                                                class="block w-full text-center px-4 py-2 bg-gray-300 text-gray-500 rounded-lg text-sm font-medium cursor-not-allowed">
                                            Stok Habis
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak Ada Barang</h3>
                    <p class="mt-2 text-gray-500">
                        @if ($kategoriId)
                            Tidak ada barang tersedia untuk kategori ini.
                            <a href="{{ route('katalog.index') }}" class="text-indigo-600 hover:underline">Lihat semua barang</a>
                        @else
                            Belum ada barang yang tersedia untuk dipinjam.
                        @endif
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
