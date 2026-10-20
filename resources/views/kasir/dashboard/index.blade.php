<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Kasir - Toko Firo</title>

    {{-- Tailwind CSS CDN untuk preview mandiri tanpa build step, fallback jika Vite belum dijalankan --}}
    <script src="https://cdn.tailwindcss.com"></script>

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
<body class="bg-gray-100 text-gray-800 text-base antialiased min-h-screen flex">

    {{-- Sidebar Navigasi Kasir Mandiri (Kepatuhan K12 & K18) --}}
    <aside class="w-64 bg-[#08331e] text-white flex flex-col shrink-0 min-h-screen p-4 justify-between">
        <div class="space-y-6">
            {{-- Brand Logo Toko Firo (Bebas Ikon Grafis / SVG) --}}
            <div class="bg-white rounded-xl p-3 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-600 text-white font-bold flex items-center justify-center text-lg">
                    F
                </div>
                <div class="font-bold text-gray-900 text-lg">
                    Toko Firo
                </div>
            </div>

            {{-- Menu Navigasi Kasir --}}
            <div>
                <div class="text-xs uppercase tracking-wider text-emerald-300 font-semibold px-3 mb-2">
                    Menu Kasir
                </div>
                <nav class="space-y-1">
                    {{-- 1. Dashboard Kasir (Aktif) --}}
                    <a
                        href="{{ route('kasir.dashboard') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors bg-emerald-600 text-white font-semibold shadow-sm"
                    >
                        Dashboard
                    </a>

                    {{-- 2. Transaksi Penjualan --}}
                    <a
                        href="{{ Route::has('kasir.transaksi') ? route('kasir.transaksi') : '#' }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Transaksi Penjualan
                    </a>

                    {{-- 3. Lihat Stok Barang --}}
                    <a
                        href="{{ Route::has('kasir.stok') ? route('kasir.stok') : '#' }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Lihat Stok Barang
                    </a>

                    {{-- 4. Saldo --}}
                    <a
                        href="{{ Route::has('kasir.saldo') ? route('kasir.saldo') : '#' }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Saldo
                    </a>

                    {{-- 5. Info Barang Baru --}}
                    <a
                        href="{{ Route::has('kasir.info-barang') ? route('kasir.info-barang') : '#' }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Info Barang Baru
                    </a>
                </nav>
            </div>
        </div>

        {{-- Profil Pengguna di Bagian Bawah Sidebar --}}
        <div class="pt-4 border-t border-emerald-900/60">
            <div class="bg-[#052314] rounded-xl p-3 mb-2 flex items-center gap-3 border border-emerald-900/40">
                <div class="w-10 h-10 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-lg shrink-0">
                    {{ $kasir['inisial'] ?? 'R' }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-medium text-white text-base truncate">
                        {{ $kasir['nama'] ?? 'Rina Putri' }}
                    </div>
                    <div class="text-sm text-emerald-300">
                        {{ $kasir['role'] ?? 'Kasir' }}
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                @csrf
                <button
                    type="submit"
                    class="touch-target w-full text-left flex items-center px-3 py-2 rounded-lg text-red-300 hover:text-white hover:bg-red-900/40 text-base transition-colors min-h-[2.75rem]"
                >
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Area Konten Utama Kasir --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
        {{-- Header Halaman Kasir (Bebas Ikon Lonceng / Grafis) --}}
        <header class="flex items-center justify-between pb-6 border-b border-gray-200 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Dashboard
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-base shrink-0">
                    {{ $kasir['inisial'] ?? 'R' }}
                </div>
                <span class="font-medium text-gray-800 text-base">
                    {{ $kasir['nama'] ?? 'Rina Putri' }}
                </span>
            </div>
        </header>

        {{-- Baris Salam & Tombol Aksi Cepat --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Halo, Rina
                </h2>
                <p class="text-slate-500 text-base mt-0.5">
                    {{ $kasir['tanggal'] ?? 'Jumat, 3 Oktober 2026' }}
                </p>
            </div>
            <div>
                <a
                    href="{{ Route::has('kasir.transaksi') ? route('kasir.transaksi') : '#' }}"
                    class="touch-target px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg min-h-[2.75rem] inline-flex items-center justify-center text-base shadow-sm transition-colors whitespace-nowrap"
                >
                    + Transaksi Baru
                </a>
            </div>
        </div>

        {{-- Grid 4 Kartu Navigasi Cepat (4 Kolom Desktop, 2 Kolom Tablet) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {{-- Kartu 1: Transaksi Penjualan --}}
            <a
                href="{{ Route::has('kasir.transaksi') ? route('kasir.transaksi') : '#' }}"
                class="bg-white border border-slate-200 rounded-xl p-5 hover:border-emerald-500 transition-colors shadow-sm block group"
            >
                <span class="inline-block px-2.5 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800 mb-2">
                    PENJUALAN
                </span>
                <h3 class="font-bold text-slate-800 text-lg group-hover:text-emerald-700 transition-colors">
                    Transaksi Penjualan
                </h3>
                <p class="text-slate-500 text-base mt-1">
                    Catat penjualan baru
                </p>
            </a>

            {{-- Kartu 2: Lihat Stok Barang --}}
            <a
                href="{{ Route::has('kasir.stok') ? route('kasir.stok') : '#' }}"
                class="bg-white border border-slate-200 rounded-xl p-5 hover:border-emerald-500 transition-colors shadow-sm block group"
            >
                <span class="inline-block px-2.5 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 mb-2">
                    INVENTORI
                </span>
                <h3 class="font-bold text-slate-800 text-lg group-hover:text-emerald-700 transition-colors">
                    Lihat Stok Barang
                </h3>
                <p class="text-slate-500 text-base mt-1">
                    Cek ketersediaan stok
                </p>
            </a>

            {{-- Kartu 3: Saldo --}}
            <a
                href="{{ Route::has('kasir.saldo') ? route('kasir.saldo') : '#' }}"
                class="bg-white border border-slate-200 rounded-xl p-5 hover:border-emerald-500 transition-colors shadow-sm block group"
            >
                <span class="inline-block px-2.5 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800 mb-2">
                    KEUANGAN
                </span>
                <h3 class="font-bold text-slate-800 text-lg group-hover:text-emerald-700 transition-colors">
                    Saldo
                </h3>
                <p class="text-slate-500 text-base mt-1">
                    Lihat saldo hari ini
                </p>
            </a>

            {{-- Kartu 4: Info Barang Baru --}}
            <a
                href="{{ Route::has('kasir.info-barang') ? route('kasir.info-barang') : '#' }}"
                class="bg-white border border-slate-200 rounded-xl p-5 hover:border-emerald-500 transition-colors shadow-sm block group"
            >
                <span class="inline-block px-2.5 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800 mb-2">
                    KATALOG
                </span>
                <h3 class="font-bold text-slate-800 text-lg group-hover:text-emerald-700 transition-colors">
                    Info Barang Baru
                </h3>
                <p class="text-slate-500 text-base mt-1">
                    Perubahan & item baru
                </p>
            </a>
        </div>

        {{-- Baris Widget Bawah (Grid 3 Kolom Desktop, 1 Kolom Tablet) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Widget 1: Saldo Hari Ini --}}
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-slate-500 text-base font-medium block">
                        Saldo Hari Ini
                    </span>
                    <div class="text-3xl font-bold text-slate-800 my-3">
                        {{ $saldoHariIni ?? 'Rp 3.180.000' }}
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100">
                    <a
                        href="{{ Route::has('kasir.saldo') ? route('kasir.saldo') : '#' }}"
                        class="text-emerald-700 font-medium hover:underline inline-flex items-center min-h-[44px] text-base"
                    >
                        Lihat rincian -&gt;
                    </a>
                </div>
            </div>

            {{-- Widget 2: Stok Menipis --}}
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 mb-3">
                        Stok Menipis
                    </h3>
                    <div class="divide-y divide-slate-100">
                        @foreach ($stokMenipis as $itemStok)
                            <div class="py-2.5 flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-slate-800 text-base">
                                        {{ $itemStok['nama'] }}
                                    </div>
                                    <div class="text-sm text-slate-500">
                                        Sisa {{ $itemStok['sisa'] }}
                                    </div>
                                </div>
                                <span class="bg-amber-100 text-amber-800 text-sm font-semibold px-2.5 py-1 rounded-md">
                                    {{ $itemStok['status'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100 mt-2">
                    <a
                        href="{{ Route::has('kasir.stok') ? route('kasir.stok') : '#' }}"
                        class="text-emerald-700 font-medium hover:underline inline-flex items-center min-h-[44px] text-base"
                    >
                        Lihat semua -&gt;
                    </a>
                </div>
            </div>

            {{-- Widget 3: Info Barang Baru --}}
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 mb-3">
                        Info Barang Baru
                    </h3>
                    <div class="divide-y divide-slate-100">
                        @foreach ($infoBarangBaru as $info)
                            <div class="py-2.5 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    {{-- Titik indikator lingkaran CSS murni --}}
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0 inline-block"></span>
                                    <span class="text-slate-800 text-base font-medium truncate">
                                        {{ $info['deskripsi'] }}
                                    </span>
                                </div>
                                <span class="text-slate-400 text-sm whitespace-nowrap shrink-0">
                                    {{ $info['waktu'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100 mt-2">
                    <a
                        href="{{ Route::has('kasir.info-barang') ? route('kasir.info-barang') : '#' }}"
                        class="text-emerald-700 font-medium hover:underline inline-flex items-center min-h-[44px] text-base"
                    >
                        Lihat semua -&gt;
                    </a>
                </div>
            </div>
        </div>
    </main>

</body>
</html>

