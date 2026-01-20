<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Sarpras</h2>
            <a href="{{ route('sarpras.create') }}" class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Barang
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    {{-- Stats Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                            <p class="text-blue-100 text-sm">Jenis Barang</p>
                            <p class="text-2xl font-bold">{{ $sarpras->total() }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                            <p class="text-green-100 text-sm">Unit Tersedia</p>
                            <p class="text-2xl font-bold">{{ $sarpras->sum('tersedia_count') }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                            <p class="text-orange-100 text-sm">Unit Dipinjam</p>
                            <p class="text-2xl font-bold">{{ $sarpras->sum('dipinjam_count') }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
                            <p class="text-red-100 text-sm">Unit Maintenance</p>
                            <p class="text-2xl font-bold">{{ $sarpras->sum('maintenance_count') }}</p>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Unit</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
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
                                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4"><span class="px-2 py-1 text-xs font-mono bg-gray-100 rounded">{{ $item->kode_barang }}</span></td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->nama_barang }}</div>
                                        </td>
                                        <td class="px-4 py-4"><span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">{{ $item->kategori->nama_kategori ?? '-' }}</span></td>
                                        <td class="px-4 py-4 text-center">
                                            {{-- Ringkasan Unit --}}
                                            <div class="flex flex-col items-center">
                                                <span class="text-lg font-bold text-gray-700">{{ $item->total_unit ?? 0 }}</span>
                                                <div class="flex gap-1 mt-1">
                                                    @if (($item->tersedia_count ?? 0) > 0)
                                                        <span class="px-1.5 py-0.5 text-xs rounded bg-green-100 text-green-700" title="Tersedia">{{ $item->tersedia_count }}</span>
                                                    @endif
                                                    @if (($item->dipinjam_count ?? 0) > 0)
                                                        <span class="px-1.5 py-0.5 text-xs rounded bg-orange-100 text-orange-700" title="Dipinjam">{{ $item->dipinjam_count }}</span>
                                                    @endif
                                                    @if (($item->maintenance_count ?? 0) > 0)
                                                        <span class="px-1.5 py-0.5 text-xs rounded bg-red-100 text-red-700" title="Maintenance">{{ $item->maintenance_count }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                {{-- Tombol Lihat Unit & Tambah Unit --}}
                                                <div class="flex gap-2">
                                                    <a href="{{ route('sarpras.units.index', $item) }}" 
                                                       class="inline-flex items-center px-2 py-1 bg-teal-100 text-teal-700 rounded text-xs font-medium hover:bg-teal-200"
                                                       title="Lihat unit {{ $item->nama_barang }}">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                                        Lihat Unit
                                                    </a>
                                                    <a href="{{ route('sarpras.units.create', $item) }}" 
                                                       class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium hover:bg-green-200"
                                                       title="Tambah unit {{ $item->nama_barang }}">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                        Tambah Unit
                                                    </a>
                                                </div>
                                                {{-- Tombol Detail, Edit, Hapus --}}
                                                <div class="flex items-center justify-center space-x-2">
                                                    <a href="{{ route('sarpras.show', $item) }}" class="text-gray-600 hover:text-gray-900" title="Detail">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </a>
                                                    <a href="{{ route('sarpras.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    </a>
                                                    @if (auth()->user()->role === 'admin')
                                                        <form action="{{ route('sarpras.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus barang ini?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada data sarpras</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($sarpras->hasPages())
                        <div class="mt-6 border-t pt-4">{{ $sarpras->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
