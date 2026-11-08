<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Info Barang Baru - Toko Firo</title>


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

    {{-- Sidebar Navigasi Kasir Mandiri (Kepatuhan K12 & K18) --}}
    <aside class="w-64 bg-[#08331e] text-white h-screen sticky top-0 flex-shrink-0 flex flex-col justify-between overflow-y-auto p-4 outline-none focus:outline-none" tabindex="-1" style="outline: none;">
        <div class="space-y-6">
            {{-- Brand Logo Toko Firo (Bebas Ikon Grafis / SVG) --}}
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
                    {{-- 1. Dashboard Kasir --}}
                    <a
                        href="{{ Route::has('kasir.dashboard') ? route('kasir.dashboard') : url('/kasir/dashboard') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Dashboard
                    </a>

                    {{-- 2. Transaksi Penjualan --}}
                    <a
                        href="{{ Route::has('kasir.transaksi') ? route('kasir.transaksi') : url('/kasir/transaksi') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Transaksi Penjualan
                    </a>

                    {{-- 3. Lihat Stok Barang --}}
                    <a
                        href="{{ Route::has('kasir.stok') ? route('kasir.stok') : url('/kasir/stok') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Lihat Stok Barang
                    </a>

                    {{-- 4. Saldo --}}
                    <a
                        href="{{ Route::has('kasir.saldo') ? route('kasir.saldo') : url('/kasir/saldo') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Saldo
                    </a>

                    {{-- 5. Info Barang Baru (Aktif) --}}
                    <a
                        href="{{ route('kasir.info-barang') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors bg-emerald-600 text-white font-semibold shadow-sm"
                    >
                        Info Barang Baru
                    </a>

                    {{-- 6. Pengembalian Barang --}}
                    <a
                        href="{{ Route::has('kasir.pengembalian') ? route('kasir.pengembalian') : '#' }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Pengembalian Barang
                    </a>
                </nav>
            </div>
        </div>

        {{-- Profil Pengguna di Bagian Bawah Sidebar --}}
        <div class="pt-4 border-t border-emerald-900/60">
            @if (in_array(strtoupper((string) (Auth::user()?->role ?? '')), ['ADMIN', 'PEMILIK']))
                <a href="{{ Route::has('admin.profil') ? route('admin.profil') : url('/admin/profil') }}" class="block bg-[#052314] hover:bg-emerald-800/60 rounded-none p-3 mb-2 transition-colors cursor-pointer shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none text-lg">
                            {{ $kasir['inisial'] ?? strtoupper(substr((string) (Auth::user()?->nama ?? Auth::user()?->username ?? 'A'), 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-medium text-white text-base truncate">
                                {{ $kasir['nama'] ?? Auth::user()?->nama ?? Auth::user()?->username ?? 'Admin / Pemilik' }}
                            </div>
                            <div class="text-sm text-emerald-300">
                                Admin / Pemilik
                            </div>
                        </div>
                    </div>
                </a>
            @else
                <div class="bg-[#052314] rounded-none p-3 mb-2 flex items-center gap-3">
                    <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none text-lg">
                        {{ $kasir['inisial'] ?? 'K' }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-medium text-white text-base truncate">
                            {{ $kasir['nama'] ?? 'Kasir' }}
                        </div>
                        <div class="text-sm text-emerald-300">
                            {{ $kasir['role'] ?? 'Kasir' }}
                        </div>
                    </div>
                </div>
            @endif
            <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}" data-turbo="false">
                @csrf
                <button
                    type="submit"
                    class="touch-target w-full text-left flex items-center px-3 py-2 rounded-none text-red-300 hover:text-white hover:bg-red-900/40 text-base transition-colors min-h-[2.75rem] cursor-pointer"
                >
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Area Konten Utama Info Barang Baru --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
        {{-- Header Halaman Kasir (Bebas Ikon Lonceng / Grafis) --}}
        <header class="flex items-center justify-between pb-6 border-b border-gray-200 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Info Barang Baru
                </h1>
                <p class="text-slate-500 text-base mt-0.5">Pembaruan produk baru, penyesuaian harga jual, dan status stok masuk dari Admin.</p>
            </div>
            <div class="flex items-center gap-3 bg-white px-4 py-2 border border-gray-200 rounded-none shadow-sm h-[3.5rem]">
                <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none">
                    {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}
                </div>
                <div class="flex flex-col text-left justify-center">
                    <span class="text-sm font-bold text-gray-800 leading-tight">{{ Auth::user()->nama ?? 'Pengguna' }}</span>
                    <span class="text-xs text-gray-500">{{ (Auth::user()->role ?? '') === 'ADMIN' ? 'Admin / Pemilik' : 'Kasir' }}</span>
                </div>
            </div>
        </header>

        {{-- Baris Tab Filter Status (Pills) --}}
        @php
            $tabs = [
                ''      => 'Semua',
                'baru'  => 'Barang Baru',
                'harga' => 'Perubahan Harga',
                'stok'  => 'Stok Diperbarui',
            ];
            $tipeAktif = $tipe ?? '';
        @endphp

        <div class="flex flex-wrap items-center gap-3 mb-6">
            @foreach ($tabs as $key => $label)
                <a
                    href="{{ route('kasir.info-barang', $key !== '' ? ['tipe' => $key] : []) }}"
                    class="touch-target font-medium px-5 py-2.5 rounded-none min-h-[2.75rem] inline-flex items-center text-base transition-colors
                        {{ $tipeAktif === $key
                            ? 'bg-emerald-700 text-white shadow-sm'
                            : 'bg-white text-slate-700 hover:bg-slate-50 shadow-sm' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Daftar Kartu Riwayat Pembaruan --}}
        <div class="space-y-4">
            @forelse ($daftarPembaruan as $item)
                <div class="bg-white rounded-none p-5 flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
                    {{-- Sisi Kiri: Badge Inisial Teks Murni & Informasi Produk --}}
                    <div class="flex items-center min-w-0">
                        {{-- Kotak lencana visual teks murni (Kepatuhan K12) --}}
                        <div class="w-12 h-12 rounded-none bg-slate-100 flex items-center justify-center font-bold text-slate-700 mr-4 flex-shrink-0 text-base">
                            {{ $item['inisial'] }}
                        </div>

                        {{-- Teks Informasi Pembaruan --}}
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-slate-900 font-bold text-base">
                                    {{ $item['nama'] }}
                                </span>

                                @if ($item['badge'] === 'Baru')
                                    <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-0.5 rounded-none">
                                        Baru
                                    </span>
                                @elseif ($item['badge'] === 'Harga')
                                    <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-0.5 rounded-none">
                                        Harga
                                    </span>
                                @else
                                    <span class="bg-slate-100 text-slate-800 text-xs font-semibold px-2.5 py-0.5 rounded-none">
                                        Update
                                    </span>
                                @endif
                            </div>
                            <div class="text-slate-500 text-sm mt-0.5">
                                {{ $item['keterangan'] }}
                            </div>
                            <div class="text-slate-700 text-base font-medium mt-1">
                                {{ $item['detail'] }}
                            </div>
                        </div>
                    </div>

                    {{-- Sisi Kanan: Waktu Relatif --}}
                    <div class="text-slate-400 text-sm font-medium whitespace-nowrap pl-4">
                        {{ $item['waktu'] }}
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-none p-8 text-center text-slate-500 text-base shadow-sm">
                    Belum ada pembaruan untuk kategori ini.
                </div>
            @endforelse
        </div>
    </main>

</body>
</html>

