<nav class="bg-white border-r border-gray-200 fixed top-0 left-0 h-full w-64 overflow-y-auto z-30 flex flex-col font-sans transition-all duration-300">
    
    <!-- Logo Area -->
    <div class="flex items-center justify-center h-16 border-b border-gray-100 shrink-0 bg-white sticky top-0 z-40">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <x-application-logo class="block h-9 w-auto fill-current text-blue-600" />
            <span class="font-bold text-xl text-gray-800 tracking-tight">SARPRAS</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
        
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 
           {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>

        <!-- Katalog (Peminjam) -->
        @if (auth()->user()->role === 'peminjam')
            <a href="{{ route('katalog.index') }}" 
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 
               {{ request()->routeIs('katalog.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('katalog.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Katalog Barang
            </a>
        @endif

        <!-- Inventaris Group (Admin/Petugas) -->
        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'petugas')
            <div x-data="{ open: {{ (request()->routeIs('sarpras.*') || request()->routeIs('maintenance.*') || request()->routeIs('barang-hilang.*')) ? 'true' : 'false' }} }" class="pt-2">
                <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2.5 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        Inventaris
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open" class="space-y-1 pl-11 pr-2 mt-1" style="display: none;">
                    <a href="{{ route('sarpras.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('sarpras.*') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        Daftar Sarpras
                    </a>
                    <a href="{{ route('maintenance.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('maintenance.*') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        Maintenance
                    </a>
                    <a href="{{ route('barang-hilang.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('barang-hilang.*') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        Barang Hilang
                    </a>
                </div>
            </div>
        @endif

        <!-- Laporan Group (Admin) -->
        @if (auth()->user()->role === 'admin')
            <div x-data="{ open: {{ request()->routeIs('laporan.*') ? 'true' : 'false' }} }" class="pt-1">
                <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2.5 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Laporan
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open" class="space-y-1 pl-11 pr-2 mt-1" style="display: none;">
                    <a href="{{ route('laporan.asset-health') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('laporan.asset-health') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        Asset Health
                    </a>
                </div>
            </div>
        @endif

        <div class="pt-4 pb-2">
            <div class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Transaksi</div>
            
            <a href="{{ route('peminjaman.index') }}" 
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 
               {{ request()->routeIs('peminjaman.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('peminjaman.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Peminjaman
            </a>

            <a href="{{ route('pengaduan.index') }}" 
               class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 
               {{ request()->routeIs('pengaduan.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('pengaduan.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                Pengaduan
            </a>
        </div>

        <!-- Master Data Group (Admin) -->
        @if (auth()->user()->role === 'admin')
            <div class="pt-2">
                <div x-data="{ open: {{ (request()->routeIs('users.*') || request()->routeIs('lokasi.*') || request()->routeIs('kategori.*') || request()->routeIs('activity-logs.*') || request()->routeIs('trash.*')) ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2.5 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors duration-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            Master Data
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" class="space-y-1 pl-11 pr-2 mt-1" style="display: none;">
                        <a href="{{ route('users.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('users.*') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            Kelola User
                        </a>
                        <a href="{{ route('lokasi.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('lokasi.*') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            Lokasi
                        </a>
                        <a href="{{ route('kategori.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('kategori.*') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            Kategori
                        </a>
                        <a href="{{ route('activity-logs.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('activity-logs.*') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            Log Aktivitas
                        </a>
                        <a href="{{ route('trash.index') }}" class="block px-3 py-2 text-sm rounded-md text-red-600 hover:bg-red-50 transition {{ request()->routeIs('trash.*') ? 'bg-red-50 font-medium' : '' }}">
                            Trash / Restore
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <!-- User Profile & Logout (Bottom) -->
    <div class="p-4 border-t border-gray-200 bg-gray-50 mt-auto">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs text-gray-500 truncate">
                    {{ ucfirst(Auth::user()->role) }}
                </p>
            </div>
            
            {{-- Notifications --}}
            @php
                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
            @endphp
            <a href="{{ route('notifications.index') }}" class="relative p-1.5 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                @if($unreadCount > 0)
                    <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white"></span>
                @endif
            </a>
        </div>
        
        <div class="space-y-1">
            <a href="{{ route('profile.edit') }}" class="block text-xs font-medium text-gray-500 hover:text-gray-800 transition">
                View Profile
            </a>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left text-xs font-medium text-red-600 hover:text-red-800 transition">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</nav>
