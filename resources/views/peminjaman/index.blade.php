<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Peminjaman') }}
            </h2>
            @if (in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']))
                <a href="{{ route('peminjaman.create') }}" 
                   class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150">
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
                    
                    {{-- FILTER SECTION --}}
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <form method="GET" action="{{ route('peminjaman.index') }}" class="flex flex-wrap items-end gap-4">
                            <div class="flex-1 min-w-[150px]">
                                <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Semua Status</option>
                                    <option value="menunggu" {{ ($filters['status'] ?? '') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="disetujui" {{ ($filters['status'] ?? '') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="selesai" {{ ($filters['status'] ?? '') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="ditolak" {{ ($filters['status'] ?? '') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="flex-1 min-w-[150px]">
                                <label for="tanggal_mulai" class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}"
                                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="flex-1 min-w-[150px]">
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}"
                                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="flex-1 min-w-[200px]">
                                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Cari</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Nama Peminjam / Barang..."
                                       oninput="debounceSubmit()">
                            </div>
                            <script>
                                let timeout = null;
                                function debounceSubmit() {
                                    clearTimeout(timeout);
                                    timeout = setTimeout(function () {
                                        const form = document.getElementById('search').closest('form');
                                        form.submit();
                                    }, 600);
                                }
                                document.addEventListener("DOMContentLoaded", function() {
                                    const searchInput = document.getElementById('search');
                                    if(searchInput && searchInput.value) {
                                        searchInput.focus();
                                        const val = searchInput.value;
                                        searchInput.value = '';
                                        searchInput.value = val;
                                    }
                                });
                            </script>
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                    Filter
                                </button>
                                @if (($filters['status'] ?? '') || ($filters['tanggal_mulai'] ?? '') || ($filters['tanggal_selesai'] ?? '') || request('search'))
                                    <a href="{{ route('peminjaman.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- QR CODE QUICK LOOKUP - Hanya untuk Admin/Petugas --}}
                    @if(in_array(auth()->user()->role, ['admin', 'petugas']))
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 mb-6 border border-blue-200">
                        <form method="POST" action="{{ route('pengembalian.lookup-qr') }}" class="flex flex-wrap items-end gap-4">
                            @csrf
                            <div class="flex-1">
                                <label for="qr_code" class="block text-xs font-medium text-gray-700 mb-1">
                                    📱 Cari Peminjaman dengan Kode QR
                                </label>
                                <input type="text" name="qr_code" id="qr_code" 
                                       class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Masukkan atau scan kode QR peminjaman..." autofocus>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Cari & Proses Pengembalian
                            </button>
                        </form>
                    </div>
                    @endif

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
                                    @if (!in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']))
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Pinjam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Kembali</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($peminjaman as $pinjam)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">#{{ $pinjam->id }}</td>
                                        @if (!in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']))
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $pinjam->user->name ?? '-' }}</div>
                                                <div class="text-sm text-gray-500">{{ $pinjam->user->email ?? '-' }}</div>
                                            </td>
                                        @endif
                                        <td class="px-6 py-4">
                                            @if ($pinjam->details && $pinjam->details->count() > 0)
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $pinjam->details->count() }} unit
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    @foreach ($pinjam->details->take(2) as $detail)
                                                        <span class="inline-block bg-gray-100 rounded px-1 mr-1">{{ $detail->sarprasUnit->kode_unit ?? '-' }}</span>
                                                    @endforeach
                                                    @if ($pinjam->details->count() > 2)
                                                        <span class="text-gray-400">+{{ $pinjam->details->count() - 2 }} lagi</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $pinjam->tgl_pinjam?->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $pinjam->tgl_kembali_rencana?->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @switch($pinjam->status)
                                                @case('menunggu')
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>
                                                    @if(Str::contains($pinjam->keterangan, '[REQ-EXT]'))
                                                        <div class="mt-1">
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                                                🔄 Minta Perpanjangan
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @break
                                                @case('disetujui')
                                                    @if($pinjam->isReadyForPickup())
                                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">📦 Siap Diambil</span>
                                                    @else
                                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sedang Dipinjam</span>
                                                    @endif

                                                    @if ($pinjam->tgl_kembali_rencana && $pinjam->isOngoing())
                                                        @php
                                                            $daysLeft = now()->diffInDays($pinjam->tgl_kembali_rencana, false);
                                                        @endphp
                                                        
                                                        <div class="mt-1">
                                                            @if ($daysLeft < 0)
                                                                {{-- Terlambat --}}
                                                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-red-600 text-white">
                                                                    ⚠️ Telat {{ abs(intval($daysLeft)) }} Hari
                                                                </span>
                                                            @elseif ($daysLeft < 1)
                                                                {{-- Hari Ini Terakhir --}}
                                                                @if(now()->isSameDay($pinjam->tgl_kembali_rencana))
                                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-orange-500 text-white">
                                                                        📅 Hari Ini
                                                                    </span>
                                                                @else
                                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-yellow-500 text-white">
                                                                        ⏳ Besok
                                                                    </span>
                                                                @endif
                                                            @elseif ($daysLeft <= 3)
                                                                {{-- Kurang dari 3 hari (Warning) --}}
                                                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-yellow-400 text-yellow-900">
                                                                    ⏳ Sisa {{ intval($daysLeft) }} Hari
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif
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
                                                @if (!in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']))
                                                    {{-- TOMBOL AKSI UTAMA BERDASARKAN STATUS --}}
                                                    @if ($pinjam->status === 'menunggu')
                                                        <a href="{{ route('peminjaman.edit', array_merge(['peminjaman' => $pinjam->id], request()->query())) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white text-xs font-bold rounded hover:bg-yellow-600 transition shadow-sm" title="Verifikasi Pengajuan">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                            Verifikasi
                                                        </a>
                                                    @elseif ($pinjam->status === 'disetujui')
                                                        @if($pinjam->isReadyForPickup())
                                                            <a href="{{ route('peminjaman.handover', array_merge(['peminjaman' => $pinjam->id], request()->query())) }}" class="inline-flex items-center px-3 py-1.5 bg-amber-600 text-white text-xs font-bold rounded hover:bg-amber-700 transition shadow-sm" title="Serah Terima Barang">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                                Serah Terima
                                                            </a>
                                                        @else
                                                            <a href="{{ route('pengembalian.create', array_merge(['peminjaman' => $pinjam->id], request()->query())) }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700 transition shadow-sm" title="Proses Pengembalian">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                                Kembalikan
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif

                                                {{-- TOMBOL DETAIL (SELALU ADA) --}}
                                                <a href="{{ route('peminjaman.show', array_merge(['peminjaman' => $pinjam->id], request()->query())) }}" class="inline-flex items-center px-2 py-1.5 {{ (!in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']) && in_array($pinjam->status, ['menunggu', 'disetujui'])) ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }} text-xs font-medium rounded transition" title="Lihat Detail">
                                                    @if (!in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']) && in_array($pinjam->status, ['menunggu', 'disetujui']))
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    @else
                                                        Detail
                                                    @endif
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ !in_array(auth()->user()->role, ['peminjam', 'guru', 'siswa']) ? 7 : 6 }}" class="px-6 py-12 text-center text-gray-500">
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
