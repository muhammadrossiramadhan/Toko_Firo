<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaksi Penjualan - Toko Firo</title>

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

                    {{-- 2. Transaksi Penjualan (Aktif) --}}
                    <a
                        href="{{ route('kasir.transaksi') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors bg-emerald-600 text-white font-semibold shadow-sm"
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

    {{-- Area Konten Utama Transaksi Penjualan Kasir --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
        {{-- Header Halaman Kasir (Bebas Ikon Lonceng / Grafis) --}}
        <header class="flex items-center justify-between pb-6 border-b border-gray-200 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Transaksi Penjualan
                </h1>
                <p class="text-slate-500 text-base mt-0.5">Catat pesanan barang dan proses pembayaran belanja kasir.</p>
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

        {{-- Stepper Bar Navigasi 4 Langkah --}}
        <div class="bg-white border border-slate-200 rounded-xl p-3 mb-6 shadow-sm">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-center text-base font-semibold" id="stepper-bar">
                <div id="step-tab-1" class="step-tab py-2.5 px-4 rounded-lg bg-emerald-600 text-white shadow-sm transition-colors cursor-pointer" onclick="setStep(1)">
                    1. Pilih Barang
                </div>
                <div id="step-tab-2" class="step-tab py-2.5 px-4 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer" onclick="setStep(2)">
                    2. Review
                </div>
                <div id="step-tab-3" class="step-tab py-2.5 px-4 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer" onclick="setStep(3)">
                    3. Bayar
                </div>
                <div id="step-tab-4" class="step-tab py-2.5 px-4 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer" onclick="setStep(4)">
                    4. Selesai
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- STEP 1: Pilih Barang & Keranjang Belanja                                  --}}
        {{-- ========================================================================= --}}
        <section id="step-container-1" class="step-container">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Sisi Kiri: Katalog Produk (2 Kolom Layout) --}}
                <div class="lg:col-span-2 space-y-4">
                    {{-- Input Pencarian Barcode / Barang --}}
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                        <label for="input-cari-barang" class="block text-slate-700 font-medium text-base mb-1.5">
                            Cari Barang / Barcode
                        </label>
                        <input
                            type="text"
                            id="input-cari-barang"
                            placeholder="Cari nama barang atau scan barcode..."
                            class="touch-target w-full bg-white border border-slate-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none min-h-[2.75rem]"
                        >
                    </div>

                    {{-- Grid Kartu Produk --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($katalog as $item)
                            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between hover:border-emerald-500 transition-colors">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                            {{ $item['kode'] }}
                                        </span>
                                        <span class="text-sm text-slate-500">
                                            Stok: {{ $item['stok'] }} {{ $item['satuan'] }}
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-slate-800 text-base leading-snug line-clamp-2">
                                        {{ $item['nama'] }}
                                    </h3>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                    <div class="font-bold text-emerald-700 text-lg">
                                        Rp {{ number_format($item['harga'], 0, ',', '.') }}
                                    </div>
                                    <button
                                        type="button"
                                        onclick="tambahItemKeranjang('{{ $item['kode'] }}', '{{ $item['nama'] }}', {{ $item['harga'] }})"
                                        class="touch-target px-3 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white font-medium rounded-lg text-base min-h-[2.75rem] transition-colors"
                                    >
                                        + Tambah
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Sisi Kanan: Keranjang Belanja --}}
                <div class="lg:col-span-1">
                    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between min-h-[500px]">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                                <h2 class="text-xl font-bold text-slate-800">
                                    Keranjang (2)
                                </h2>
                                <span class="text-xs uppercase tracking-wider font-semibold text-emerald-700 bg-emerald-50 px-2 py-1 rounded">
                                    2 Item Terpilih
                                </span>
                            </div>

                            {{-- Daftar Item Keranjang --}}
                            <div class="divide-y divide-slate-100 space-y-3" id="daftar-keranjang">
                                {{-- Item 1 --}}
                                <div class="pt-3 flex flex-col gap-2">
                                    <div class="flex items-start justify-between">
                                        <div class="font-medium text-slate-800 text-base">
                                            Kertas HVS A4 70gr
                                        </div>
                                        <div class="font-semibold text-slate-900 text-base whitespace-nowrap">
                                            Rp 52.000
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-500">@ Rp 52.000</span>
                                        <div class="flex items-center gap-2">
                                            <button type="button" class="touch-target w-11 h-11 bg-slate-100 hover:bg-slate-200 rounded-lg text-base font-bold text-slate-700 flex items-center justify-center">-</button>
                                            <span class="w-8 text-center font-semibold text-base text-slate-900">1</span>
                                            <button type="button" class="touch-target w-11 h-11 bg-slate-100 hover:bg-slate-200 rounded-lg text-base font-bold text-slate-700 flex items-center justify-center">+</button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Item 2 --}}
                                <div class="pt-3 flex flex-col gap-2">
                                    <div class="flex items-start justify-between">
                                        <div class="font-medium text-slate-800 text-base">
                                            Pulpen Pilot G2
                                        </div>
                                        <div class="font-semibold text-slate-900 text-base whitespace-nowrap">
                                            Rp 30.000
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-500">2 x Rp 15.000</span>
                                        <div class="flex items-center gap-2">
                                            <button type="button" class="touch-target w-11 h-11 bg-slate-100 hover:bg-slate-200 rounded-lg text-base font-bold text-slate-700 flex items-center justify-center">-</button>
                                            <span class="w-8 text-center font-semibold text-base text-slate-900">2</span>
                                            <button type="button" class="touch-target w-11 h-11 bg-slate-100 hover:bg-slate-200 rounded-lg text-base font-bold text-slate-700 flex items-center justify-center">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total & Tombol Lanjutkan --}}
                        <div class="pt-4 border-t border-slate-200 mt-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-600 text-base">Total Belanja</span>
                                <span class="text-2xl font-bold text-slate-900">Rp 82.000</span>
                            </div>
                            <button
                                type="button"
                                onclick="setStep(2)"
                                class="touch-target w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-lg min-h-[2.75rem] text-base shadow-sm transition-colors flex items-center justify-center cursor-pointer"
                            >
                                Lanjutkan -&gt;
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ========================================================================= --}}
        {{-- STEP 2: Review Pesanan                                                    --}}
        {{-- ========================================================================= --}}
        <section id="step-container-2" class="step-container hidden">
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm max-w-4xl mx-auto space-y-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Review Ringkasan Pesanan</h2>
                    <p class="text-slate-500 text-base mt-1">Periksa kembali daftar barang belanjaan pelanggan sebelum proses pembayaran.</p>
                </div>

                {{-- Tabel Rincian --}}
                <div class="overflow-x-auto border border-slate-200 rounded-lg">
                    <table class="w-full text-left text-base divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-slate-700 font-semibold">
                            <tr>
                                <th class="py-3 px-4">Nama Barang</th>
                                <th class="py-3 px-4 text-right">Harga Satuan</th>
                                <th class="py-3 px-4 text-center">Qty</th>
                                <th class="py-3 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="py-3.5 px-4 font-medium text-slate-800">Kertas HVS A4 70gr</td>
                                <td class="py-3.5 px-4 text-right text-slate-600">Rp 52.000</td>
                                <td class="py-3.5 px-4 text-center font-semibold text-slate-800">1</td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900">Rp 52.000</td>
                            </tr>
                            <tr>
                                <td class="py-3.5 px-4 font-medium text-slate-800">Pulpen Pilot G2</td>
                                <td class="py-3.5 px-4 text-right text-slate-600">Rp 15.000</td>
                                <td class="py-3.5 px-4 text-center font-semibold text-slate-800">2</td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900">Rp 30.000</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold text-slate-900">
                            <tr>
                                <td colspan="3" class="py-4 px-4 text-right text-lg">Total Pembayaran:</td>
                                <td class="py-4 px-4 text-right text-2xl text-emerald-700">Rp 82.000</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Tombol Aksi Step 2 --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <button
                        type="button"
                        onclick="setStep(1)"
                        class="touch-target border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium px-6 py-2.5 rounded-lg min-h-[2.75rem] text-base transition-colors"
                    >
                        &lt;- Kembali
                    </button>
                    <button
                        type="button"
                        onclick="setStep(3)"
                        class="touch-target bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-lg min-h-[2.75rem] text-base shadow-sm transition-colors"
                    >
                        Lanjut ke Pembayaran -&gt;
                    </button>
                </div>
            </div>
        </section>

        {{-- ========================================================================= --}}
        {{-- STEP 3: Pembayaran & Kembalian                                            --}}
        {{-- ========================================================================= --}}
        <section id="step-container-3" class="step-container hidden">
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm max-w-2xl mx-auto space-y-6">
                {{-- Total Tagihan --}}
                <div class="text-center p-6 bg-slate-50 border border-slate-200 rounded-xl">
                    <span class="text-slate-500 text-base font-medium block">Total yang harus dibayar</span>
                    <div class="text-3xl md:text-4xl font-bold text-slate-900 my-1">
                        Rp 82.000
                    </div>
                </div>

                {{-- Pilihan Metode Pembayaran --}}
                <div>
                    <label class="block text-slate-700 font-medium text-base mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            id="btn-metode-tunai"
                            onclick="pilihMetode('tunai')"
                            class="touch-target p-3 rounded-xl border border-emerald-600 bg-emerald-600 text-white font-semibold text-base min-h-[2.75rem] transition-colors"
                        >
                            Tunai
                        </button>
                        <button
                            type="button"
                            id="btn-metode-qris"
                            onclick="pilihMetode('qris')"
                            class="touch-target p-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-base min-h-[2.75rem] transition-colors"
                        >
                            QRIS
                        </button>
                    </div>
                </div>

                {{-- Input Uang Diterima & Tombol Cepat --}}
                <div id="seksi-tunai" class="space-y-4">
                    <div>
                        <label for="input-uang-diterima" class="block text-slate-700 font-medium text-base mb-1.5">
                            Uang Diterima (Rp)
                        </label>
                        <input
                            type="text"
                            id="input-uang-diterima"
                            value="Rp 100.000"
                            oninput="hitungKembalian(this.value)"
                            class="touch-target w-full bg-white border border-slate-300 rounded-lg px-4 py-3 text-xl font-bold text-slate-900 min-h-[2.75rem] shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                        >
                    </div>

                    {{-- Tombol Cepat Pecahan Uang --}}
                    <div>
                        <span class="text-sm text-slate-500 mb-1.5 block">Pecahan Cepat:</span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <button
                                type="button"
                                onclick="isiUangCepat(82000)"
                                class="touch-target px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-medium rounded-lg text-base min-h-[2.75rem] transition-colors"
                            >
                                Uang Pas
                            </button>
                            <button
                                type="button"
                                onclick="isiUangCepat(100000)"
                                class="touch-target px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-medium rounded-lg text-base min-h-[2.75rem] transition-colors"
                            >
                                Rp 100.000
                            </button>
                            <button
                                type="button"
                                onclick="isiUangCepat(150000)"
                                class="touch-target px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-medium rounded-lg text-base min-h-[2.75rem] transition-colors"
                            >
                                Rp 150.000
                            </button>
                            <button
                                type="button"
                                onclick="isiUangCepat(200000)"
                                class="touch-target px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-medium rounded-lg text-base min-h-[2.75rem] transition-colors"
                            >
                                Rp 200.000
                            </button>
                        </div>
                    </div>

                    {{-- Banner Kembalian --}}
                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                        <span class="text-sm font-medium text-emerald-800 block">Kembalian</span>
                        <div id="teks-kembalian" class="text-2xl font-bold text-emerald-800 mt-1">
                            Kembalian Rp 18.000
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi Step 3 --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <button
                        type="button"
                        onclick="setStep(2)"
                        class="touch-target border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium px-6 py-2.5 rounded-lg min-h-[2.75rem] text-base transition-colors"
                    >
                        &lt;- Kembali
                    </button>
                    <button
                        type="button"
                        onclick="setStep(4)"
                        class="touch-target bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-8 py-2.5 rounded-lg min-h-[2.75rem] text-base shadow-sm transition-colors cursor-pointer"
                    >
                        ✓ Selesaikan Transaksi
                    </button>
                </div>
            </div>
        </section>

        {{-- ========================================================================= --}}
        {{-- STEP 4: Dialog Transaksi Berhasil                                         --}}
        {{-- ========================================================================= --}}
        <div id="step-container-4" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 text-center shadow-2xl relative" onclick="event.stopPropagation()">
                {{-- Lingkaran Hijau Centang CSS Murni --}}
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 font-bold text-3xl flex items-center justify-center mx-auto mb-4">
                    ✓
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mb-1">
                    Transaksi Berhasil!
                </h2>
                <p class="text-slate-600 text-base mb-6">
                    Pembayaran telah diterima. Kembalian: Rp 18.000
                </p>

                {{-- Checklist Sistem --}}
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-left space-y-2 mb-6">
                    <div class="flex items-center gap-2 text-base text-slate-700">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span>Saldo diperbarui</span>
                    </div>
                    <div class="flex items-center gap-2 text-base text-slate-700">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span>Stok barang dikurangi</span>
                    </div>
                    <div class="flex items-center gap-2 text-base text-slate-700">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span>Riwayat transaksi disimpan</span>
                    </div>
                </div>

                {{-- Tombol Aksi Step 4 --}}
                <div class="space-y-2.5">
                    <button
                        type="button"
                        onclick="bukaModalStruk()"
                        class="touch-target w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 rounded-lg text-base min-h-[2.75rem] shadow-sm transition-colors cursor-pointer"
                    >
                        Cetak Struk
                    </button>
                    <button
                        type="button"
                        onclick="resetTransaksi()"
                        class="touch-target w-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-medium py-2.5 rounded-lg text-base min-h-[2.75rem] transition-colors cursor-pointer"
                    >
                        Transaksi Baru -&gt;
                    </button>
                    <div class="pt-2">
                        <a
                            href="{{ Route::has('kasir.dashboard') ? route('kasir.dashboard') : url('/kasir/dashboard') }}"
                            class="touch-target inline-flex items-center justify-center text-emerald-700 hover:underline font-medium text-base min-h-[44px]"
                        >
                            Ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- MODAL STRUK TERMAL 80mm                                                   --}}
        {{-- ========================================================================= --}}
        <div id="modal-struk" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl max-w-[380px] w-full p-6 shadow-2xl relative font-mono text-sm leading-relaxed text-slate-800 border border-slate-200" onclick="event.stopPropagation()">
                {{-- Header Struk --}}
                <div class="text-center pb-3 border-b border-dashed border-slate-300">
                    <div class="text-xl font-bold font-sans text-slate-900">TOKO FIRO</div>
                    <div class="text-xs text-slate-500">Jl. Contoh No. 123, Kota</div>
                    <div class="text-xs text-slate-500">Telp: 0812-3456-7890</div>
                </div>

                {{-- Metadata Transaksi --}}
                <div class="py-3 border-b border-dashed border-slate-300 text-xs space-y-1">
                    <div class="flex justify-between">
                        <span>No. Transaksi</span>
                        <span class="font-bold">TRX-20261003-001</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tanggal</span>
                        <span>03/10/2026 14:32</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kasir</span>
                        <span>Rina Putri</span>
                    </div>
                </div>

                {{-- Daftar Rincian Barang --}}
                <div class="py-3 border-b border-dashed border-slate-300 space-y-2 text-xs">
                    <div>
                        <div class="font-medium text-slate-900">Kertas HVS A4 70gr</div>
                        <div class="flex justify-between text-slate-600">
                            <span>1 x Rp 52.000</span>
                            <span class="font-bold text-slate-900">Rp 52.000</span>
                        </div>
                    </div>
                    <div>
                        <div class="font-medium text-slate-900">Pulpen Pilot G2</div>
                        <div class="flex justify-between text-slate-600">
                            <span>2 x Rp 15.000</span>
                            <span class="font-bold text-slate-900">Rp 30.000</span>
                        </div>
                    </div>
                </div>

                {{-- Total dan Pembayaran --}}
                <div class="py-3 border-b border-dashed border-slate-300 space-y-1 text-xs">
                    <div class="flex justify-between font-bold text-base text-slate-900">
                        <span>Total</span>
                        <span>Rp 82.000</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Tunai</span>
                        <span>Rp 100.000</span>
                    </div>
                    <div class="flex justify-between font-semibold text-slate-800">
                        <span>Kembalian</span>
                        <span>Rp 18.000</span>
                    </div>
                </div>

                {{-- Footer Struk --}}
                <div class="pt-3 text-center text-xs text-slate-500">
                    <div>Terima kasih telah berbelanja!</div>
                    <div class="text-[10px] text-slate-400 mt-1">Barang yang sudah dibeli tidak dapat dikembalikan</div>
                </div>

                {{-- Tombol Tutup Struk --}}
                <div class="mt-5 pt-3 border-t border-slate-100 font-sans">
                    <button
                        type="button"
                        onclick="tutupModalStruk()"
                        class="touch-target w-full bg-slate-800 hover:bg-slate-900 text-white font-medium py-2.5 rounded-lg text-base min-h-[2.75rem] transition-colors cursor-pointer"
                    >
                        Tutup Struk
                    </button>
                </div>
            </div>
        </div>
    </main>

    {{-- Script Vanilla JavaScript untuk Wizard & Modal Interactivity --}}
    <script>
        let currentStep = 1;
        const totalTagihan = 82000;

        function setStep(step) {
            currentStep = step;

            // Perbarui tab indikator stepper
            for (let i = 1; i <= 4; i++) {
                const tab = document.getElementById('step-tab-' + i);
                if (tab) {
                    if (i === step) {
                        tab.className = 'step-tab py-2.5 px-4 rounded-lg bg-emerald-600 text-white shadow-sm transition-colors cursor-pointer';
                    } else if (i < step) {
                        tab.className = 'step-tab py-2.5 px-4 rounded-lg bg-emerald-100 text-emerald-800 font-semibold hover:bg-emerald-200 transition-colors cursor-pointer';
                    } else {
                        tab.className = 'step-tab py-2.5 px-4 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer';
                    }
                }
            }

            // Tampilkan/sembunyikan kontainer langkah
            document.getElementById('step-container-1').classList.toggle('hidden', step !== 1);
            document.getElementById('step-container-2').classList.toggle('hidden', step !== 2);
            document.getElementById('step-container-3').classList.toggle('hidden', step !== 3);

            const modalStep4 = document.getElementById('step-container-4');
            if (modalStep4) {
                modalStep4.classList.toggle('hidden', step !== 4);
            }
        }

        function pilihMetode(metode) {
            const btnTunai = document.getElementById('btn-metode-tunai');
            const btnQris = document.getElementById('btn-metode-qris');
            const seksiTunai = document.getElementById('seksi-tunai');

            if (metode === 'tunai') {
                btnTunai.className = 'touch-target p-3 rounded-xl border border-emerald-600 bg-emerald-600 text-white font-semibold text-base min-h-[2.75rem] transition-colors';
                btnQris.className = 'touch-target p-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-base min-h-[2.75rem] transition-colors';
                if (seksiTunai) seksiTunai.classList.remove('hidden');
            } else {
                btnQris.className = 'touch-target p-3 rounded-xl border border-emerald-600 bg-emerald-600 text-white font-semibold text-base min-h-[2.75rem] transition-colors';
                btnTunai.className = 'touch-target p-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-base min-h-[2.75rem] transition-colors';
                if (seksiTunai) seksiTunai.classList.add('hidden');
            }
        }

        function isiUangCepat(nominal) {
            const input = document.getElementById('input-uang-diterima');
            if (input) {
                input.value = 'Rp ' + formatRupiah(nominal);
                hitungKembalian(nominal);
            }
        }

        function hitungKembalian(val) {
            const angka = typeof val === 'number' ? val : parseInt(val.toString().replace(/[^0-9]/g, ''), 10) || 0;
            const kembalian = Math.max(0, angka - totalTagihan);
            const el = document.getElementById('teks-kembalian');
            if (el) {
                el.textContent = 'Kembalian Rp ' + formatRupiah(kembalian);
            }
        }

        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function bukaModalStruk() {
            const m = document.getElementById('modal-struk');
            if (m) m.classList.remove('hidden');
        }

        function tutupModalStruk() {
            const m = document.getElementById('modal-struk');
            if (m) m.classList.add('hidden');
        }

        function resetTransaksi() {
            tutupModalStruk();
            setStep(1);
        }

        function tambahItemKeranjang(kode, nama, harga) {
            // Memberikan umpan balik visual ketika tombol tambah diklik
            alert('Item ' + nama + ' (' + kode + ') ditambahkan ke keranjang!');
        }

        // Tutup modal struk jika area latar belakang diklik
        document.getElementById('modal-struk')?.addEventListener('click', function (e) {
            if (e.target === this) {
                tutupModalStruk();
            }
        });
    </script>
</body>
</html>

