<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Barang Rusak') }}
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                    <p class="text-orange-100 text-sm">Jenis Barang Rusak</p>
                    <p class="text-3xl font-bold">{{ $totalJenis }}</p>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
                    <p class="text-red-100 text-sm">Total Unit Rusak</p>
                    <p class="text-3xl font-bold">{{ $totalUnit }} unit</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    @if ($barangRusak->isEmpty())
                        <div class="text-center py-12 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-2">Tidak ada barang rusak 🎉</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok Baik</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok Rusak</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($barangRusak as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->nama_barang }}</div>
                                                <div class="text-sm text-gray-500">{{ $item->kode_barang }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->lokasi->nama_lokasi ?? '-' }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="text-lg font-bold text-green-600">{{ $item->stok }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="text-lg font-bold text-red-600">{{ $item->stok_rusak }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col gap-2">
                                                    {{-- Form Perbaiki --}}
                                                    <form action="{{ route('barang-rusak.perbaiki', $item) }}" method="POST" class="flex gap-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="number" name="jumlah" min="1" max="{{ $item->stok_rusak }}" value="1" class="w-16 text-sm border-gray-300 rounded" required>
                                                        <button type="submit" class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded hover:bg-green-200" onclick="return confirm('Pindahkan ke stok tersedia?')">
                                                            🔧 Perbaiki
                                                        </button>
                                                    </form>
                                                    {{-- Form Hapus --}}
                                                    <form action="{{ route('barang-rusak.hapus', $item) }}" method="POST" class="flex gap-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="number" name="jumlah" min="1" max="{{ $item->stok_rusak }}" value="1" class="w-16 text-sm border-gray-300 rounded" required>
                                                        <button type="submit" class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded hover:bg-red-200" onclick="return confirm('Hapus dari inventaris? Tindakan ini tidak dapat dibatalkan.')">
                                                            🗑️ Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($barangRusak->hasPages())
                            <div class="mt-6 border-t pt-4">{{ $barangRusak->links() }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
