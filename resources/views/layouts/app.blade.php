<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased" x-data="{ sidebarOpen: false, sidebarMinimized: localStorage.getItem('sidebarMinimized') === 'true' }" x-init="$watch('sidebarMinimized', val => localStorage.setItem('sidebarMinimized', val))">
    <div class="min-h-screen bg-slate-100 flex print:bg-white relative">
        <!-- Sidebar -->
        <div class="print:hidden">
            @include('layouts.navigation')
        </div>

        <!-- Mobile Header & Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black/50 lg:hidden transition-opacity" style="display: none;"></div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen w-full print:ml-0 print:w-full transition-all duration-300"
             :class="sidebarMinimized ? 'lg:ml-20' : 'lg:ml-64'">
            
            <!-- Mobile Header for Hamburger -->
            <div class="bg-white sticky top-0 z-10 flex items-center p-4 lg:hidden border-b border-gray-200">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="ml-4 font-bold text-xl text-gray-800">SARPRAS</div>
            </div>

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow sticky top-0 z-10 print:hidden hidden lg:block">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1 print:p-0">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>