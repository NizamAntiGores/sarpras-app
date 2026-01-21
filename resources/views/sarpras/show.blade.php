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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Foto Barang --}}
                        <div class="md:col-span-2 mb-4">
                            <div class="flex justify-center">
                                @if ($sarpras->foto)
                                    <img src="{{ Storage::url($sarpras->foto) }}" alt="{{ $sarpras->nama_barang }}" 
                                         class="max-h-64 rounded-xl shadow-lg border border-gray-200">
                                @else
                                    <div class="w-64 h-48 bg-gray-100 rounded-xl flex items-center justify-center border border-gray-200">
                                        <div class="text-center text-gray-400">
                                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="mt-2 text-sm">Tidak ada foto</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Kode Barang</label>
                                <p class="text-lg font-mono bg-gray-50 px-3 py-2 rounded">{{ $sarpras->kode_barang }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Nama Barang</label>
                                <p class="text-lg font-semibold">{{ $sarpras->nama_barang }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Kategori</label>
                                <p><span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">{{ $sarpras->kategori->nama_kategori ?? '-' }}</span></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Lokasi</label>
                                <p>{{ $sarpras->lokasi->nama_lokasi ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            {{-- Stok Cards (Unit-based) --}}
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                                    <p class="text-xs text-green-600 font-medium">Tersedia</p>
                                    <p class="text-2xl font-bold text-green-700">{{ $statistics['tersedia'] }}</p>
                                </div>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                                    <p class="text-xs text-red-600 font-medium">Rusak</p>
                                    <p class="text-2xl font-bold text-red-700">{{ $statistics['rusak'] }}</p>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
                                    <p class="text-xs text-blue-600 font-medium">Total Unit</p>
                                    <p class="text-2xl font-bold text-blue-700">{{ $statistics['total_unit'] }}</p>
                                </div>
                            </div>
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 text-center">
                                <p class="text-xs text-orange-600 font-medium">Sedang Dipinjam</p>
                                <p class="text-xl font-bold text-orange-700">{{ $statistics['dipinjam'] }} unit</p>
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
