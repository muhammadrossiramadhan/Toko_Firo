<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lihat Stok Barang - Toko Firo</title>

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

                    {{-- 3. Lihat Stok Barang (Aktif) --}}
                    <a
                        href="{{ route('kasir.stok') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors bg-emerald-600 text-white font-semibold shadow-sm"
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

    {{-- Area Konten Utama Lihat Stok Barang Kasir --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
        {{-- Header Halaman Kasir (Bebas Ikon Lonceng / Grafis) --}}
        <header class="flex items-center justify-between pb-6 border-b border-gray-200 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Lihat Stok Barang
                </h1>
                <p class="text-slate-500 text-base mt-0.5">Pantau ketersediaan stok fisik barang dan status inventori toko.</p>
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
            $statusAktif = strtolower($filters['status'] ?? '');
        @endphp
        <div class="flex flex-wrap items-center gap-2 mb-6" id="filter-status-pills">
            {{-- Tab Semua --}}
            <a
                href="{{ route('kasir.stok', array_filter(['q' => $filters['q'] ?? null, 'jenis' => $filters['jenis'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                class="touch-target inline-flex items-center gap-2 px-4 py-2 {{ $statusAktif === '' ? 'bg-white text-slate-800 font-semibold shadow-sm border border-slate-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium' }} rounded-xl min-h-[2.75rem] text-base transition-colors"
            >
                <span>Semua</span>
                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ $ringkasan['semua'] ?? 47 }}
                </span>
            </a>

            {{-- Tab Habis --}}
            <a
                href="{{ route('kasir.stok', array_filter(['status' => 'habis', 'q' => $filters['q'] ?? null, 'jenis' => $filters['jenis'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                class="touch-target inline-flex items-center gap-2 px-4 py-2 {{ $statusAktif === 'habis' ? 'bg-white text-slate-800 font-semibold shadow-sm border border-slate-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium' }} rounded-xl min-h-[2.75rem] text-base transition-colors"
            >
                <span>Habis</span>
                <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ $ringkasan['habis'] ?? 2 }}
                </span>
            </a>

            {{-- Tab Menipis --}}
            <a
                href="{{ route('kasir.stok', array_filter(['status' => 'menipis', 'q' => $filters['q'] ?? null, 'jenis' => $filters['jenis'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                class="touch-target inline-flex items-center gap-2 px-4 py-2 {{ $statusAktif === 'menipis' ? 'bg-white text-slate-800 font-semibold shadow-sm border border-slate-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium' }} rounded-xl min-h-[2.75rem] text-base transition-colors"
            >
                <span>Menipis</span>
                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ $ringkasan['menipis'] ?? 5 }}
                </span>
            </a>

            {{-- Tab Normal --}}
            <a
                href="{{ route('kasir.stok', array_filter(['status' => 'normal', 'q' => $filters['q'] ?? null, 'jenis' => $filters['jenis'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                class="touch-target inline-flex items-center gap-2 px-4 py-2 {{ $statusAktif === 'normal' ? 'bg-white text-slate-800 font-semibold shadow-sm border border-slate-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium' }} rounded-xl min-h-[2.75rem] text-base transition-colors"
            >
                <span>Normal</span>
                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ $ringkasan['normal'] ?? 28 }}
                </span>
            </a>

            {{-- Tab Aman --}}
            <a
                href="{{ route('kasir.stok', array_filter(['status' => 'aman', 'q' => $filters['q'] ?? null, 'jenis' => $filters['jenis'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                class="touch-target inline-flex items-center gap-2 px-4 py-2 {{ $statusAktif === 'aman' ? 'bg-white text-slate-800 font-semibold shadow-sm border border-slate-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium' }} rounded-xl min-h-[2.75rem] text-base transition-colors"
            >
                <span>Aman</span>
                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ $ringkasan['aman'] ?? 12 }}
                </span>
            </a>
        </div>

        {{-- Bilah Pencarian & Filter Dropdown --}}
        <form method="GET" action="{{ route('kasir.stok') }}" class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">
            @if (!empty($filters['status']))
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
            @endif

            {{-- Input Pencarian Teks Murni (Tanpa Ikon Kaca Pembesar) --}}
            <div class="flex-1 flex gap-2">
                <label for="input-cari-stok" class="sr-only">Cari nama atau kode barang...</label>
                <input
                    type="text"
                    name="q"
                    value="{{ $filters['q'] ?? '' }}"
                    id="input-cari-stok"
                    placeholder="Cari nama atau kode barang..."
                    class="touch-target w-full bg-white border border-slate-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none min-h-[2.75rem]"
                >
                <button
                    type="submit"
                    class="touch-target bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2.5 rounded-lg min-h-[2.75rem] text-base transition-colors shrink-0"
                >
                    Cari
                </button>
            </div>

            {{-- Dropdown Filter Jenis & Urutkan (Panah Teks ASCII 'v') --}}
            <div class="flex items-center gap-3">
                <div class="relative">
                    <select
                        name="jenis"
                        onchange="this.form.submit()"
                        aria-label="Filter Jenis"
                        class="touch-target appearance-none bg-white border border-slate-300 rounded-lg pl-4 pr-8 py-2.5 text-base font-medium text-slate-700 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none min-h-[2.75rem] cursor-pointer"
                    >
                        <option value="">Jenis: Semua v</option>
                        <option value="ATK" {{ ($filters['jenis'] ?? '') === 'ATK' ? 'selected' : '' }}>Jenis: ATK</option>
                        <option value="Kertas" {{ ($filters['jenis'] ?? '') === 'Kertas' ? 'selected' : '' }}>Jenis: Kertas</option>
                        <option value="Buku" {{ ($filters['jenis'] ?? '') === 'Buku' ? 'selected' : '' }}>Jenis: Buku</option>
                        <option value="Lainnya" {{ ($filters['jenis'] ?? '') === 'Lainnya' ? 'selected' : '' }}>Jenis: Lainnya</option>
                    </select>
                </div>

                <div class="relative">
                    <select
                        name="sort"
                        onchange="this.form.submit()"
                        aria-label="Urutan Tampilan"
                        class="touch-target appearance-none bg-white border border-slate-300 rounded-lg pl-4 pr-8 py-2.5 text-base font-medium text-slate-700 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none min-h-[2.75rem] cursor-pointer"
                    >
                        <option value="az" {{ ($filters['sort'] ?? '') === 'az' ? 'selected' : '' }}>Urutkan: A-Z v</option>
                        <option value="za" {{ ($filters['sort'] ?? '') === 'za' ? 'selected' : '' }}>Urutkan: Z-A v</option>
                        <option value="stok_terendah" {{ ($filters['sort'] ?? '') === 'stok_terendah' ? 'selected' : '' }}>Stok: Terendah v</option>
                        <option value="stok_tertinggi" {{ ($filters['sort'] ?? '') === 'stok_tertinggi' ? 'selected' : '' }}>Stok: Tertinggi v</option>
                    </select>
                </div>
            </div>
        </form>

        {{-- Tabel Data Stok Barang --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden mt-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-base divide-y divide-slate-200">
                    <thead class="bg-slate-50 text-slate-700 font-semibold">
                        <tr>
                            <th class="py-3.5 px-4 whitespace-nowrap">Kode</th>
                            <th class="py-3.5 px-4">Nama Item</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Jenis</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Merek</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Stok</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Satuan</th>
                            <th class="py-3.5 px-4 text-right whitespace-nowrap">Harga Jual</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($daftarBarang as $barang)
                            <tr class="hover:bg-slate-50/80 transition-colors {{ $barang['status'] === 'Habis' ? 'border-l-4 border-l-red-500 bg-red-50/20' : ($barang['status'] === 'Menipis' ? 'border-l-4 border-l-amber-500 bg-amber-50/20' : '') }}">
                                <td class="py-3.5 px-4 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $barang['kode'] }}
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-800">
                                    {{ $barang['nama'] }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $barang['jenis'] }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $barang['merek'] }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-900 whitespace-nowrap">
                                    <span>{{ $barang['stok'] }}</span>
                                    @if ((int) $barang['stok'] <= (int) ($barang['batas_minimum'] ?? 5))
                                        <span class="inline-block ml-1 px-1.5 py-0.5 text-xs font-semibold rounded {{ (int) $barang['stok'] <= 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800' }}">
                                            Kritis
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $barang['satuan'] }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-900 whitespace-nowrap">
                                    {{ $barang['harga'] }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if ($barang['status'] === 'Habis' || (int) $barang['stok'] <= 0)
                                        <span class="inline-block px-3 py-1 font-semibold text-red-700 bg-red-100 rounded-md text-sm">
                                            Habis
                                        </span>
                                    @elseif ($barang['status'] === 'Menipis' || (int) $barang['stok'] <= (int) ($barang['batas_minimum'] ?? 5))
                                        <span class="inline-block px-3 py-1 font-semibold text-amber-800 bg-amber-100 rounded-md text-sm">
                                            Menipis
                                        </span>
                                    @elseif ($barang['status'] === 'Normal')
                                        <span class="inline-block px-3 py-1 font-semibold text-blue-700 bg-blue-100 rounded-md text-sm">
                                            Normal
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 font-semibold text-emerald-800 bg-emerald-100 rounded-md text-sm">
                                            Aman
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer Keterangan Jumlah Data & Paginasi Dinamis --}}
            <div class="px-6 py-4 bg-white border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-slate-500 text-sm">
                <div>
                    @if ($daftarBarang instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                        Menampilkan {{ $daftarBarang->firstItem() ?? 0 }}-{{ $daftarBarang->lastItem() ?? 0 }} dari {{ $daftarBarang->total() }} barang
                    @else
                        Menampilkan 1-8 dari 47 barang
                    @endif
                </div>

                @if ($daftarBarang instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $daftarBarang->hasPages())
                    <nav class="flex items-center gap-2" aria-label="Navigasi Halaman Stok">
                        {{-- Tombol Sebelumnya --}}
                        @if ($daftarBarang->onFirstPage())
                            <span class="touch-target px-3 py-1.5 bg-slate-100 text-slate-400 rounded-lg text-sm min-h-[2.75rem] inline-flex items-center cursor-not-allowed">
                                Sebelumnya
                            </span>
                        @else
                            <a href="{{ $daftarBarang->previousPageUrl() }}" class="touch-target px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium rounded-lg text-sm min-h-[2.75rem] inline-flex items-center transition-colors">
                                Sebelumnya
                            </a>
                        @endif

                        {{-- Info Halaman Aktif --}}
                        <span class="text-sm font-medium text-slate-700 px-2">
                            Halaman {{ $daftarBarang->currentPage() }} dari {{ $daftarBarang->lastPage() }}
                        </span>

                        {{-- Tombol Berikutnya --}}
                        @if ($daftarBarang->hasMorePages())
                            <a href="{{ $daftarBarang->nextPageUrl() }}" class="touch-target px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium rounded-lg text-sm min-h-[2.75rem] inline-flex items-center transition-colors">
                                Berikutnya
                            </a>
                        @else
                            <span class="touch-target px-3 py-1.5 bg-slate-100 text-slate-400 rounded-lg text-sm min-h-[2.75rem] inline-flex items-center cursor-not-allowed">
                                Berikutnya
                            </span>
                        @endif
                    </nav>
                @else
                    <div class="text-slate-400">
                        Pembaruan otomatis data inventori Toko Firo
                    </div>
                @endif
            </div>
        </div>
    </main>

</body>
</html>

