<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin - Toko Firo')</title>
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
<body class="min-h-screen flex flex-col md:flex-row bg-slate-50 text-gray-800 text-base antialiased m-0 p-0">

    {{-- Sidebar Navigasi Admin Terpusat (Termasuk Topbar Mobile Eksklusif & Backdrop) --}}
    @include('partials.sidebar_admin')

    {{-- Area Konten Utama --}}
    <main class="flex-1 min-w-0 p-4 sm:p-6 md:p-8 overflow-y-auto">
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-gray-200 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">@yield('page_title', 'Admin Dashboard')</h1>
                @hasSection('page_subtitle')
                    <p class="text-slate-500 text-base mt-1">@yield('page_subtitle')</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @yield('header_actions')
                {{-- Profil Topbar (Inisial & Nama Dinamis, Tanpa Ikon) --}}
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
