<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ========================================== --}}
            {{-- DASHBOARD ADMIN --}}
            {{-- ========================================== --}}
            @if (auth()->user()->role === 'admin')

                {{-- Quick Stats Row - Colorful Gradient Cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                    {{-- Users --}}
                    <a href="{{ route('users.index') }}" class="group">
                        <div class="bg-gradient-to-br from-slate-700 via-slate-800 to-slate-900 rounded-2xl p-5 text-white hover:shadow-xl hover:shadow-slate-500/20 transition-all duration-300 hover:-translate-y-1">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-slate-300 bg-white/10 px-2 py-1 rounded-full">Total</span>
                            </div>
                            <p class="text-4xl font-bold">{{ $data['totalUsers'] ?? 0 }}</p>
                            <p class="text-sm text-slate-300 mt-1">Pengguna</p>
                        </div>
                    </a>

                    {{-- Sarpras --}}
                    <a href="{{ route('sarpras.index') }}" class="group">
                        <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 rounded-2xl p-5 text-white hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-300 hover:-translate-y-1">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-blue-100 bg-white/10 px-2 py-1 rounded-full">Jenis</span>
                            </div>
                            <p class="text-4xl font-bold">{{ $data['totalJenisSarpras'] ?? 0 }}</p>
                            <p class="text-sm text-blue-100 mt-1">Barang</p>
                        </div>
                    </a>

                    {{-- Pending --}}
                    <a href="{{ route('peminjaman.index', ['status' => 'menunggu']) }}" class="group">
                        <div class="bg-gradient-to-br from-amber-400 via-orange-500 to-red-500 rounded-2xl p-5 text-white hover:shadow-xl hover:shadow-orange-500/30 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                            @if(($data['peminjamanMenunggu'] ?? 0) > 0)
                                <div class="absolute top-3 right-3">
                                    <span class="flex h-3 w-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                                    </span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-4xl font-bold">{{ $data['peminjamanMenunggu'] ?? 0 }}</p>
                            <p class="text-sm text-orange-100 mt-1">Menunggu</p>
                        </div>
                    </a>

                    {{-- Total Transaksi --}}
                    <a href="{{ route('peminjaman.index') }}" class="group">
                        <div class="bg-gradient-to-br from-violet-500 via-purple-600 to-fuchsia-700 rounded-2xl p-5 text-white hover:shadow-xl hover:shadow-purple-500/30 transition-all duration-300 hover:-translate-y-1">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-purple-100 bg-white/10 px-2 py-1 rounded-full">All Time</span>
                            </div>
                            <p class="text-4xl font-bold">{{ $data['totalPeminjaman'] ?? 0 }}</p>
                            <p class="text-sm text-purple-100 mt-1">Transaksi</p>
                        </div>
                    </a>
                </div>

                {{-- User Breakdown --}}
                <div class="grid grid-cols-3 gap-4 mb-8">
                    <a href="{{ route('users.index', ['role' => 'admin']) }}" class="bg-gradient-to-br from-rose-50 to-white rounded-xl p-4 border border-rose-100 hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-rose-700">{{ $data['totalAdmin'] ?? 0 }}</p>
                                <p class="text-xs text-rose-600/70">Admin</p>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('users.index', ['role' => 'petugas']) }}" class="bg-gradient-to-br from-sky-50 to-white rounded-xl p-4 border border-sky-100 hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-sky-700">{{ $data['totalPetugas'] ?? 0 }}</p>
                                <p class="text-xs text-sky-600/70">Petugas</p>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('users.index', ['role' => 'peminjam']) }}" class="bg-gradient-to-br from-emerald-50 to-white rounded-xl p-4 border border-emerald-100 hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-emerald-700">{{ $data['totalPeminjam'] ?? 0 }}</p>
                                <p class="text-xs text-emerald-600/70">Siswa/Guru</p>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Alerts Section --}}
                @if (($data['stokHabis'] ?? collect())->count() > 0 || ($data['stokMenipis'] ?? collect())->count() > 0)
                    <div class="mb-8 space-y-3">
                        @if (($data['stokHabis'] ?? collect())->count() > 0)
                            <div class="bg-red-50/80 backdrop-blur border border-red-200/50 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-semibold text-red-800">Stok Habis</h4>
                                            <span class="text-xs font-medium text-red-600">{{ $data['stokHabis']->count() }} barang</span>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($data['stokHabis']->take(5) as $item)
                                                <a href="{{ route('sarpras.units.create', $item->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg text-xs font-medium text-red-700 hover:bg-red-100 transition border border-red-200">
                                                    {{ Str::limit($item->nama_barang, 20) }}
                                                    <span class="text-red-400">+</span>
                                                </a>
                                            @endforeach
                                            @if($data['stokHabis']->count() > 5)
                                                <a href="{{ route('sarpras.index', ['filter' => 'stok_habis']) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 hover:underline">
                                                    +{{ $data['stokHabis']->count() - 5 }} lainnya →
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (($data['stokMenipis'] ?? collect())->count() > 0)
                            <div class="bg-amber-50/80 backdrop-blur border border-amber-200/50 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-semibold text-amber-800">Stok Menipis</h4>
                                            <span class="text-xs font-medium text-amber-600">{{ $data['stokMenipis']->count() }} barang</span>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($data['stokMenipis']->take(5) as $item)
                                                <a href="{{ route('sarpras.units.create', $item->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg text-xs font-medium text-amber-700 hover:bg-amber-100 transition border border-amber-200">
                                                    {{ Str::limit($item->nama_barang, 20) }}
                                                    <span class="text-amber-500 font-bold">{{ $item->available_count }}</span>
                                                </a>
                                            @endforeach
                                            @if($data['stokMenipis']->count() > 5)
                                                <a href="{{ route('sarpras.index', ['filter' => 'stok_menipis']) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-amber-600 hover:underline">
                                                    +{{ $data['stokMenipis']->count() - 5 }} lainnya →
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Charts Section --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    {{-- Trend Chart (Larger) --}}
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-5 border-b border-gray-50 flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-900">Trend Peminjaman</h4>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @if(($data['chartTrend']['filter'] ?? 'weekly') == 'weekly') Minggu ini
                                    @elseif(($data['chartTrend']['filter'] ?? 'weekly') == 'monthly') Bulan ini
                                    @else Tahun ini @endif
                                </p>
                            </div>
                            <select onchange="window.location.href='?trend_filter='+this.value" class="text-xs border-0 bg-gray-50 rounded-lg py-1.5 pl-3 pr-8 text-gray-600 focus:ring-2 focus:ring-blue-100">
                                <option value="weekly" {{ ($data['chartTrend']['filter'] ?? 'weekly') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                <option value="monthly" {{ ($data['chartTrend']['filter'] ?? 'weekly') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                <option value="yearly" {{ ($data['chartTrend']['filter'] ?? 'weekly') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                            </select>
                        </div>
                        <div class="p-5 h-72">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>

                    {{-- Status Donut --}}
                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-5 border-b border-gray-50">
                            <h4 class="font-semibold text-gray-900">Status Peminjaman</h4>
                            <p class="text-xs text-gray-400 mt-0.5">Distribusi saat ini</p>
                        </div>
                        <div class="p-5 h-72 flex items-center justify-center">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Top 5 & Recent Activity --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Top 5 Barang --}}
                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-5 border-b border-gray-50">
                            <h4 class="font-semibold text-gray-900">Barang Populer</h4>
                            <p class="text-xs text-gray-400 mt-0.5">Paling sering dipinjam</p>
                        </div>
                        <div class="p-5">
                            @if(isset($data['top5Barang']) && $data['top5Barang']->count() > 0)
                                <div class="space-y-3">
                                    @foreach($data['top5Barang'] as $index => $item)
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-full bg-gradient-to-br {{ $index == 0 ? 'from-amber-400 to-orange-500' : ($index == 1 ? 'from-gray-300 to-gray-400' : ($index == 2 ? 'from-orange-300 to-orange-400' : 'from-gray-100 to-gray-200')) }} flex items-center justify-center text-xs font-bold {{ $index < 3 ? 'text-white' : 'text-gray-600' }}">
                                                {{ $index + 1 }}
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $item->nama_barang }}</p>
                                                <p class="text-xs text-gray-400">{{ $item->kode_barang }}</p>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-700">{{ $item->total_dipinjam }}x</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center text-gray-400 py-8">Belum ada data</p>
                            @endif
                        </div>
                    </div>

                    {{-- Recent Activity --}}
                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-5 border-b border-gray-50 flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-900">Aktivitas Terbaru</h4>
                                <p class="text-xs text-gray-400 mt-0.5">Peminjaman terkini</p>
                            </div>
                            <a href="{{ route('peminjaman.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Lihat semua →</a>
                        </div>
                        <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
                            @if(isset($data['recentPeminjaman']) && $data['recentPeminjaman']->count() > 0)
                                @foreach($data['recentPeminjaman']->take(6) as $pinjam)
                                    <a href="{{ route('peminjaman.show', $pinjam) }}" class="p-4 flex items-center gap-3 hover:bg-gray-50 transition">
                                        <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                            <span class="text-sm font-medium text-gray-600">{{ substr($pinjam->user->name ?? '?', 0, 1) }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $pinjam->user->name ?? '-' }}</p>
                                            <p class="text-xs text-gray-400">{{ $pinjam->details->count() }} unit • {{ $pinjam->created_at->diffForHumans() }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $pinjam->status === 'menunggu' ? 'bg-amber-100 text-amber-700' : '' }}
                                            {{ $pinjam->status === 'disetujui' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                            {{ $pinjam->status === 'selesai' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $pinjam->status === 'ditolak' ? 'bg-red-100 text-red-700' : '' }}
                                        ">{{ ucfirst($pinjam->status) }}</span>
                                    </a>
                                @endforeach
                            @else
                                <div class="p-8 text-center text-gray-400">
                                    <p>Belum ada aktivitas</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- DASHBOARD PETUGAS --}}
            {{-- ========================================== --}}
            @elseif (auth()->user()->role === 'petugas')
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                    <a href="{{ route('sarpras.index') }}" class="group">
                        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 text-white hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold">{{ number_format($data['tersedia'] ?? 0) }}</p>
                            <p class="text-emerald-100 text-sm mt-1">Siap Pakai</p>
                        </div>
                    </a>

                    <a href="{{ route('maintenance.index') }}" class="group">
                        <div class="bg-gradient-to-br from-rose-500 to-rose-600 rounded-2xl p-5 text-white hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold">{{ number_format($data['maintenance'] ?? 0) }}</p>
                            <p class="text-rose-100 text-sm mt-1">Maintenance</p>
                        </div>
                    </a>

                    <a href="{{ route('peminjaman.index', ['status' => 'disetujui']) }}" class="group">
                        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl p-5 text-white hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold">{{ number_format($data['dipinjam'] ?? 0) }}</p>
                            <p class="text-amber-100 text-sm mt-1">Dipinjam</p>
                        </div>
                    </a>

                    <a href="{{ route('sarpras.index') }}" class="group">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold">{{ number_format($data['totalUnit'] ?? 0) }}</p>
                            <p class="text-blue-100 text-sm mt-1">Total Unit</p>
                        </div>
                    </a>
                </div>

                @if (($data['peminjamanMenungguHariIni'] ?? 0) > 0)
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/50 rounded-xl p-4 mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                                <span class="text-lg">📋</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-amber-800">Ada {{ $data['peminjamanMenungguHariIni'] }} pengajuan baru hari ini</p>
                                <p class="text-sm text-amber-600">Segera proses untuk memberikan pelayanan terbaik</p>
                            </div>
                            <a href="{{ route('peminjaman.index', ['status' => 'menunggu']) }}" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600 transition">
                                Proses Sekarang
                            </a>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-5 mb-8">
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <span class="text-sm text-gray-600">Perlu Verifikasi</span>
                        </div>
                        <p class="text-4xl font-bold text-gray-900">{{ $data['peminjamanMenunggu'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                            <span class="text-sm text-gray-600">Sedang Dipinjam</span>
                        </div>
                        <p class="text-4xl font-bold text-gray-900">{{ $data['peminjamanDisetujui'] ?? 0 }}</p>
                    </div>
                </div>

                {{-- Recent Pending Requests --}}
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-50">
                        <h4 class="font-semibold text-gray-900">Pengajuan Menunggu</h4>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @if(isset($data['recentPeminjaman']) && $data['recentPeminjaman']->count() > 0)
                            @foreach($data['recentPeminjaman'] as $pinjam)
                                <a href="{{ route('peminjaman.edit', $pinjam) }}" class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-amber-600">{{ substr($pinjam->user->name ?? '?', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $pinjam->user->name ?? '-' }}</p>
                                            <p class="text-sm text-gray-500">{{ $pinjam->details->count() }} unit • {{ $pinjam->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1.5 bg-amber-100 text-amber-700 text-xs font-medium rounded-full">
                                        Proses →
                                    </span>
                                </a>
                            @endforeach
                        @else
                            <div class="p-8 text-center text-gray-400">
                                <p>Tidak ada pengajuan menunggu</p>
                            </div>
                        @endif
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- DASHBOARD PEMINJAM/SISWA/GURU --}}
            {{-- ========================================== --}}
            @else
                {{-- Stats Cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <p class="text-sm text-gray-500 mb-1">Total Peminjaman</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $data['totalPeminjamanSaya'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border-l-4 border-l-amber-400 border border-gray-100 p-5">
                        <p class="text-sm text-gray-500 mb-1">Menunggu</p>
                        <p class="text-3xl font-bold text-amber-600">{{ $data['peminjamanMenunggu'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border-l-4 border-l-emerald-400 border border-gray-100 p-5">
                        <p class="text-sm text-gray-500 mb-1">Dipinjam</p>
                        <p class="text-3xl font-bold text-emerald-600">{{ $data['peminjamanDisetujui'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border-l-4 border-l-blue-400 border border-gray-100 p-5">
                        <p class="text-sm text-gray-500 mb-1">Selesai</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $data['peminjamanSelesai'] ?? 0 }}</p>
                    </div>
                </div>

                {{-- Quick Action --}}
                <div class="mb-8">
                    <a href="{{ route('katalog.index') }}" class="block bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold mb-1">Pinjam Barang</h3>
                                <p class="text-blue-100">{{ $data['totalKatalog'] ?? 0 }} barang tersedia</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Katalog Preview --}}
                @if (isset($data['katalogBarang']) && $data['katalogBarang']->count() > 0)
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-900">Barang Tersedia</h3>
                            <a href="{{ route('katalog.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat semua →</a>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach ($data['katalogBarang'] as $item)
                                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                                    <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                        @if ($item->foto)
                                            <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama_barang }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <span class="absolute top-2 right-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $item->available_count > 5 ? 'bg-emerald-500' : 'bg-amber-500' }} text-white">
                                            {{ $item->available_count }}
                                        </span>
                                    </div>
                                    <div class="p-3">
                                        <p class="text-xs text-blue-600 font-medium">{{ $item->kategori->nama_kategori ?? '-' }}</p>
                                        <h5 class="font-medium text-gray-900 text-sm line-clamp-2 mt-1">{{ $item->nama_barang }}</h5>
                                        <a href="{{ route('peminjaman.create', ['sarpras_id' => $item->id]) }}" class="mt-3 block w-full text-center px-3 py-2 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition">
                                            Pinjam
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Riwayat Peminjaman --}}
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-50 flex items-center justify-between">
                        <h4 class="font-semibold text-gray-900">Riwayat Peminjaman</h4>
                        <a href="{{ route('peminjaman.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat semua →</a>
                    </div>
                    @if (isset($data['riwayatPeminjaman']) && $data['riwayatPeminjaman']->count() > 0)
                        <div class="divide-y divide-gray-50">
                            @foreach ($data['riwayatPeminjaman'] as $pinjam)
                                <a href="{{ route('peminjaman.show', $pinjam) }}" class="p-4 flex items-center gap-4 hover:bg-gray-50 transition">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        @if ($pinjam->details->count() > 0)
                                            @php $firstDetail = $pinjam->details->first(); @endphp
                                            <p class="font-medium text-gray-900 truncate">{{ $firstDetail->sarprasUnit->sarpras->nama_barang ?? '-' }}</p>
                                            <p class="text-xs text-gray-400">{{ $pinjam->details->count() }} unit • {{ $pinjam->tgl_pinjam?->format('d M Y') }}</p>
                                        @endif
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $pinjam->status === 'menunggu' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $pinjam->status === 'disetujui' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $pinjam->status === 'selesai' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $pinjam->status === 'ditolak' ? 'bg-red-100 text-red-700' : '' }}
                                    ">{{ ucfirst($pinjam->status) }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 mb-2">Belum ada riwayat peminjaman</p>
                            <a href="{{ route('katalog.index') }}" class="text-blue-600 hover:underline text-sm font-medium">Mulai pinjam barang →</a>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

    {{-- Chart.js for Admin Dashboard --}}
    @if (auth()->user()->role === 'admin')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            Chart.defaults.font.family = "'Inter', 'system-ui', 'sans-serif'";
            Chart.defaults.color = '#94a3b8';
            
            document.addEventListener('DOMContentLoaded', function() {
                // Trend Chart
                const trendCtx = document.getElementById('trendChart');
                if (trendCtx) {
                    const ctx = trendCtx.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 250);
                    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.15)');
                    gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

                    new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($data['chartTrend']['labels'] ?? []) !!},
                            datasets: [{
                                label: 'Peminjaman',
                                data: {!! json_encode($data['chartTrend']['data'] ?? []) !!},
                                borderColor: '#6366f1',
                                backgroundColor: gradient,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#6366f1',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { intersect: false, mode: 'index' },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#1e293b',
                                    padding: 12,
                                    cornerRadius: 8,
                                    titleFont: { size: 13, weight: '600' },
                                    bodyFont: { size: 12 },
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    border: { display: false },
                                    grid: { color: '#f1f5f9', drawBorder: false },
                                    ticks: { padding: 10, font: { size: 11 } }
                                },
                                x: {
                                    border: { display: false },
                                    grid: { display: false },
                                    ticks: { padding: 10, font: { size: 11 } }
                                }
                            }
                        }
                    });
                }

                // Status Doughnut
                const statusCtx = document.getElementById('statusChart');
                if (statusCtx) {
                    new Chart(statusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Menunggu', 'Dipinjam', 'Selesai'],
                            datasets: [{
                                data: [
                                    {{ $data['peminjamanMenunggu'] ?? 0 }},
                                    {{ $data['peminjamanDisetujui'] ?? 0 }},
                                    {{ $data['peminjamanSelesai'] ?? 0 }}
                                ],
                                backgroundColor: ['#f59e0b', '#10b981', '#6366f1'],
                                borderWidth: 0,
                                hoverOffset: 8
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
                                        padding: 16,
                                        font: { size: 11 }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
