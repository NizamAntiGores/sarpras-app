<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Maintenance</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $maintenance->sarprasUnit->kode_unit }}</p>
            </div>
            <div class="mt-3 sm:mt-0 flex space-x-2">
                <a href="{{ route('maintenance.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold uppercase hover:bg-gray-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
                @if ($maintenance->status === 'sedang_berlangsung')
                    <a href="{{ route('maintenance.edit', $maintenance) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500">Unit</p>
                            <p class="font-semibold font-mono">{{ $maintenance->sarprasUnit->kode_unit }}</p>
                            <p class="text-sm text-gray-600">{{ $maintenance->sarprasUnit->sarpras->nama_barang }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Jenis Maintenance</p>
                            <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $maintenance->jenis)) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Mulai</p>
                            <p class="font-semibold">{{ $maintenance->tanggal_mulai->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Selesai</p>
                            <p class="font-semibold">{{ $maintenance->tanggal_selesai ? $maintenance->tanggal_selesai->format('d M Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Petugas</p>
                            <p class="font-semibold">{{ $maintenance->petugas->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Biaya</p>
                            <p class="font-semibold">{{ $maintenance->biaya ? 'Rp ' . number_format($maintenance->biaya, 0, ',', '.') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
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
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kondisi Unit Saat Ini</p>
                            @switch($maintenance->sarprasUnit->kondisi)
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
                        </div>
                    </div>

                    @if ($maintenance->deskripsi)
                        <div class="mt-6 pt-6 border-t">
                            <p class="text-sm text-gray-500 mb-2">Deskripsi</p>
                            <p class="text-gray-700">{{ $maintenance->deskripsi }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
