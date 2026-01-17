<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Peminjaman') }}
            </h2>
            @if (auth()->user()->role === 'peminjam')
                <a href="{{ route('peminjaman.create') }}" 
                   class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajukan Peminjaman
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-lg p-4 text-white">
                            <p class="text-yellow-100 text-sm">Menunggu</p>
                            <p class="text-2xl font-bold">{{ $peminjaman->where('status', 'menunggu')->count() }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                            <p class="text-green-100 text-sm">Disetujui</p>
                            <p class="text-2xl font-bold">{{ $peminjaman->where('status', 'disetujui')->count() }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                            <p class="text-blue-100 text-sm">Selesai</p>
                            <p class="text-2xl font-bold">{{ $peminjaman->where('status', 'selesai')->count() }}</p>
                        </div>
                        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
                            <p class="text-red-100 text-sm">Ditolak</p>
                            <p class="text-2xl font-bold">{{ $peminjaman->where('status', 'ditolak')->count() }}</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    @if (auth()->user()->role !== 'peminjam')
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Pinjam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Kembali</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($peminjaman as $pinjam)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                        @if (auth()->user()->role !== 'peminjam')
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $pinjam->user->name ?? '-' }}</div>
                                                <div class="text-sm text-gray-500">{{ $pinjam->user->email ?? '-' }}</div>
                                            </td>
                                        @endif
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $pinjam->sarpras->nama_barang ?? '-' }}</div>
                                            <div class="text-sm text-gray-500">{{ $pinjam->sarpras->kode_barang ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center text-lg font-semibold">{{ $pinjam->jumlah_pinjam }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $pinjam->tgl_pinjam?->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $pinjam->tgl_kembali_rencana?->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @switch($pinjam->status)
                                                @case('menunggu')
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>
                                                    @break
                                                @case('disetujui')
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                                                    @break
                                                @case('selesai')
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Selesai</span>
                                                    @break
                                                @case('ditolak')
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a href="{{ route('peminjaman.show', $pinjam) }}" class="text-gray-600 hover:text-gray-900" title="Detail">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                @if (auth()->user()->role !== 'peminjam')
                                                    @if ($pinjam->status === 'menunggu')
                                                        <form action="{{ route('peminjaman.update', $pinjam) }}" method="POST" class="inline">
                                                            @csrf @method('PUT')
                                                            <input type="hidden" name="status" value="disetujui">
                                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Setujui" onclick="return confirm('Setujui peminjaman?')">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('peminjaman.update', $pinjam) }}" method="POST" class="inline">
                                                            @csrf @method('PUT')
                                                            <input type="hidden" name="status" value="ditolak">
                                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Tolak" onclick="return confirm('Tolak peminjaman?')">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                        </form>
                                                    @elseif ($pinjam->status === 'disetujui')
                                                        <form action="{{ route('peminjaman.update', $pinjam) }}" method="POST" class="inline">
                                                            @csrf @method('PUT')
                                                            <input type="hidden" name="status" value="selesai">
                                                            <button type="submit" class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-md hover:bg-blue-200" onclick="return confirm('Barang sudah dikembalikan?')">Kembali</button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->role !== 'peminjam' ? 8 : 7 }}" class="px-6 py-12 text-center text-gray-500">
                                            Belum ada data peminjaman
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($peminjaman->hasPages())
                        <div class="mt-6 border-t pt-4">{{ $peminjaman->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
