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

<body class="font-sans antialiased">
    <div class="min-h-screen bg-slate-100 flex print:bg-white">
        <!-- Sidebar -->
        <div class="print:hidden">
            @include('layouts.navigation')
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 ml-64 flex flex-col min-h-screen w-full print:ml-0 print:w-full">
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow sticky top-0 z-10 print:hidden">
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