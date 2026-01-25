<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tong Sampah & Pemulihan Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- SECTION 1: MASTER BARANG (SARPRAS) --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Master Barang Terhapus
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-red-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-red-800 uppercase">Nama Barang</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-red-800 uppercase">Kode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-red-800 uppercase">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-red-800 uppercase">Dihapus Pada</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-red-800 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($deletedSarpras as $sarpras)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 font-medium text-gray-900">
                                            {{ $sarpras->nama_barang }}
                                            <div class="text-xs text-gray-500">Total Unit: {{ $sarpras->units()->count() }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">{{ $sarpras->kode_barang }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-500">{{ $sarpras->kategori->nama_kategori ?? '-' }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-500">{{ $sarpras->deleted_at->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-4">
                                            <form action="{{ route('trash.sarpras.restore', $sarpras->id) }}" method="POST" onsubmit="return confirm('Restore Master Barang ini?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-sm font-medium hover:bg-green-200 flex items-center transition">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    Restore
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data master barang di sampah.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $deletedSarpras->appends(['unit_page' => $units->currentPage()])->links() }}
                    </div>
                </div>
            </div>

            {{-- SECTION 2: UNIT BARANG --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        Unit Barang Dihapusbukukan
                    </h3>
                    <div class="mb-4 text-sm text-gray-600">
                        Unit yang "Dihapusbukukan" sebenarnya tidak hilang, hanya dinonaktifkan. Anda bisa mengembalikannya ke status "Tersedia".
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Unit</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Dihapus</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($units as $unit)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $unit->sarpras->nama_barang ?? '-' }}</div>
                                            @if($unit->sarpras && $unit->sarpras->trashed())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    Parent Deleted
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 font-mono">{{ $unit->kode_unit }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-500">{{ $unit->lokasi->nama_lokasi ?? '-' }}</td>
                                        <td class="px-4 py-4">
                                            @if($unit->kondisi === 'rusak_berat')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rusak Berat</span>
                                            @elseif($unit->kondisi === 'rusak_ringan')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Rusak Ringan</span>
                                            @elseif($unit->kondisi === 'baik')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Baik</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">{{ $unit->updated_at->format('d M Y') }}</td>
                                        <td class="px-4 py-4 text-sm">
                                            @if($unit->sarpras && $unit->sarpras->trashed())
                                                <button disabled class="text-gray-400 cursor-not-allowed flex items-center" title="Harap restore Master Barang terlebih dahulu">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    Locked
                                                </button>
                                            @else
                                                <form action="{{ route('trash.restore', $unit->id) }}" method="POST" onsubmit="return confirm('Kembalikan unit ini ke stok tersedia?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-blue-600 hover:text-blue-900 font-medium flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                        Restore Unit
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            <span class="block italic">Tidak ada unit yang dihapusbukukan saat ini.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4">
                        {{ $units->appends(['sarpras_page' => $deletedSarpras->currentPage()])->links() }}
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
