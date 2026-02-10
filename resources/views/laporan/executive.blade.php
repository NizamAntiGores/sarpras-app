<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Executive Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- High-Level Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-indigo-600 rounded-lg shadow-lg p-6 text-white transform transition hover:scale-105">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-500 rounded-full bg-opacity-75">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold uppercase tracking-wider opacity-75">Total Aset</h4>
                            <span class="text-3xl font-bold">{{ number_format($totalAssets) }}</span>
                            <span class="text-sm opacity-75 block">Unit Terdaftar</span>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-500 rounded-lg shadow-lg p-6 text-white transform transition hover:scale-105">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-400 rounded-full bg-opacity-75">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold uppercase tracking-wider opacity-75">Sedang Dipinjam</h4>
                            <span class="text-3xl font-bold">{{ number_format($activeLoans) }}</span>
                            <span class="text-sm opacity-75 block">Transaksi Aktif</span>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-500 rounded-lg shadow-lg p-6 text-white transform transition hover:scale-105">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-400 rounded-full bg-opacity-75">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold uppercase tracking-wider opacity-75">Pengaduan</h4>
                            <span class="text-3xl font-bold">{{ $pendingIssues }}</span>
                            <span class="text-sm opacity-75 block">Butuh Tindak Lanjut</span>
                        </div>
                    </div>
                </div>

                <div class="bg-red-500 rounded-lg shadow-lg p-6 text-white transform transition hover:scale-105">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-400 rounded-full bg-opacity-75">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold uppercase tracking-wider opacity-75">Aset Kritis</h4>
                            <span class="text-3xl font-bold">{{ $criticalAssets }}</span>
                            <span class="text-sm opacity-75 block">Rusak Berat</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Dashboard Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left Column: Charts --}}
                <div class="lg:col-span-2 space-y-8">
                    {{-- Asset Condition Chart --}}
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Distribusi Kondisi Aset</h3>
                        <div class="relative h-64">
                            {{-- Placeholder for Chart --}}
                            <div class="flex items-end justify-around h-full space-x-2 px-6">
                                @foreach($conditionStats as $condition => $count)
                                    <div class="flex flex-col items-center group w-full">
                                        <div class="w-full bg-indigo-100 rounded-t-lg relative transition-all duration-500 group-hover:bg-indigo-200" style="height: {{ ($count / max(1, array_sum($conditionStats))) * 100 }}%">
                                            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition">
                                                {{ $count }} Unit
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-600 mt-2 font-medium capitalize text-center">{{ str_replace('_', ' ', $condition) }}</span>
                                        <span class="text-xs text-gray-400">{{ round(($count / max(1, array_sum($conditionStats))) * 100) }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Borrowing Trend --}}
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Tren Peminjaman (6 Bulan Terakhir)</h3>
                         <div class="relative h-64 border-l border-b border-gray-200">
                            <div class="flex items-end justify-around h-full">
                                 @foreach($monthlyTrend as $trend)
                                    <div class="flex flex-col items-center group">
                                        <div class="w-12 bg-blue-500 rounded-t shadow-md transition-all duration-300 group-hover:bg-blue-600" style="height: {{ min(100, $trend->count * 10) }}%"></div> {{-- Simple scaling --}}
                                        <span class="text-xs text-gray-600 mt-2 font-medium">{{ \Carbon\Carbon::parse($trend->month_year)->format('M Y') }}</span>
                                        <span class="text-xs text-blue-500 font-bold -mt-6 bg-white px-1 rounded shadow-sm relative z-10">{{ $trend->count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Side Stats --}}
                <div class="lg:col-span-1 space-y-8">
                    {{-- Category Breakdown --}}
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Sebaran Kategori</h3>
                        <div class="space-y-4">
                            @foreach($categoryStats as $stat)
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">{{ $stat['name'] }}</span>
                                        <span class="text-gray-500">{{ $stat['count'] }} Unit</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ ($stat['count'] / max(1, $totalAssets)) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Recent Critical Logs --}}
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center justify-between">
                            <span>Aktivitas Kritis Terbaru</span>
                            <span class="text-xs font-normal text-red-500 bg-red-50 px-2 py-1 rounded-full">Last 5</span>
                        </h3>
                        <div class="space-y-4">
                            @forelse($recentCriticalLogs as $log)
                                <div class="flex items-start space-x-3 text-sm">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <span class="w-2 h-2 bg-red-500 rounded-full block"></span>
                                    </div>
                                    <div>
                                        <p class="text-gray-800 font-medium">{{ $log->description }}</p>
                                        <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }} by {{ $log->user->name ?? 'System' }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 italic text-center py-4">Tidak ada aktivitas kritis.</p>
                            @endforelse
                        </div>
                         <div class="mt-4 text-center">
                            <a href="{{ route('activity-logs.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Lihat Semua Log &rarr;</a>
                        </div>
                    </div>

                    {{-- Quick Links --}}
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg shadow-lg p-6 text-white">
                        <h3 class="font-bold mb-4 text-lg">Aksi Cepat</h3>
                        <div class="space-y-3">
                             <a href="{{ route('sarpras.create') }}" class="block w-full text-center py-2 bg-white bg-opacity-10 hover:bg-opacity-20 rounded transition text-sm font-medium">
                                + Tambah Aset Baru
                            </a>
                            <a href="{{ route('laporan.asset-health') }}" class="block w-full text-center py-2 bg-white bg-opacity-10 hover:bg-opacity-20 rounded transition text-sm font-medium">
                                Lihat Laporan Kesehatan
                            </a>
                             <a href="{{ route('maintenance.index') }}" class="block w-full text-center py-2 bg-white bg-opacity-10 hover:bg-opacity-20 rounded transition text-sm font-medium">
                                Jadwal Maintenance
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
