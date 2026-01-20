<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Maintenance</h2>
            <a href="{{ route('maintenance.create') }}" class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Maintenance
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

            {{-- Filter --}}
            <div class="bg-white shadow-sm rounded-lg mb-6 p-4">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">Semua Status</option>
                            <option value="sedang_berlangsung" {{ $filters['status'] == 'sedang_berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                            <option value="selesai" {{ $filters['status'] == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ $filters['status'] == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div>
                        <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                        <select name="jenis" id="jenis" class="rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">Semua Jenis</option>
                            <option value="perbaikan" {{ $filters['jenis'] == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                            <option value="servis_rutin" {{ $filters['jenis'] == 'servis_rutin' ? 'selected' : '' }}>Servis Rutin</option>
                            <option value="kalibrasi" {{ $filters['jenis'] == 'kalibrasi' ? 'selected' : '' }}>Kalibrasi</option>
                            <option value="penggantian_komponen" {{ $filters['jenis'] == 'penggantian_komponen' ? 'selected' : '' }}>Penggantian Komponen</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700">Filter</button>
                    <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300">Reset</a>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($maintenances as $maintenance)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-medium text-gray-900 font-mono">{{ $maintenance->sarprasUnit->kode_unit }}</div>
                                            <div class="text-xs text-gray-500">{{ $maintenance->sarprasUnit->sarpras->nama_barang ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700">
                                            {{ ucfirst(str_replace('_', ' ', $maintenance->jenis)) }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700">
                                            {{ $maintenance->tanggal_mulai->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700">
                                            {{ $maintenance->tanggal_selesai ? $maintenance->tanggal_selesai->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700">
                                            {{ $maintenance->petugas->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            @switch($maintenance->status)
                                                @case('sedang_berlangsung')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Sedang Berlangsung</span>
                                                    @break
                                                @case('selesai')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Selesai</span>
                                                    @break
                                                @case('dibatalkan')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Dibatalkan</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a href="{{ route('maintenance.show', $maintenance) }}" class="text-gray-600 hover:text-gray-900" title="Detail">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                @if ($maintenance->status === 'sedang_berlangsung')
                                                    <a href="{{ route('maintenance.edit', $maintenance) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">Belum ada data maintenance</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($maintenances->hasPages())
                        <div class="mt-6 border-t pt-4">{{ $maintenances->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
