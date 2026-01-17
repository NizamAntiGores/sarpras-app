<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Welcome Card --}}
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h3>
                        <p class="text-indigo-100 mt-1">Role: <span class="font-semibold capitalize">{{ auth()->user()->role }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-indigo-100 text-sm">{{ now()->format('l, d F Y') }}</p>
                        <p class="text-indigo-100 text-sm">{{ now()->format('H:i') }} WIB</p>
                    </div>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- DASHBOARD ADMIN --}}
            {{-- ========================================== --}}
            @if (auth()->user()->role === 'admin')
                {{-- KARTU INVENTARIS UTAMA - 4 Kolom --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    {{-- Barang Siap Pakai --}}
                    <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-xl shadow-lg p-5 text-white">
                        <p class="text-green-100 text-xs font-medium uppercase tracking-wide">Barang Siap Pakai</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($data['stokTersedia'] ?? 0) }}</p>
                        <p class="text-green-100 text-xs mt-1">unit tersedia</p>
                    </div>

                    {{-- Barang Rusak --}}
                    <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-xl shadow-lg p-5 text-white">
                        <p class="text-red-100 text-xs font-medium uppercase tracking-wide">Barang Rusak</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($data['stokRusak'] ?? 0) }}</p>
                        <p class="text-red-100 text-xs mt-1">unit rusak</p>
                    </div>

                    {{-- Sedang Dipinjam --}}
                    <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl shadow-lg p-5 text-white">
                        <p class="text-orange-100 text-xs font-medium uppercase tracking-wide">Sedang Dipinjam</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($data['sedangDipinjam'] ?? 0) }}</p>
                        <p class="text-orange-100 text-xs mt-1">unit dipinjam</p>
                    </div>

                    {{-- Total Inventaris --}}
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl shadow-lg p-5 text-white">
                        <p class="text-blue-100 text-xs font-medium uppercase tracking-wide">Total Inventaris</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($data['totalInventaris'] ?? 0) }}</p>
                        <p class="text-blue-100 text-xs mt-1">unit total aset</p>
                    </div>
                </div>

                {{-- Statistik Lainnya --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow p-4">
                        <p class="text-sm text-gray-500">Total User</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $data['totalUsers'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4">
                        <p class="text-sm text-gray-500">Jenis Barang</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $data['totalJenisSarpras'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4">
                        <p class="text-sm text-gray-500">Menunggu Verifikasi</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $data['peminjamanMenunggu'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4">
                        <p class="text-sm text-gray-500">Total Transaksi</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $data['totalPeminjaman'] ?? 0 }}</p>
                    </div>
                </div>

                {{-- User Stats --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-red-600 font-medium">Admin</p>
                        <p class="text-3xl font-bold text-red-700">{{ $data['totalAdmin'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-blue-600 font-medium">Petugas</p>
                        <p class="text-3xl font-bold text-blue-700">{{ $data['totalPetugas'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-green-600 font-medium">Peminjam/Siswa</p>
                        <p class="text-3xl font-bold text-green-700">{{ $data['totalPeminjam'] ?? 0 }}</p>
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- DASHBOARD PETUGAS --}}
            {{-- ========================================== --}}
            @elseif (auth()->user()->role === 'petugas')
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-xl shadow-lg p-5 text-white">
                        <p class="text-green-100 text-xs">Barang Siap Pakai</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($data['stokTersedia'] ?? 0) }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-xl shadow-lg p-5 text-white">
                        <p class="text-red-100 text-xs">Barang Rusak</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($data['stokRusak'] ?? 0) }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl shadow-lg p-5 text-white">
                        <p class="text-orange-100 text-xs">Sedang Dipinjam</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($data['sedangDipinjam'] ?? 0) }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl shadow-lg p-5 text-white">
                        <p class="text-blue-100 text-xs">Total Inventaris</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($data['totalInventaris'] ?? 0) }}</p>
                    </div>
                </div>

                @if (($data['peminjamanMenungguHariIni'] ?? 0) > 0)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg">
                        <div class="flex">
                            <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <p class="text-yellow-700 font-medium">Ada {{ $data['peminjamanMenungguHariIni'] }} pengajuan baru hari ini!</p>
                                <a href="{{ route('peminjaman.index') }}" class="text-yellow-600 underline text-sm">Lihat sekarang →</a>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500">
                        <p class="text-sm text-gray-500">Perlu Verifikasi</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $data['peminjamanMenunggu'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
                        <p class="text-sm text-gray-500">Sedang Dipinjam (Aktif)</p>
                        <p class="text-3xl font-bold text-green-600">{{ $data['peminjamanDisetujui'] ?? 0 }}</p>
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- DASHBOARD PEMINJAM/SISWA --}}
            {{-- ========================================== --}}
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow p-4">
                        <p class="text-sm text-gray-500">Total Peminjaman Saya</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $data['totalPeminjamanSaya'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4">
                        <p class="text-sm text-gray-500">Menunggu</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $data['peminjamanMenunggu'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4">
                        <p class="text-sm text-gray-500">Disetujui</p>
                        <p class="text-3xl font-bold text-green-600">{{ $data['peminjamanDisetujui'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4">
                        <p class="text-sm text-gray-500">Selesai</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $data['peminjamanSelesai'] ?? 0 }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6 mb-6">
                    <h4 class="font-semibold text-gray-800 mb-4">Aksi Cepat</h4>
                    <a href="{{ route('peminjaman.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajukan Peminjaman Baru
                    </a>
                </div>
            @endif

            {{-- Recent Activity --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h4 class="font-semibold text-gray-800">
                        @if (auth()->user()->role === 'petugas')
                            Pengajuan Menunggu Verifikasi
                        @else
                            Aktivitas Terbaru
                        @endif
                    </h4>
                </div>
                <div class="p-4">
                    @if (isset($data['recentPeminjaman']) && $data['recentPeminjaman']->count() > 0)
                        <div class="space-y-3">
                            @foreach ($data['recentPeminjaman'] as $pinjam)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $pinjam->sarpras->nama_barang ?? '-' }}</p>
                                        <p class="text-sm text-gray-500">
                                            @if (auth()->user()->role !== 'peminjam')
                                                {{ $pinjam->user->name ?? '-' }} •
                                            @endif
                                            {{ $pinjam->jumlah_pinjam }} unit • {{ $pinjam->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $pinjam->status === 'menunggu' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $pinjam->status === 'disetujui' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $pinjam->status === 'selesai' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $pinjam->status === 'ditolak' ? 'bg-red-100 text-red-800' : '' }}
                                    ">{{ ucfirst($pinjam->status) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Belum ada aktivitas</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
