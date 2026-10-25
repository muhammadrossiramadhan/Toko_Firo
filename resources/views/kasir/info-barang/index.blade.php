<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Info Barang Baru - Toko Firo</title>

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
                    {{-- 1. Dashboard Kasir --}}
                    <a
                        href="{{ Route::has('kasir.dashboard') ? route('kasir.dashboard') : url('/kasir/dashboard') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Dashboard
                    </a>

                    {{-- 2. Transaksi Penjualan --}}
                    <a
                        href="{{ Route::has('kasir.transaksi') ? route('kasir.transaksi') : url('/kasir/transaksi') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Transaksi Penjualan
                    </a>

                    {{-- 3. Lihat Stok Barang --}}
                    <a
                        href="{{ Route::has('kasir.stok') ? route('kasir.stok') : url('/kasir/stok') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Lihat Stok Barang
                    </a>

                    {{-- 4. Saldo --}}
                    <a
                        href="{{ Route::has('kasir.saldo') ? route('kasir.saldo') : url('/kasir/saldo') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Saldo
                    </a>

                    {{-- 5. Info Barang Baru (Aktif) --}}
                    <a
                        href="{{ route('kasir.info-barang') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors bg-emerald-600 text-white font-semibold shadow-sm"
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
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-base shrink-0">
                    {{ $kasir['inisial'] ?? 'R' }}
                </div>
                <span class="font-medium text-gray-800 text-base">
                    {{ $kasir['nama'] ?? 'Rina Putri' }}
                </span>
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
                    class="touch-target font-medium px-5 py-2.5 rounded-full min-h-[2.75rem] inline-flex items-center text-base transition-colors
                        {{ $tipeAktif === $key
                            ? 'bg-emerald-700 text-white shadow-sm'
                            : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Daftar Kartu Riwayat Pembaruan --}}
        <div class="space-y-4">
            @forelse ($daftarPembaruan as $item)
                <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center justify-between shadow-sm hover:border-emerald-300 transition-colors">
                    {{-- Sisi Kiri: Badge Inisial Teks Murni & Informasi Produk --}}
                    <div class="flex items-center min-w-0">
                        {{-- Kotak lencana visual teks murni (Kepatuhan K12) --}}
                        <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-700 mr-4 flex-shrink-0 text-base">
                            {{ $item['inisial'] }}
                        </div>

                        {{-- Teks Informasi Pembaruan --}}
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-slate-900 font-bold text-base">
                                    {{ $item['nama'] }}
                                </span>

                                @if ($item['badge'] === 'Baru')
                                    <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                        Baru
                                    </span>
                                @elseif ($item['badge'] === 'Harga')
                                    <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                        Harga
                                    </span>
                                @else
                                    <span class="bg-slate-100 text-slate-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
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
                <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-500 text-base">
                    Belum ada pembaruan untuk kategori ini.
                </div>
            @endforelse
        </div>
    </main>

</body>
</html>

