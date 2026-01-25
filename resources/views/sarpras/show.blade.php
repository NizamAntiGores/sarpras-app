<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('sarpras.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Barang</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {{-- Left Column: Foto --}}
                        <div class="md:col-span-1">
                            <div class="sticky top-6">
                                <div class="w-full aspect-square bg-gray-50 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-200 overflow-hidden relative">
                                    @if ($sarpras->foto)
                                        <img src="{{ Storage::url($sarpras->foto) }}" alt="{{ $sarpras->nama_barang }}" 
                                             class="absolute inset-0 w-full h-full object-cover">
                                    @else
                                        <div class="text-center text-gray-400">
                                            <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-sm font-medium">Tidak ada foto</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        {{-- Right Column: Details & Stats --}}
                        <div class="md:col-span-2 space-y-6">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded text-xs font-mono font-bold tracking-wider">{{ $sarpras->kode_barang }}</span>
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $sarpras->kategori->nama_kategori ?? '-' }}</span>
                                </div>
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $sarpras->nama_barang }}</h1>
                                <div class="flex items-center text-gray-500 text-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $sarpras->lokasi->nama_lokasi ?? 'Lokasi belum diset' }}
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            {{-- Statistik Unit --}}
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Status Inventaris</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                                        <p class="text-xs text-blue-600 font-medium mb-1">Total Unit</p>
                                        <p class="text-2xl font-bold text-blue-700">{{ $statistics['total_unit'] }}</p>
                                    </div>
                                    <div class="bg-green-50 rounded-xl p-3 border border-green-100">
                                        <div class="flex items-center gap-1 mb-1">
                                            <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                            <p class="text-xs text-green-600 font-medium">Tersedia</p>
                                        </div>
                                        <p class="text-2xl font-bold text-green-700">{{ $statistics['tersedia'] }}</p>
                                    </div>
                                    <div class="bg-orange-50 rounded-xl p-3 border border-orange-100">
                                        <div class="flex items-center gap-1 mb-1">
                                            <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>
                                            <p class="text-xs text-orange-600 font-medium">Dipinjam</p>
                                        </div>
                                        <p class="text-2xl font-bold text-orange-700">{{ $statistics['dipinjam'] }}</p>
                                    </div>
                                    <div class="bg-red-50 rounded-xl p-3 border border-red-100">
                                        <div class="flex items-center gap-1 mb-1">
                                            <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>
                                            <p class="text-xs text-red-600 font-medium">Rusak/Maint.</p>
                                        </div>
                                        <p class="text-2xl font-bold text-red-700">{{ $statistics['rusak'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t">
                        <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                            <a href="{{ route('sarpras.units.index', $sarpras) }}" 
                               class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-semibold hover:bg-teal-700 transition shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                Kelola Unit Satuan
                            </a>
                            <a href="{{ route('sarpras.units.create', $sarpras) }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Unit
                            </a>
                            <a href="{{ route('sarpras.edit', $sarpras) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Data
                            </a>
                            <a href="{{ route('sarpras.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition shadow-sm">
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
