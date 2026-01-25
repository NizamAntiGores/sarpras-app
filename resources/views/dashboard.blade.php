<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Welcome Card --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl shadow-lg p-6 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h3>
                        <p class="text-blue-100 mt-1">Role: <span class="font-semibold capitalize">{{ auth()->user()->role }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-blue-100 text-sm">{{ now()->format('l, d F Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- DASHBOARD ADMIN --}}
            {{-- ========================================== --}}
            @if (auth()->user()->role === 'admin')

                {{-- Statistik Lainnya --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <a href="{{ route('users.index') }}" class="block transform hover:scale-105 transition-transform duration-200">
                        <div class="bg-white rounded-xl shadow p-4 h-full border-b-4 border-gray-400">
                            <p class="text-sm text-gray-500">Total User</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $data['totalUsers'] ?? 0 }}</p>
                        </div>
                    </a>
                    <a href="{{ route('sarpras.index') }}" class="block transform hover:scale-105 transition-transform duration-200">
                        <div class="bg-white rounded-xl shadow p-4 h-full border-b-4 border-blue-500">
                            <p class="text-sm text-gray-500">Jenis Barang</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $data['totalJenisSarpras'] ?? 0 }}</p>
                        </div>
                    </a>
                    <a href="{{ route('peminjaman.index', ['status' => 'menunggu']) }}" class="block transform hover:scale-105 transition-transform duration-200">
                        <div class="bg-white rounded-xl shadow p-4 h-full border-b-4 border-yellow-500">
                            <p class="text-sm text-gray-500">Menunggu Verifikasi</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ $data['peminjamanMenunggu'] ?? 0 }}</p>
                        </div>
                    </a>
                    <a href="{{ route('peminjaman.index') }}" class="block transform hover:scale-105 transition-transform duration-200">
                        <div class="bg-white rounded-xl shadow p-4 h-full border-b-4 border-purple-500">
                            <p class="text-sm text-gray-500">Total Transaksi</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $data['totalPeminjaman'] ?? 0 }}</p>
                        </div>
                    </a>
                </div>

                {{-- User Stats --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <a href="{{ route('users.index', ['role' => 'admin']) }}" class="block transform hover:scale-105 transition-transform duration-200">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 h-full hover:bg-red-100 transition">
                            <p class="text-red-600 font-medium">Admin</p>
                            <p class="text-3xl font-bold text-red-700">{{ $data['totalAdmin'] ?? 0 }}</p>
                        </div>
                    </a>
                    <a href="{{ route('users.index', ['role' => 'petugas']) }}" class="block transform hover:scale-105 transition-transform duration-200">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 h-full hover:bg-blue-100 transition">
                            <p class="text-blue-600 font-medium">Petugas</p>
                            <p class="text-3xl font-bold text-blue-700">{{ $data['totalPetugas'] ?? 0 }}</p>
                        </div>
                    </a>
                    <a href="{{ route('users.index', ['role' => 'peminjam']) }}" class="block transform hover:scale-105 transition-transform duration-200">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 h-full hover:bg-green-100 transition">
                            <p class="text-green-600 font-medium">Peminjam/Siswa</p>
                            <p class="text-3xl font-bold text-green-700">{{ $data['totalPeminjam'] ?? 0 }}</p>
                        </div>
                    </a>
                </div>

                {{-- STOK ALERT SECTION --}}
                @if (($data['stokHabis'] ?? collect())->count() > 0 || ($data['stokMenipis'] ?? collect())->count() > 0)
                    <div class="mb-6 space-y-4">
                        {{-- Stok Habis Alert --}}
                        @if (($data['stokHabis'] ?? collect())->count() > 0)
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-3 w-full">
                                        <div class="flex justify-between items-center mb-2">
                                            <a href="{{ route('sarpras.index', ['filter' => 'stok_habis']) }}" class="group flex items-center">
                                                <h3 class="text-red-800 font-semibold group-hover:underline cursor-pointer">🚨 Stok Habis ({{ $data['stokHabis']->count() }} barang)</h3>
                                            </a>
                                            <a href="{{ route('sarpras.index', ['filter' => 'stok_habis']) }}" class="text-xs text-red-600 font-bold hover:underline">Lihat Selengkapnya →</a>
                                        </div>
                                        <div class="max-h-64 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                                            @foreach ($data['stokHabis'] as $item)
                                                <li class="flex items-center justify-between bg-white p-2 rounded shadow-sm">
                                                    <div>
                                                        <span class="font-medium text-gray-800">{{ $item->nama_barang }}</span>
                                                        <span class="text-xs text-gray-500 block">{{ $item->lokasi->nama_lokasi ?? '-' }}</span>
                                                    </div>
                                                    <a href="{{ route('sarpras.units.create', $item->id) }}" class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold uppercase rounded hover:bg-red-200 transition">
                                                        + Restock
                                                    </a>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Stok Menipis Alert --}}
                        @if (($data['stokMenipis'] ?? collect())->count() > 0)
                            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-lg">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-3 w-full">
                                        <div class="flex justify-between items-center mb-2">
                                            <a href="{{ route('sarpras.index', ['filter' => 'stok_menipis']) }}" class="group flex items-center">
                                                <h3 class="text-yellow-800 font-semibold group-hover:underline cursor-pointer">⚠️ Stok Menipis ({{ $data['stokMenipis']->count() }} barang)</h3>
                                            </a>
                                            <a href="{{ route('sarpras.index', ['filter' => 'stok_menipis']) }}" class="text-xs text-yellow-700 font-bold hover:underline">Lihat Selengkapnya →</a>
                                        </div>
                                        <div class="max-h-64 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                                            @foreach ($data['stokMenipis'] as $item)
                                                <li class="flex items-center justify-between bg-white p-2 rounded shadow-sm">
                                                    <div>
                                                        <span class="font-medium text-gray-800">{{ $item->nama_barang }}</span>
                                                        <span class="text-xs font-bold text-yellow-600 block">Sisa {{ $item->available_count }} unit</span>
                                                    </div>
                                                    <a href="{{ route('sarpras.units.create', $item->id) }}" class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold uppercase rounded hover:bg-yellow-200 transition">
                                                        + Tambah Stok
                                                    </a>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- TOP 5 BARANG & RECENT ACTIVITY --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    {{-- Top 5 Barang Dipinjam --}}
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="p-4 border-b bg-gray-50">
                            <h4 class="font-semibold text-gray-800">📊 Top 5 Barang Paling Sering Dipinjam</h4>
                        </div>
                        <div class="p-4">
                            <canvas id="top5Chart" height="200"></canvas>
                        </div>
                    </div>

                    {{-- Quick Stats --}}
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="p-4 border-b bg-gray-50">
                            <h4 class="font-semibold text-gray-800">📈 Statistik Peminjaman</h4>
                        </div>
                        <div class="p-4">
                            <canvas id="statusChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- DASHBOARD PETUGAS --}}
            {{-- ========================================== --}}
            @elseif (auth()->user()->role === 'petugas')
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    {{-- Barang Siap Pakai --}}
                    <a href="{{ route('sarpras.index') }}" class="block transform hover:scale-105 transition-transform duration-200">
                        <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-xl shadow-lg p-5 text-white h-full">
                            <p class="text-green-100 text-xs">Barang Siap Pakai</p>
                            <p class="text-3xl font-bold mt-1">{{ number_format($data['tersedia'] ?? 0) }}</p>
                        </div>
                    </a>

                    {{-- Barang Rusak --}}
                    <a href="{{ route('laporan.asset-health') }}" class="block transform hover:scale-105 transition-transform duration-200">
                        <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-xl shadow-lg p-5 text-white h-full">
                            <p class="text-red-100 text-xs">Barang Rusak</p>
                            <p class="text-3xl font-bold mt-1">{{ number_format($data['maintenance'] ?? 0) }}</p>
                        </div>
                    </a>

                    {{-- Sedang Dipinjam --}}
                    <a href="{{ route('peminjaman.index', ['status' => 'disetujui']) }}" class="block transform hover:scale-105 transition-transform duration-200">
                        <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl shadow-lg p-5 text-white h-full">
                            <p class="text-orange-100 text-xs">Sedang Dipinjam</p>
                            <p class="text-3xl font-bold mt-1">{{ number_format($data['dipinjam'] ?? 0) }}</p>
                        </div>
                    </a>

                    {{-- Total Inventaris --}}
                    <a href="{{ route('sarpras.index') }}" class="block transform hover:scale-105 transition-transform duration-200">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl shadow-lg p-5 text-white h-full">
                            <p class="text-blue-100 text-xs">Total Inventaris</p>
                            <p class="text-3xl font-bold mt-1">{{ number_format($data['totalUnit'] ?? 0) }}</p>
                        </div>
                    </a>
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
                {{-- Statistik Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow p-4">
                        <p class="text-sm text-gray-500">Total Peminjaman</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $data['totalPeminjamanSaya'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500">
                        <p class="text-sm text-gray-500">Menunggu</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $data['peminjamanMenunggu'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
                        <p class="text-sm text-gray-500">Sedang Dipinjam</p>
                        <p class="text-3xl font-bold text-green-600">{{ $data['peminjamanDisetujui'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
                        <p class="text-sm text-gray-500">Selesai</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $data['peminjamanSelesai'] ?? 0 }}</p>
                    </div>
                </div>

                {{-- KATALOG BARANG - Langsung Ditampilkan --}}
                <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
                    <div class="p-4 border-b bg-gradient-to-r from-emerald-500 to-teal-600 text-white flex justify-between items-center">
                        <div>
                            <h4 class="font-semibold text-lg">📦 Katalog Barang</h4>
                            <p class="text-emerald-100 text-sm">{{ $data['totalKatalog'] ?? 0 }} barang tersedia untuk dipinjam</p>
                        </div>
                        <a href="{{ route('katalog.index') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition">
                            Lihat Semua →
                        </a>
                    </div>
                    <div class="p-4">
                        @if (isset($data['katalogBarang']) && $data['katalogBarang']->count() > 0)
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach ($data['katalogBarang'] as $item)
                                    <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                                        {{-- Foto --}}
                                        <div class="aspect-square bg-gray-200 relative">
                                            @if ($item->foto)
                                                <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama_barang }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            <span class="absolute top-2 right-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $item->available_count > 5 ? 'bg-green-500' : 'bg-yellow-500' }} text-white">
                                                {{ $item->available_count }}
                                            </span>
                                        </div>
                                        {{-- Content --}}
                                        <div class="p-3">
                                            <p class="text-xs text-blue-600 font-medium">{{ $item->kategori->nama_kategori ?? '-' }}</p>
                                            <h5 class="font-medium text-gray-900 text-sm line-clamp-2 mt-1">{{ $item->nama_barang }}</h5>
                                            <p class="text-xs text-gray-500 mt-1">{{ $item->lokasi->nama_lokasi ?? '-' }}</p>
                                            <a href="{{ route('peminjaman.create', ['sarpras_id' => $item->id]) }}" 
                                               class="mt-3 block w-full text-center px-3 py-2 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition">
                                                Pinjam
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p>Tidak ada barang tersedia saat ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- RIWAYAT PEMINJAMAN - Langsung Ditampilkan --}}
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                        <h4 class="font-semibold text-gray-800">📋 Riwayat Peminjaman Saya</h4>
                        <a href="{{ route('peminjaman.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Lihat Semua →
                        </a>
                    </div>
                    <div class="p-4">
                        @if (isset($data['riwayatPeminjaman']) && $data['riwayatPeminjaman']->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                                            <th class="pb-3">Barang</th>
                                            <th class="pb-3">Jumlah</th>
                                            <th class="pb-3">Tanggal</th>
                                            <th class="pb-3 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($data['riwayatPeminjaman'] as $pinjam)
                                            <tr class="hover:bg-gray-50">
                                                <td class="py-3">
                                                    @if ($pinjam->details->count() > 0)
                                                        @php $firstDetail = $pinjam->details->first(); @endphp
                                                        <p class="font-medium text-gray-800 text-sm">{{ $firstDetail->sarprasUnit->sarpras->nama_barang ?? '-' }}</p>
                                                        <p class="text-xs text-gray-500">{{ $firstDetail->sarprasUnit->kode_unit ?? '-' }}</p>
                                                    @else
                                                        <p class="text-gray-400">-</p>
                                                    @endif
                                                </td>
                                                <td class="py-3 text-sm font-semibold text-gray-700">{{ $pinjam->details->count() }}</td>
                                                <td class="py-3 text-xs text-gray-500">
                                                    <p>{{ $pinjam->tgl_pinjam?->format('d M Y') }}</p>
                                                    <p class="text-gray-400">s/d {{ $pinjam->tgl_kembali_rencana?->format('d M Y') }}</p>
                                                </td>
                                                <td class="py-3 text-center">
                                                    @switch($pinjam->status)
                                                        @case('menunggu')
                                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">⏳ Menunggu</span>
                                                            @break
                                                        @case('disetujui')
                                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">✅ Dipinjam</span>
                                                            @break
                                                        @case('selesai')
                                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">📦 Selesai</span>
                                                            @break
                                                        @case('ditolak')
                                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">❌ Ditolak</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p>Belum ada riwayat peminjaman</p>
                                <a href="{{ route('katalog.index') }}" class="text-blue-600 hover:underline text-sm mt-2 inline-block">Mulai pinjam barang →</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Recent Activity (Hanya untuk Admin & Petugas) --}}
            @if (auth()->user()->role !== 'peminjam')
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
                    <div class="max-h-64 overflow-y-auto p-4 custom-scrollbar">
                        @if (isset($data['recentPeminjaman']) && $data['recentPeminjaman']->count() > 0)
                            <div class="space-y-3">
                                @foreach ($data['recentPeminjaman'] as $pinjam)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            @php
                                                $unitNames = $pinjam->details->take(2)->map(fn($d) => $d->sarprasUnit->kode_unit ?? '-')->join(', ');
                                                $moreCount = $pinjam->details->count() > 2 ? ' +' . ($pinjam->details->count() - 2) : '';
                                            @endphp
                                            <p class="font-medium text-gray-800">{{ $unitNames }}{{ $moreCount }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $pinjam->user->name ?? '-' }} •
                                                {{ $pinjam->details->count() }} unit • {{ $pinjam->created_at->diffForHumans() }}
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
            @endif
        </div>
    </div>

    {{-- Chart.js for Admin Dashboard --}}
    @if (auth()->user()->role === 'admin')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Top 5 Barang Chart
                const top5Ctx = document.getElementById('top5Chart');
                if (top5Ctx) {
                    // Create gradient
                    const gradient = top5Ctx.getContext('2d').createLinearGradient(0, 0, 400, 0);
                    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)'); // Blue 500
                    gradient.addColorStop(1, 'rgba(147, 51, 234, 0.8)'); // Purple 600

                    new Chart(top5Ctx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(($data['top5Barang'] ?? collect())->pluck('nama_barang')->map(fn($n) => strlen($n) > 20 ? substr($n, 0, 20) . '...' : $n)) !!},
                            datasets: [{
                                label: 'Total Dipinjam',
                                data: {!! json_encode(($data['top5Barang'] ?? collect())->pluck('total_dipinjam')) !!},
                                backgroundColor: gradient,
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1,
                                borderRadius: 8,
                                barPercentage: 0.6,
                                categoryPercentage: 0.8
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                    padding: 12,
                                    titleFont: { size: 13 },
                                    bodyFont: { size: 14, weight: 'bold' },
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            return 'Dipinjam: ' + context.raw + ' kali';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { 
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)',
                                        borderDash: [5, 5]
                                    },
                                    ticks: { font: { size: 11 } }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { font: { size: 12, weight: '500' } }
                                }
                            },
                            animation: {
                                duration: 1500,
                                easing: 'easeOutQuart'
                            }
                        }
                    });
                }

                // Status Peminjaman Chart
                const statusCtx = document.getElementById('statusChart');
                if (statusCtx) {
                    new Chart(statusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Menunggu', 'Disetujui', 'Selesai'],
                            datasets: [{
                                data: [
                                    {{ $data['peminjamanMenunggu'] ?? 0 }},
                                    {{ $data['peminjamanDisetujui'] ?? 0 }},
                                    {{ $data['peminjamanSelesai'] ?? 0 }}
                                ],
                                backgroundColor: [
                                    '#FBBF24', // Amber 400
                                    '#34D399', // Emerald 400
                                    '#60A5FA'  // Blue 400
                                ],
                                hoverBackgroundColor: [
                                    '#F59E0B',
                                    '#10B981',
                                    '#3B82F6'
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: { size: 12 }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                    padding: 12,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            let value = context.raw || 0;
                                            let total = context.chart._metasets[context.datasetIndex].total;
                                            let percentage = Math.round((value / total) * 100) + '%';
                                            return label + value + ' (' + percentage + ')';
                                        }
                                    }
                                }
                            },
                            animation: {
                                animateScale: true,
                                animateRotate: true
                            }
                        }
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
