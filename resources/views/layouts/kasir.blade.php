<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kasir - Toko Firo')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        .touch-target {
            min-height: 2.75rem;
            min-width: 2.75rem;
        }
    </style>

    @include('partials.skala')
</head>
<body class="bg-gray-100 text-gray-800 text-base antialiased min-h-screen flex flex-col md:flex-row m-0 p-0">

    {{-- 1. Satu Tombol Hamburger Eksklusif di Topbar / Header Utama Mobile (z-50) --}}
    <header class="md:hidden sticky top-0 z-50 flex items-center justify-between bg-[#08331e] text-white px-4 py-3 border-b border-emerald-900 shadow-md">
        <button type="button" onclick="toggleSidebar()" class="touch-target px-3.5 py-2 bg-emerald-800 hover:bg-emerald-700 active:bg-emerald-900 text-white font-bold rounded-none flex items-center gap-2 cursor-pointer shadow-sm text-base" aria-label="Buka Menu Navigasi">
            <span style="font-size: 1.25rem; line-height: 1;">&#9776;</span>
            <span class="text-sm font-semibold">Menu</span>
        </button>
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="Toko Firo" class="h-8 w-auto">
        </div>
        <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none text-sm shadow-sm">
            {{ strtoupper(substr(Auth::user()->nama ?? Auth::user()->username ?? 'K', 0, 1)) }}
        </div>
    </header>

    {{-- 2. Implementasi Overlay/Backdrop Gelap Menutupi Konten (z-[55]) --}}
    <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-[55] hidden transition-opacity backdrop-blur-sm cursor-pointer" aria-hidden="true"></div>

    {{-- 3. Sidebar Navigasi Kasir (z-[60] di Mobile) --}}
    @include('partials.sidebar_kasir')

    {{-- 4. Main Area --}}
    <main class="flex-1 flex flex-col p-4 sm:p-6 md:p-8 overflow-y-auto min-w-0">
        <header class="flex items-center justify-between pb-6 border-b border-gray-200 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">@yield('page_title', 'Kasir')</h1>
                @hasSection('page_subtitle')
                    <p class="text-slate-500 text-base mt-0.5">@yield('page_subtitle')</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @yield('header_actions')
                {{-- Profil Topbar --}}
                <div class="flex items-center gap-3 bg-white px-4 py-2 border border-gray-200 rounded-none shadow-sm h-[3.5rem]">
                    <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none">
                        {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex flex-col text-left justify-center">
                        <span class="text-sm font-bold text-gray-800 leading-tight">{{ Auth::user()->nama ?? 'Pengguna' }}</span>
                        <span class="text-xs text-gray-500">{{ (Auth::user()->role ?? '') === 'ADMIN' ? 'Admin / Pemilik' : 'Kasir' }}</span>
                    </div>
                </div>
            </div>
        </header>

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
