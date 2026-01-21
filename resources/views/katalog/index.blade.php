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
                                class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategori as $kat)
                                <option value="{{ $kat->id }}" {{ $kategoriId == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if ($kategoriId)
                        <a href="{{ route('katalog.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            &times; Reset Filter
                        </a>
                    @endif
                </form>
            </div>

            {{-- Grid Cards --}}
            @if ($sarpras->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($sarpras as $item)
                        <div class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300">
                            {{-- Foto with Hover Overlay --}}
                            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                @if ($item->foto)
                                    <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama_barang }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                
                                {{-- Stok Badge --}}
                                <div class="absolute top-2 right-2 z-10">
                                    @if ($item->available_count > 5)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full text-white shadow-sm" style="background-color: #3b82f6;">
                                            Tersedia: {{ $item->available_count }}
                                        </span>
                                    @elseif ($item->available_count > 0)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full text-white shadow-sm" style="background-color: #fb923c;">
                                            Sisa: {{ $item->available_count }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full text-white shadow-sm" style="background-color: #ef4444;">
                                            Habis
                                        </span>
                                    @endif
                                </div>

                                {{-- Hover Overlay with Quick Action --}}
                                @if ($item->available_count > 0)
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-6">
                                        <a href="{{ route('peminjaman.create', ['sarpras_id' => $item->id, 'from' => 'katalog']) }}" 
                                           class="inline-flex items-center px-5 py-2.5 text-blue-600 rounded-full text-sm font-bold shadow-lg hover:bg-gray-50 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300"
                                           style="background-color: #ffffff;">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            Pinjam Sekarang
                                        </a>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Content --}}
                            <div class="p-4">
                                <span class="text-xs font-bold uppercase tracking-wider" style="color: #2563eb;">{{ $item->kategori->nama_kategori ?? '-' }}</span>
                                <h3 class="font-bold text-gray-900 mt-1 line-clamp-2 leading-tight h-10">{{ $item->nama_barang }}</h3>
                                <p class="text-xs text-gray-400 mt-2 font-mono">ID: {{ $item->kode_barang }}</p>
                                
                                {{-- Tombol Pinjam (Always Visible) --}}
                                <div class="mt-4">
                                    @if ($item->available_count > 0)
                                        <a href="{{ route('peminjaman.create', ['sarpras_id' => $item->id, 'from' => 'katalog']) }}" 
                                           class="block w-full text-center px-4 py-2.5 text-white rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg"
                                           style="background-color: #2563eb;">
                                            Pinjam Barang
                                        </a>
                                    @else
                                        <button disabled 
                                                class="block w-full text-center px-4 py-2.5 bg-gray-100 text-gray-400 rounded-xl text-sm font-bold cursor-not-allowed border border-gray-200">
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
                            <a href="{{ route('katalog.index') }}" class="text-blue-600 hover:underline">Lihat semua barang</a>
                        @else
                            Belum ada barang yang tersedia untuk dipinjam.
                        @endif
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
