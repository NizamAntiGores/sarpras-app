<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Unit: {{ $sarpras->nama_barang }}</h2>
                <p class="text-sm text-gray-500 mt-1">Kode: {{ $sarpras->kode_barang }}</p>
            </div>
            <div class="mt-3 sm:mt-0 flex space-x-2">
                <a href="{{ route('sarpras.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold uppercase hover:bg-gray-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
                <a href="{{ route('sarpras.units.create', $sarpras) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Unit
                </a>
            </div>
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

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Total Unit</p>
                    <p class="text-2xl font-bold text-gray-700">{{ $stats['total_unit'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Tersedia</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['tersedia'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Dipinjam</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['dipinjam'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-sm">Maintenance</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['maintenance'] }}</p>
                </div>
            </div>

            {{-- Search & Filter Section --}}
            <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
                <form action="{{ route('sarpras.units.index', $sarpras) }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Cari Kode Unit</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Contoh: TAB-001"
                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Kondisi</label>
                        <select name="kondisi" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kondisi</option>
                            <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak_ringan" {{ request('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak_berat" {{ request('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                            Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status', 'kondisi']))
                            <a href="{{ route('sarpras.units.index', $sarpras) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Unit</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Perolehan</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($units as $unit)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <span class="px-2 py-1 text-sm font-mono bg-gray-100 rounded font-semibold">{{ $unit->kode_unit }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700">
                                            {{ $unit->lokasi->nama_lokasi ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            @switch($unit->kondisi)
                                                @case('baik')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Baik</span>
                                                    @break
                                                @case('rusak_ringan')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Rusak Ringan</span>
                                                    @break
                                                @case('rusak_berat')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rusak Berat</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            @switch($unit->status)
                                                @case('tersedia')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Tersedia</span>
                                                    @break
                                                @case('dipinjam')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Dipinjam</span>
                                                    @break
                                                @case('maintenance')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Maintenance</span>
                                                    @break
                                                @case('dihapusbukukan')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Dihapusbukukan</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">
                                            {{ $unit->tanggal_perolehan ? $unit->tanggal_perolehan->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a href="{{ route('sarpras.units.show', [$sarpras, $unit]) }}" class="text-gray-600 hover:text-gray-900" title="Detail">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                <a href="{{ route('sarpras.units.edit', [$sarpras, $unit]) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                                @if ($unit->status !== 'dipinjam')
                                                    <a href="{{ route('maintenance.create', ['unit_id' => $unit->id]) }}" class="text-teal-600 hover:text-teal-900" title="Maintenance">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    </a>
                                                @endif
                                                @if ($unit->status !== 'dipinjam')
                                                    <form action="{{ route('sarpras.units.destroy', [$sarpras, $unit]) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapusbukukan unit ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapusbukukan">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <p>Belum ada unit untuk barang ini.</p>
                                            <a href="{{ route('sarpras.units.create', $sarpras) }}" class="text-blue-600 hover:underline mt-2 inline-block">Tambahkan unit sekarang</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($units->hasPages())
                        <div class="mt-6 border-t pt-4">{{ $units->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
