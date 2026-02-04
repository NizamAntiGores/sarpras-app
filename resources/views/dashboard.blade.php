<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Overview</h1>
                <p class="text-xs text-slate-500 mt-0.5 font-medium">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-xs font-bold text-slate-600">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- ========================================== --}}
    {{-- DASHBOARD ADMIN --}}
    {{-- ========================================== --}}
    @if (auth()->user()->role === 'admin')
        <div class="p-8 space-y-6 max-w-[1600px] mx-auto bg-slate-50 min-h-screen">
            {{-- Quick Stats Cards - Row 1 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                {{-- Total Users --}}
                <a href="{{ route('users.index') }}" class="group">
                    <div class="bg-white border border-slate-200 rounded-xl p-6 transition-all hover:border-indigo-300 hover:shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-lg border border-indigo-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded uppercase tracking-wide">Total</span>
                        </div>
                        <h3 class="text-3xl font-extrabold text-slate-900 mb-1">{{ $data['totalUsers'] ?? 0 }}</h3>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengguna</p>
                    </div>
                </a>

                {{-- Total Barang --}}
                <a href="{{ route('sarpras.index') }}" class="group">
                    <div class="bg-white border border-slate-200 rounded-xl p-6 transition-all hover:border-blue-300 hover:shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg border border-blue-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded uppercase tracking-wide">Jenis</span>
                        </div>
                        <h3 class="text-3xl font-extrabold text-slate-900 mb-1">{{ $data['totalJenisSarpras'] ?? 0 }}</h3>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jenis Barang</p>
                    </div>
                </a>

                {{-- Menunggu --}}
                <a href="{{ route('peminjaman.index', ['status' => 'menunggu']) }}" class="group">
                    <div class="bg-white border border-slate-200 rounded-xl p-6 transition-all hover:border-orange-300 hover:shadow-sm relative">
                        @if(($data['peminjamanMenunggu'] ?? 0) > 0)
                            <div class="absolute top-4 right-4">
                                <span class="flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-orange-500"></span>
                                </span>
                            </div>
                        @endif
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2.5 bg-orange-50 text-orange-600 rounded-lg border border-orange-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-orange-700 bg-orange-50 px-2 py-0.5 rounded uppercase tracking-wide">Pending</span>
                        </div>
                        <h3 class="text-3xl font-extrabold text-slate-900 mb-1">{{ $data['peminjamanMenunggu'] ?? 0 }}</h3>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Proses</p>
                    </div>
                </a>

                {{-- Total Transaksi --}}
                <a href="{{ route('peminjaman.index') }}" class="group">
                    <div class="bg-white border border-slate-200 rounded-xl p-6 transition-all hover:border-purple-300 hover:shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2.5 bg-purple-50 text-purple-600 rounded-lg border border-purple-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded uppercase tracking-wide">Volume</span>
                        </div>
                        <h3 class="text-3xl font-extrabold text-slate-900 mb-1">{{ number_format($data['totalPeminjaman'] ?? 0) }}</h3>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Transaksi</p>
                    </div>
                </a>
            </div>

            {{-- User Breakdown - Row 2 --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('users.index', ['role' => 'admin']) }}" class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-4 hover:border-rose-200 hover:shadow-sm transition-all">
                    <div class="p-3 bg-rose-50 text-rose-600 rounded-lg border border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900">{{ $data['totalAdmin'] ?? 0 }}</h4>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Admin</p>
                    </div>
                </a>

                <a href="{{ route('users.index', ['role' => 'petugas']) }}" class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-4 hover:border-cyan-200 hover:shadow-sm transition-all">
                    <div class="p-3 bg-cyan-50 text-cyan-600 rounded-lg border border-cyan-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900">{{ $data['totalPetugas'] ?? 0 }}</h4>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Petugas</p>
                    </div>
                </a>

                <a href="{{ route('users.index', ['role' => 'peminjam']) }}" class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-4 hover:border-emerald-200 hover:shadow-sm transition-all">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900">{{ $data['totalPeminjam'] ?? 0 }}</h4>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Siswa/Guru</p>
                    </div>
                </a>
            </div>

            {{-- Alerts Section - Row 3 --}}
            @if (($data['stokHabis'] ?? collect())->count() > 0 || ($data['stokMenipis'] ?? collect())->count() > 0)
                <div class="space-y-3">
                    @if (($data['stokHabis'] ?? collect())->count() > 0)
                        <div class="bg-white border border-slate-200 border-l-4 border-l-red-500 rounded-lg p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="p-2 bg-red-50 text-red-600 rounded-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 mb-1">Stok Habis</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($data['stokHabis']->take(5) as $item)
                                            <a href="{{ route('sarpras.units.create', $item->id) }}" class="px-2 py-0.5 bg-slate-50 border border-slate-200 rounded text-[10px] font-bold text-slate-600 hover:bg-red-50 hover:border-red-200 hover:text-red-700 transition-colors">
                                                {{ Str::limit($item->nama_barang, 25) }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold bg-red-50 text-red-700 border border-red-100 px-3 py-1 rounded-full uppercase tracking-wider">
                                {{ $data['stokHabis']->count() }} Item
                            </span>
                        </div>
                    @endif

                    @if (($data['stokMenipis'] ?? collect())->count() > 0)
                        <div class="bg-white border border-slate-200 border-l-4 border-l-amber-500 rounded-lg p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="p-2 bg-amber-50 text-amber-600 rounded-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 mb-1">Stok Menipis</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($data['stokMenipis']->take(5) as $item)
                                            <a href="{{ route('sarpras.units.create', $item->id) }}" class="px-2 py-0.5 bg-slate-50 border border-slate-200 rounded text-[10px] font-bold text-slate-600 hover:bg-amber-50 hover:border-amber-200 hover:text-amber-700 transition-colors">
                                                {{ Str::limit($item->nama_barang, 20) }} <span class="text-amber-600">({{ $item->available_count }})</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 px-3 py-1 rounded-full uppercase tracking-wider">
                                {{ $data['stokMenipis']->count() }} Item
                            </span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Charts Section - Row 4 --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Trend Chart --}}
                <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Trend Peminjaman</h3>
                            <p class="text-[11px] text-slate-500 font-medium">
                                @if(($data['chartTrend']['filter'] ?? 'weekly') == 'weekly') Aktivitas minggu ini
                                @elseif(($data['chartTrend']['filter'] ?? 'weekly') == 'monthly') Aktivitas bulan ini
                                @else Aktivitas tahun ini @endif
                            </p>
                        </div>
                        <select onchange="window.location.href='?trend_filter='+this.value" class="text-xs font-bold border-slate-200 rounded-lg bg-slate-50 text-slate-600 focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-3">
                            <option value="weekly" {{ ($data['chartTrend']['filter'] ?? 'weekly') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                            <option value="monthly" {{ ($data['chartTrend']['filter'] ?? 'weekly') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            <option value="yearly" {{ ($data['chartTrend']['filter'] ?? 'weekly') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                        </select>
                    </div>
                    <div class="h-64">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                {{-- Status Donut --}}
                <div class="bg-white border border-slate-200 rounded-xl p-8 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Status Peminjaman</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Distribusi saat ini</p>
                    </div>
                    <div class="flex items-center justify-center my-6">
                        <div class="relative w-40 h-40">
                            <canvas id="statusChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-2xl font-black text-slate-900">{{ number_format($data['totalPeminjaman'] ?? 0) }}</span>
                                <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Total Data</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-[10px] font-bold uppercase tracking-wider">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded bg-orange-500"></span>
                            <span class="text-slate-600">Menunggu</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded bg-emerald-500"></span>
                            <span class="text-slate-600">Dipinjam</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded bg-blue-500"></span>
                            <span class="text-slate-600">Selesai</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Section - Row 5 --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-8">
                {{-- Most Borrowed --}}
                <div class="bg-white border border-slate-200 rounded-xl p-8">
                    <h3 class="text-base font-bold text-slate-900 mb-1">Barang Populer</h3>
                    <p class="text-[11px] text-slate-500 font-medium mb-6">Paling sering dipinjam</p>
                    @if(isset($data['top5Barang']) && $data['top5Barang']->count() > 0)
                        <div class="space-y-4">
                            @foreach($data['top5Barang'] as $index => $item)
                                <div class="flex items-center justify-between group cursor-pointer border-b border-slate-50 pb-3 last:border-0">
                                    <div class="flex items-center gap-4">
                                        <span class="w-7 h-7 flex items-center justify-center {{ $index == 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-500' }} rounded text-[11px] font-black">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $item->nama_barang }}</p>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $item->kode_barang }}</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-slate-50 border border-slate-200 rounded text-[10px] font-black text-slate-700">{{ $item->total_dipinjam }}x</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-slate-400 py-8 text-sm">Belum ada data</p>
                    @endif
                </div>

                {{-- Recent Activity --}}
                <div class="bg-white border border-slate-200 rounded-xl p-8">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-base font-bold text-slate-900">Aktivitas Terbaru</h3>
                        <a href="{{ route('peminjaman.index') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-wider">Lihat Semua</a>
                    </div>
                    <p class="text-[11px] text-slate-500 font-medium mb-6">Peminjaman terkini</p>
                    @if(isset($data['recentPeminjaman']) && $data['recentPeminjaman']->count() > 0)
                        <div class="space-y-6">
                            @foreach($data['recentPeminjaman']->take(5) as $pinjam)
                                <a href="{{ route('peminjaman.show', $pinjam) }}" class="flex items-start justify-between relative pl-6 border-l-2 border-slate-100 pb-2 hover:border-l-indigo-300 transition-colors">
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full border-4 border-white 
                                        {{ $pinjam->status === 'menunggu' ? 'bg-orange-500' : '' }}
                                        {{ $pinjam->status === 'disetujui' ? 'bg-emerald-500' : '' }}
                                        {{ $pinjam->status === 'selesai' ? 'bg-blue-500' : '' }}
                                        {{ $pinjam->status === 'ditolak' ? 'bg-red-500' : '' }}
                                    "></div>
                                    <div class="flex items-start gap-3 -mt-1">
                                        <div class="w-9 h-9 rounded bg-slate-100 flex items-center justify-center text-slate-600 text-xs font-black border border-slate-200">
                                            {{ strtoupper(substr($pinjam->user->name ?? '?', 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 leading-none">{{ $pinjam->user->name ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-500 mt-1.5 font-medium">
                                                {{ $pinjam->details->count() }} unit • {{ $pinjam->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border
                                        {{ $pinjam->status === 'menunggu' ? 'bg-orange-50 text-orange-700 border-orange-100' : '' }}
                                        {{ $pinjam->status === 'disetujui' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : '' }}
                                        {{ $pinjam->status === 'selesai' ? 'bg-blue-50 text-blue-700 border-blue-100' : '' }}
                                        {{ $pinjam->status === 'ditolak' ? 'bg-red-50 text-red-700 border-red-100' : '' }}
                                    ">{{ ucfirst($pinjam->status) }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-slate-400 py-8">
                            <p class="text-sm">Belum ada aktivitas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chart.js Scripts --}}
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
                    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.1)');
                    gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

                    new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($data['chartTrend']['labels'] ?? []) !!},
                            datasets: [{
                                label: 'Peminjaman',
                                data: {!! json_encode($data['chartTrend']['data'] ?? []) !!},
                                borderColor: '#4F46E5',
                                backgroundColor: gradient,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#4F46E5',
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
                                backgroundColor: ['#f97316', '#10b981', '#3b82f6'],
                                borderWidth: 0,
                                hoverOffset: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            cutout: '75%',
                            plugins: {
                                legend: { display: false }
                            }
                        }
                    });
                }
            });
        </script>

    {{-- ========================================== --}}
    {{-- DASHBOARD PETUGAS --}}
    {{-- ========================================== --}}
    @elseif (auth()->user()->role === 'petugas')
        <div class="p-8 space-y-6 max-w-[1600px] mx-auto bg-slate-50 min-h-screen">
            {{-- Quick Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('sarpras.index') }}" class="bg-white border border-slate-200 rounded-xl p-6 hover:border-emerald-300 hover:shadow-sm transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-slate-900">{{ number_format($data['tersedia'] ?? 0) }}</p>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">Siap Pakai</p>
                </a>

                <a href="{{ route('maintenance.index') }}" class="bg-white border border-slate-200 rounded-xl p-6 hover:border-rose-300 hover:shadow-sm transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-rose-50 text-rose-600 rounded-lg border border-rose-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-slate-900">{{ number_format($data['maintenance'] ?? 0) }}</p>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">Maintenance</p>
                </a>

                <a href="{{ route('peminjaman.index', ['status' => 'disetujui']) }}" class="bg-white border border-slate-200 rounded-xl p-6 hover:border-amber-300 hover:shadow-sm transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-amber-50 text-amber-600 rounded-lg border border-amber-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-slate-900">{{ number_format($data['dipinjam'] ?? 0) }}</p>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">Dipinjam</p>
                </a>

                <a href="{{ route('sarpras.index') }}" class="bg-white border border-slate-200 rounded-xl p-6 hover:border-blue-300 hover:shadow-sm transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg border border-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-slate-900">{{ number_format($data['totalUnit'] ?? 0) }}</p>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">Total Unit</p>
                </a>
            </div>

            {{-- Alert for Today's Pending --}}
            @if (($data['peminjamanMenungguHariIni'] ?? 0) > 0)
                <div class="bg-white border border-slate-200 border-l-4 border-l-amber-500 rounded-lg p-4">
                    <div class="flex items-center gap-4">
                        <div class="p-2.5 bg-amber-50 text-amber-600 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-slate-900">Ada {{ $data['peminjamanMenungguHariIni'] }} pengajuan baru hari ini</p>
                            <p class="text-sm text-slate-500">Segera proses untuk memberikan pelayanan terbaik</p>
                        </div>
                        <a href="{{ route('peminjaman.index', ['status' => 'menunggu']) }}" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-bold hover:bg-amber-600 transition">
                            Proses Sekarang
                        </a>
                    </div>
                </div>
            @endif

            {{-- Quick Stats Mini --}}
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                        <span class="text-sm text-slate-600 font-medium">Perlu Verifikasi</span>
                    </div>
                    <p class="text-4xl font-extrabold text-slate-900">{{ $data['peminjamanMenunggu'] ?? 0 }}</p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                        <span class="text-sm text-slate-600 font-medium">Sedang Dipinjam</span>
                    </div>
                    <p class="text-4xl font-extrabold text-slate-900">{{ $data['peminjamanDisetujui'] ?? 0 }}</p>
                </div>
            </div>

            {{-- Recent Pending Requests --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h4 class="font-bold text-slate-900">Pengajuan Menunggu</h4>
                </div>
                <div class="divide-y divide-slate-50">
                    @if(isset($data['recentPeminjaman']) && $data['recentPeminjaman']->count() > 0)
                        @foreach($data['recentPeminjaman'] as $pinjam)
                            <a href="{{ route('peminjaman.edit', $pinjam) }}" class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center">
                                        <span class="text-sm font-bold text-amber-600">{{ strtoupper(substr($pinjam->user->name ?? '?', 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $pinjam->user->name ?? '-' }}</p>
                                        <p class="text-sm text-slate-500">{{ $pinjam->details->count() }} unit • {{ $pinjam->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-100 text-xs font-bold rounded-lg">
                                    Proses →
                                </span>
                            </a>
                        @endforeach
                    @else
                        <div class="p-8 text-center text-slate-400">
                            <p>Tidak ada pengajuan menunggu</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    {{-- ========================================== --}}
    {{-- DASHBOARD PEMINJAM/SISWA/GURU --}}
    {{-- ========================================== --}}
    @else
        <div class="p-8 space-y-6 max-w-[1600px] mx-auto bg-slate-50 min-h-screen">
            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <p class="text-sm text-slate-500 mb-1 font-medium">Total Peminjaman</p>
                    <p class="text-3xl font-extrabold text-slate-900">{{ $data['totalPeminjamanSaya'] ?? 0 }}</p>
                </div>
                <div class="bg-white border-l-4 border-l-amber-400 border border-slate-200 rounded-xl p-5">
                    <p class="text-sm text-slate-500 mb-1 font-medium">Menunggu</p>
                    <p class="text-3xl font-extrabold text-amber-600">{{ $data['peminjamanMenunggu'] ?? 0 }}</p>
                </div>
                <div class="bg-white border-l-4 border-l-emerald-400 border border-slate-200 rounded-xl p-5">
                    <p class="text-sm text-slate-500 mb-1 font-medium">Dipinjam</p>
                    <p class="text-3xl font-extrabold text-emerald-600">{{ $data['peminjamanDisetujui'] ?? 0 }}</p>
                </div>
                <div class="bg-white border-l-4 border-l-blue-400 border border-slate-200 rounded-xl p-5">
                    <p class="text-sm text-slate-500 mb-1 font-medium">Selesai</p>
                    <p class="text-3xl font-extrabold text-blue-600">{{ $data['peminjamanSelesai'] ?? 0 }}</p>
                </div>
            </div>

            {{-- Quick Action --}}
            <a href="{{ route('katalog.index') }}" class="block bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-xl p-6 text-white hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-1">Pinjam Barang</h3>
                        <p class="text-indigo-200">{{ $data['totalKatalog'] ?? 0 }} barang tersedia</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Katalog Preview --}}
            @if (isset($data['katalogBarang']) && $data['katalogBarang']->count() > 0)
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-900">Barang Tersedia</h3>
                        <a href="{{ route('katalog.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-bold">Lihat semua →</a>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($data['katalogBarang'] as $item)
                            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 group">
                                <div class="aspect-square bg-slate-100 relative overflow-hidden">
                                    @if ($item->foto)
                                        <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama_barang }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="absolute top-2 right-2 px-2 py-0.5 text-xs font-bold rounded-full {{ $item->available_count > 5 ? 'bg-emerald-500' : 'bg-amber-500' }} text-white">
                                        {{ $item->available_count }}
                                    </span>
                                </div>
                                <div class="p-3">
                                    <p class="text-xs text-indigo-600 font-bold">{{ $item->kategori->nama_kategori ?? '-' }}</p>
                                    <h5 class="font-bold text-slate-900 text-sm line-clamp-2 mt-1">{{ $item->nama_barang }}</h5>
                                    <a href="{{ route('peminjaman.create', ['sarpras_id' => $item->id]) }}" class="mt-3 block w-full text-center px-3 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition">
                                        Pinjam
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Riwayat Peminjaman --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h4 class="font-bold text-slate-900">Riwayat Peminjaman</h4>
                    <a href="{{ route('peminjaman.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-bold">Lihat semua →</a>
                </div>
                @if (isset($data['riwayatPeminjaman']) && $data['riwayatPeminjaman']->count() > 0)
                    <div class="divide-y divide-slate-50">
                        @foreach ($data['riwayatPeminjaman'] as $pinjam)
                            <a href="{{ route('peminjaman.show', $pinjam) }}" class="p-4 flex items-center gap-4 hover:bg-slate-50 transition">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if ($pinjam->details->count() > 0)
                                        @php $firstDetail = $pinjam->details->first(); @endphp
                                        <p class="font-bold text-slate-900 truncate">{{ $firstDetail->sarprasUnit->sarpras->nama_barang ?? '-' }}</p>
                                        <p class="text-xs text-slate-400">{{ $pinjam->details->count() }} unit • {{ $pinjam->tgl_pinjam?->format('d M Y') }}</p>
                                    @endif
                                </div>
                                <span class="px-2 py-1 text-xs font-bold rounded-lg border
                                    {{ $pinjam->status === 'menunggu' ? 'bg-amber-50 text-amber-700 border-amber-100' : '' }}
                                    {{ $pinjam->status === 'disetujui' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : '' }}
                                    {{ $pinjam->status === 'selesai' ? 'bg-blue-50 text-blue-700 border-blue-100' : '' }}
                                    {{ $pinjam->status === 'ditolak' ? 'bg-red-50 text-red-700 border-red-100' : '' }}
                                ">{{ ucfirst($pinjam->status) }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-slate-500 mb-2">Belum ada riwayat peminjaman</p>
                        <a href="{{ route('katalog.index') }}" class="text-indigo-600 hover:underline text-sm font-bold">Mulai pinjam barang →</a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-app-layout>
