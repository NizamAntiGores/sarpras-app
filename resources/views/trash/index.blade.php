<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tong Sampah (Unit Dihapusbukukan)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 text-sm text-gray-600">
                        Halaman ini menampilkan unit-unit yang telah dihapus (dihapusbukukan). Anda dapat mengembalikannya (restore) agar bisa digunakan kembali di sistem.
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Unit</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kondisi Terakhir</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Dihapus</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($units as $unit)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $unit->sarpras->nama_barang }}</div>
                                            <div class="text-xs text-gray-500">{{ $unit->sarpras->kategori->nama_kategori ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900">{{ $unit->kode_unit }}</td>
                                        <td class="px-4 py-4">
                                            @if($unit->kondisi === 'rusak_berat')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rusak Berat</span>
                                            @elseif($unit->kondisi === 'rusak_ringan')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Rusak Ringan</span>
                                            @elseif($unit->kondisi === 'baik')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Baik</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">
                                            {{ $unit->updated_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            <form action="{{ route('trash.restore', $unit->id) }}" method="POST" onsubmit="return confirm('Kembalikan unit ini ke stok tersedia?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    Restore
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span class="mt-2 block text-sm font-medium text-gray-900">Tong sampah kosong</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($units->hasPages())
                        <div class="mt-4">
                            {{ $units->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
