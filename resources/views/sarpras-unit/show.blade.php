<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Unit: {{ $unit->kode_unit }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $sarpras->nama_barang }}</p>
            </div>
            <div class="mt-3 sm:mt-0 flex space-x-2">
                <a href="{{ route('sarpras.units.index', $sarpras) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold uppercase hover:bg-gray-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
                <a href="{{ route('sarpras.units.edit', [$sarpras, $unit]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Unit Info Card --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Unit</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Kode Unit</p>
                            <p class="font-semibold font-mono">{{ $unit->kode_unit }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Lokasi</p>
                            <p class="font-semibold">{{ $unit->lokasi->nama_lokasi ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kondisi</p>
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
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
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
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Perolehan</p>
                            <p class="font-semibold">{{ $unit->tanggal_perolehan ? $unit->tanggal_perolehan->format('d M Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nilai Perolehan</p>
                            <p class="font-semibold">{{ $unit->nilai_perolehan ? 'Rp ' . number_format($unit->nilai_perolehan, 0, ',', '.') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat Peminjaman --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Peminjaman</h3>
                    @if ($unit->peminjamanDetails->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pinjam</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Kembali</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($unit->peminjamanDetails as $detail)
                                        <tr>
                                            <td class="px-4 py-2 text-sm">{{ $detail->peminjaman->user->name ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $detail->peminjaman->tgl_pinjam->format('d M Y') }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $detail->peminjaman->tgl_kembali_rencana->format('d M Y') }}</td>
                                            <td class="px-4 py-2 text-center">
                                                <span class="px-2 py-1 text-xs rounded-full {{ $detail->peminjaman->status === 'selesai' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                                    {{ ucfirst($detail->peminjaman->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada riwayat peminjaman untuk unit ini.</p>
                    @endif
                </div>
            </div>

            {{-- Riwayat Maintenance --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Maintenance</h3>
                    @if ($unit->maintenances->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($unit->maintenances as $maintenance)
                                        <tr>
                                            <td class="px-4 py-2 text-sm">{{ ucfirst(str_replace('_', ' ', $maintenance->jenis)) }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $maintenance->tanggal_mulai->format('d M Y') }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $maintenance->tanggal_selesai ? $maintenance->tanggal_selesai->format('d M Y') : '-' }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $maintenance->petugas->name ?? '-' }}</td>
                                            <td class="px-4 py-2 text-center">
                                                <span class="px-2 py-1 text-xs rounded-full {{ $maintenance->status === 'selesai' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada riwayat maintenance untuk unit ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
