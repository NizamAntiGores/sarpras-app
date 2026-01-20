<nav x-data="{ open: false, inventarisOpen: false, masterOpen: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'petugas')
                        {{-- Dropdown Inventaris --}}
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" 
                                    class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out
                                           {{ request()->routeIs('sarpras.*') || request()->routeIs('barang-rusak.*') || request()->routeIs('barang-hilang.*') 
                                              ? 'text-gray-900 border-b-2 border-indigo-400' 
                                              : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300' }}">
                                <span>Inventaris</span>
                                <svg class="ml-1 h-4 w-4 transition-transform" :class="{'rotate-180': open}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ route('sarpras.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('sarpras.*') ? 'bg-gray-100 font-medium' : '' }}">
                                        📦 Daftar Sarpras
                                    </a>
                                    <a href="{{ route('maintenance.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('maintenance.*') ? 'bg-gray-100 font-medium' : '' }}">
                                        🔧 Maintenance
                                    </a>
                                    <a href="{{ route('barang-hilang.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('barang-hilang.*') ? 'bg-gray-100 font-medium' : '' }}">
                                        ❌ Barang Hilang
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <x-nav-link :href="route('peminjaman.index')" :active="request()->routeIs('peminjaman.*')">
                        {{ __('Peminjaman') }}
                    </x-nav-link>
                    
                    {{-- Dropdown Master Data - Hanya Admin --}}
                    @if (auth()->user()->role === 'admin')
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" 
                                    class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out
                                           {{ request()->routeIs('users.*') || request()->routeIs('lokasi.*') || request()->routeIs('kategori.*') 
                                              ? 'text-gray-900 border-b-2 border-indigo-400' 
                                              : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300' }}">
                                <span>Master Data</span>
                                <svg class="ml-1 h-4 w-4 transition-transform" :class="{'rotate-180': open}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('users.*') ? 'bg-gray-100 font-medium' : '' }}">
                                        👥 Kelola User
                                    </a>
                                    <a href="{{ route('lokasi.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('lokasi.*') ? 'bg-gray-100 font-medium' : '' }}">
                                        📍 Lokasi
                                    </a>
                                    <a href="{{ route('kategori.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('kategori.*') ? 'bg-gray-100 font-medium' : '' }}">
                                        🏷️ Kategori
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'petugas')
                {{-- Inventaris Group --}}
                <div class="border-t border-gray-200 mt-2 pt-2">
                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Inventaris</div>
                    <x-responsive-nav-link :href="route('sarpras.index')" :active="request()->routeIs('sarpras.*')">
                        📦 Daftar Sarpras
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('maintenance.index')" :active="request()->routeIs('maintenance.*')">
                        🔧 Maintenance
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('barang-hilang.index')" :active="request()->routeIs('barang-hilang.*')">
                        ❌ Barang Hilang
                    </x-responsive-nav-link>
                </div>
            @endif
            
            <div class="border-t border-gray-200 mt-2 pt-2">
                <x-responsive-nav-link :href="route('peminjaman.index')" :active="request()->routeIs('peminjaman.*')">
                    {{ __('Peminjaman') }}
                </x-responsive-nav-link>
            </div>
            
            {{-- Master Data Group - Admin only --}}
            @if (auth()->user()->role === 'admin')
                <div class="border-t border-gray-200 mt-2 pt-2">
                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Master Data</div>
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        👥 Kelola User
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('lokasi.index')" :active="request()->routeIs('lokasi.*')">
                        📍 Lokasi
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('kategori.index')" :active="request()->routeIs('kategori.*')">
                        🏷️ Kategori
                    </x-responsive-nav-link>
                </div>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
