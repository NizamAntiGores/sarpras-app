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
                                <p><span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">{{ $sarpras->kategori->nama_kategori ?? '-' }}</span></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Lokasi</label>
                                <p>{{ $sarpras->lokasi }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            {{-- Stok Cards --}}
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                                    <p class="text-xs text-green-600 font-medium">Tersedia</p>
                                    <p class="text-2xl font-bold text-green-700">{{ $sarpras->stok }}</p>
                                </div>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                                    <p class="text-xs text-red-600 font-medium">Rusak</p>
                                    <p class="text-2xl font-bold text-red-700">{{ $sarpras->stok_rusak }}</p>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
                                    <p class="text-xs text-blue-600 font-medium">Total</p>
                                    <p class="text-2xl font-bold text-blue-700">{{ $sarpras->stok + $sarpras->stok_rusak }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Kondisi Awal</label>
                                <p><span class="px-2 py-1 rounded-full text-sm {{ $sarpras->kondisi_awal === 'baik' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($sarpras->kondisi_awal) }}</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t flex space-x-3">
                        <a href="{{ route('sarpras.edit', $sarpras) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Edit</a>
                        <a href="{{ route('sarpras.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
