<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kasir - Toko Firo')</title>

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
<body class="bg-gray-100 text-gray-800 text-base antialiased min-h-screen flex m-0 p-0">

    {{-- Sidebar Navigasi Kasir Mandiri --}}
    <aside class="w-64 bg-[#08331e] text-white h-screen sticky top-0 flex-shrink-0 flex flex-col justify-between overflow-y-auto p-4 outline-none focus:outline-none" tabindex="-1" style="outline: none;">
        <div class="space-y-6">
            {{-- Brand Logo Toko Firo --}}
            <div class="rounded-none p-2 flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Toko Firo" class="w-full max-w-[180px] h-auto object-contain">
            </div>

            @if (strtoupper(Auth::user()->role ?? '') === 'ADMIN')
            <div class="mb-2">
                <a href="{{ route('admin.dashboard') }}" class="text-white hover:text-gray-300 font-bold transition-colors flex items-center gap-2 min-h-[2.75rem] rounded-none p-2 outline-none focus:outline-none focus:ring-0" style="outline: none !important; box-shadow: none !important;">
                    &larr; Kembali ke Menu Admin
                </a>
            </div>
            @endif

            {{-- Menu Navigasi Kasir --}}
            <div>
                <div class="text-xs uppercase tracking-wider text-emerald-300 font-semibold px-3 mb-2">
                    Menu Kasir
                </div>
                <nav class="space-y-1">
                    <a href="{{ route('kasir.dashboard') }}" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors {{ request()->routeIs('kasir.dashboard') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('kasir.transaksi') }}" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors {{ request()->routeIs('kasir.transaksi') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}">
                        Transaksi Penjualan
                    </a>
                    <a href="{{ route('kasir.stok') }}" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors {{ request()->routeIs('kasir.stok') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}">
                        Lihat Stok Barang
                    </a>
                    <a href="{{ route('kasir.saldo') }}" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors {{ request()->routeIs('kasir.saldo') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}">
                        Saldo
                    </a>
                    <a href="{{ route('kasir.info-barang') }}" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors {{ request()->routeIs('kasir.info-barang') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}">
                        Info Barang Baru
                    </a>
                    <a href="{{ route('kasir.pengembalian') }}" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors {{ request()->routeIs('kasir.pengembalian') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}">
                        Pengembalian Barang
                    </a>
                </nav>
            </div>
        </div>

        {{-- Logout --}}
        <div class="pt-4 border-t border-emerald-900/60">
            <form method="POST" action="{{ route('logout') }}" data-turbo="false">
                @csrf
                <button type="submit" class="touch-target w-full text-left flex items-center px-3 py-2 rounded-none text-red-300 hover:text-white hover:bg-red-900/40 text-base transition-colors min-h-[2.75rem] cursor-pointer">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Area --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
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
