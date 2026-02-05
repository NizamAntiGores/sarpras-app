<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center bg-white border-b border-gray-200">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4 sm:mb-0">
                {{ __('Daftar Pengaduan') }}
            </h2>

            <div class="flex space-x-3">
                @if(in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']))
                    <a href="{{ route('pengaduan.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Buat Pengaduan
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Message --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Filter & Search --}}
            <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('pengaduan.index') }}"
                    class="flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-4">
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                                placeholder="Cari judul atau pelapor...">
                        </div>
                    </div>

                    <div class="w-full md:w-48">
                        <select name="status" onchange="this.form.submit()"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg transition duration-150 ease-in-out">
                            <option value="">Semua Status</option>
                            <option value="belum_ditindaklanjuti" {{ request('status') == 'belum_ditindaklanjuti' ? 'selected' : '' }}>Belum Ditindaklanjuti</option>
                            <option value="sedang_diproses" {{ request('status') == 'sedang_diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai
                            </option>
                            <option value="ditutup" {{ request('status') == 'ditutup' ? 'selected' : '' }}>Ditutup
                            </option>
                        </select>
                    </div>
                </form>
            </div>

            {{-- Daftar Pengaduan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                @if($pengaduan->isEmpty())
                    <div class="p-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pengaduan</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada data pengaduan yang ditemukan.</p>
                    </div>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($pengaduan as $item)
                            <li class="hover:bg-gray-50 transition duration-150 ease-in-out ">
                                <a href="{{ route('pengaduan.show', $item) }}" class="block p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0 pr-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <p class="text-sm font-medium text-blue-600 truncate">
                                                    @if($item->jenis === 'tempat')
                                                        🏫 {{ $item->lokasi?->nama_lokasi ?? $item->lokasi_lainnya ?? 'Lokasi Tidak Diketahui' }}
                                                    @else
                                                        📦 {{ $item->sarpras?->nama_barang ?? $item->barang_lainnya ?? 'Barang Tidak Diketahui' }}
                                                    @endif
                                                </p>
                                                <div class="ml-2 flex-shrink-0 flex">
                                                    @php
                                                        $statusColors = [
                                                            'belum_ditindaklanjuti' => 'bg-red-100 text-red-800 border-red-200',
                                                            'sedang_diproses' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                            'selesai' => 'bg-green-100 text-green-800 border-green-200',
                                                            'ditutup' => 'bg-gray-100 text-gray-800 border-gray-200',
                                                        ];
                                                        $statusLabel = [
                                                            'belum_ditindaklanjuti' => 'Belum Ditindaklanjuti',
                                                            'sedang_diproses' => 'Sedang Diproses',
                                                            'selesai' => 'Selesai',
                                                            'ditutup' => 'Ditutup',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $statusColors[$item->status] }}">
                                                        {{ $statusLabel[$item->status] }}
                                                    </span>
                                                </div>
                                            </div>

                                            <h3
                                                class="text-lg font-bold text-gray-900 mb-1 group-hover:text-blue-600 transition-colors">
                                                {{ $item->judul }}
                                            </h3>

                                            <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                                {{ $item->deskripsi }}
                                            </p>

                                            <div class="flex items-center text-xs text-gray-500 space-x-4 mt-3">
                                                <div class="flex items-center">
                                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    {{ $item->user?->name ?? 'User Dihapus' }}
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ $item->created_at->format('d M Y, H:i') }}
                                                </div>
                                                @if($item->sarpras)
                                                    <div class="flex items-center">
                                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                        {{ $item->sarpras->nama_barang }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $pengaduan->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>