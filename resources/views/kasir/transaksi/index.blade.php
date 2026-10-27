<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    {{-- Notifikasi Toast Mengambang K12 --}}
    <div id="toast-notif" class="fixed top-6 right-6 z-50 bg-slate-800 text-white font-medium px-5 py-3 rounded-xl shadow-xl transition-all duration-300 opacity-0 pointer-events-none text-base border border-slate-700"></div>

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

                    {{-- 5. Info Barang Baru --}}
                    <a
                        href="{{ Route::has('kasir.info-barang') ? route('kasir.info-barang') : url('/kasir/info-barang') }}"
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
        {{-- Header Halaman Kasir (Bebas Ikon Grafis) --}}
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
                <div id="step-tab-2" class="step-tab py-2.5 px-4 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer" onclick="lanjutKeReview()">
                    2. Review
                </div>
                <div id="step-tab-3" class="step-tab py-2.5 px-4 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer" onclick="lanjutKeBayar()">
                    3. Bayar
                </div>
                <div id="step-tab-4" class="step-tab py-2.5 px-4 rounded-lg bg-slate-100 text-slate-600 transition-colors opacity-70 cursor-not-allowed">
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
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="grid-katalog">
                        @forelse ($katalog as $item)
                            <div
                                class="kartu-produk bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between hover:border-emerald-500 transition-colors"
                                data-kode="{{ strtolower($item['kode']) }}"
                                data-nama="{{ strtolower($item['nama']) }}"
                            >
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono">
                                            {{ $item['kode'] }}
                                        </span>
                                        <span class="text-sm {{ $item['stok'] <= 5 ? 'text-amber-700 font-semibold' : 'text-slate-500' }}">
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
                                        onclick="tambahItemKeranjang('{{ $item['kode'] }}', '{{ addslashes($item['nama']) }}', {{ $item['harga'] }}, {{ $item['stok'] }}, '{{ $item['satuan'] }}')"
                                        class="touch-target px-3 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white font-medium rounded-lg text-base min-h-[2.75rem] transition-colors cursor-pointer"
                                    >
                                        + Tambah
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-500">
                                Belum ada barang aktif dengan ketersediaan stok di inventori.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Sisi Kanan: Keranjang Belanja Reaktif --}}
                <div class="lg:col-span-1">
                    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between min-h-[500px]">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                                <h2 class="text-xl font-bold text-slate-800">
                                    Keranjang (<span id="jumlah-item-badge">0</span>)
                                </h2>
                                <span id="total-item-badge" class="text-xs uppercase tracking-wider font-semibold text-emerald-700 bg-emerald-50 px-2 py-1 rounded">
                                    0 Item Terpilih
                                </span>
                            </div>

                            {{-- Daftar Item Keranjang --}}
                            <div class="divide-y divide-slate-100 space-y-3" id="daftar-keranjang">
                                {{-- Dirender secara dinamis oleh JavaScript --}}
                            </div>
                        </div>

                        {{-- Total & Tombol Lanjutkan --}}
                        <div class="pt-4 border-t border-slate-200 mt-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-600 text-base">Total Belanja</span>
                                <span id="total-belanja-step1" class="text-2xl font-bold text-slate-900">Rp 0</span>
                            </div>
                            <button
                                type="button"
                                onclick="lanjutKeReview()"
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
                        <tbody id="tabel-review-body" class="divide-y divide-slate-100">
                            {{-- Dirender secara dinamis oleh JavaScript --}}
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold text-slate-900">
                            <tr>
                                <td colspan="3" class="py-4 px-4 text-right text-lg">Total Pembayaran:</td>
                                <td id="total-review" class="py-4 px-4 text-right text-2xl text-emerald-700">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Tombol Aksi Step 2 --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <button
                        type="button"
                        onclick="setStep(1)"
                        class="touch-target border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium px-6 py-2.5 rounded-lg min-h-[2.75rem] text-base transition-colors cursor-pointer"
                    >
                        &lt;- Kembali
                    </button>
                    <button
                        type="button"
                        onclick="lanjutKeBayar()"
                        class="touch-target bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-lg min-h-[2.75rem] text-base shadow-sm transition-colors cursor-pointer"
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
                    <div id="total-tagihan-step3" class="text-3xl md:text-4xl font-bold text-slate-900 my-1">
                        Rp 0
                    </div>
                </div>

                {{-- Pilihan Metode Pembayaran --}}
                <div>
                    <label class="block text-slate-700 font-medium text-base mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-3">
                        <button
                            type="button"
                            id="btn-metode-tunai"
                            onclick="pilihMetode('TUNAI')"
                            class="touch-target p-3 rounded-xl border border-emerald-600 bg-emerald-600 text-white font-semibold text-base min-h-[2.75rem] transition-colors cursor-pointer"
                        >
                            Tunai
                        </button>
                        <button
                            type="button"
                            id="btn-metode-qris"
                            onclick="pilihMetode('QRIS')"
                            class="touch-target p-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-base min-h-[2.75rem] transition-colors cursor-pointer"
                        >
                            QRIS
                        </button>
                        <button
                            type="button"
                            id="btn-metode-transfer"
                            onclick="pilihMetode('TRANSFER')"
                            class="touch-target p-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-base min-h-[2.75rem] transition-colors cursor-pointer"
                        >
                            Transfer
                        </button>
                    </div>
                </div>

                {{-- Input Uang Diterima & Tombol Cepat (Khusus Tunai) --}}
                <div id="seksi-tunai" class="space-y-4">
                    <div>
                        <label for="input-uang-diterima" class="block text-slate-700 font-medium text-base mb-1.5">
                            Uang Diterima (Rp)
                        </label>
                        <input
                            type="text"
                            id="input-uang-diterima"
                            value="Rp 0"
                            oninput="onInputUang(this.value)"
                            class="touch-target w-full bg-white border border-slate-300 rounded-lg px-4 py-3 text-xl font-bold text-slate-900 min-h-[2.75rem] shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                        >
                    </div>

                    {{-- Tombol Cepat Pecahan Uang --}}
                    <div>
                        <span class="text-sm text-slate-500 mb-1.5 block">Pecahan Cepat:</span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <button
                                type="button"
                                onclick="isiUangPas()"
                                class="touch-target px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-medium rounded-lg text-base min-h-[2.75rem] transition-colors cursor-pointer"
                            >
                                Uang Pas
                            </button>
                            <button
                                type="button"
                                onclick="isiUangCepat(50000)"
                                class="touch-target px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-medium rounded-lg text-base min-h-[2.75rem] transition-colors cursor-pointer"
                            >
                                Rp 50.000
                            </button>
                            <button
                                type="button"
                                onclick="isiUangCepat(100000)"
                                class="touch-target px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-medium rounded-lg text-base min-h-[2.75rem] transition-colors cursor-pointer"
                            >
                                Rp 100.000
                            </button>
                            <button
                                type="button"
                                onclick="isiUangCepat(200000)"
                                class="touch-target px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-medium rounded-lg text-base min-h-[2.75rem] transition-colors cursor-pointer"
                            >
                                Rp 200.000
                            </button>
                        </div>
                    </div>

                    {{-- Banner Kembalian --}}
                    <div id="kotak-kembalian" class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                        <span id="label-kembalian" class="text-sm font-medium text-emerald-800 block">Kembalian</span>
                        <div id="teks-kembalian" class="text-2xl font-bold text-emerald-800 mt-1">
                            Kembalian Rp 0
                        </div>
                    </div>
                </div>

                {{-- Banner Informasi QRIS --}}
                <div id="seksi-qris" class="hidden p-4 bg-slate-50 border border-slate-200 rounded-xl text-center">
                    <p class="text-slate-700 text-base">
                        Pastikan pembayaran QRIS telah terverifikasi sukses pada layar mesin scanner/EDC sebelum menyelesaikan transaksi.
                    </p>
                </div>

                {{-- Tombol Aksi Step 3 --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <button
                        type="button"
                        onclick="setStep(2)"
                        class="touch-target border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium px-6 py-2.5 rounded-lg min-h-[2.75rem] text-base transition-colors cursor-pointer"
                    >
                        &lt;- Kembali
                    </button>
                    <button
                        type="button"
                        id="btn-selesai-transaksi"
                        onclick="prosesBayarTransaksi()"
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
                {{-- Lingkaran Hijau Centang CSS Murni (Kepatuhan K12: Bebas SVG) --}}
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 font-bold text-3xl flex items-center justify-center mx-auto mb-4">
                    ✓
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mb-1">
                    Transaksi Berhasil!
                </h2>
                <p id="sukses-subteks" class="text-slate-600 text-base mb-6">
                    Pembayaran telah diterima.
                </p>

                {{-- Checklist Sistem --}}
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-left space-y-2 mb-6">
                    <div class="flex items-center gap-2 text-base text-slate-700">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span>Saldo kasir diperbarui</span>
                    </div>
                    <div class="flex items-center gap-2 text-base text-slate-700">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span>Stok barang otomatis dikurangi</span>
                    </div>
                    <div class="flex items-center gap-2 text-base text-slate-700">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span>Riwayat transaksi tersimpan permanen</span>
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
                    <div class="text-xs text-slate-500">Penyedia ATK &amp; Kebutuhan Kantor</div>
                    <div class="text-xs text-slate-500">Jl. Yos Sudarso, Kraksaan, Probolinggo</div>
                </div>

                {{-- Metadata Transaksi --}}
                <div class="py-3 border-b border-dashed border-slate-300 text-xs space-y-1">
                    <div class="flex justify-between">
                        <span>No. Transaksi</span>
                        <span id="struk-nota" class="font-bold">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tanggal</span>
                        <span id="struk-tanggal">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kasir</span>
                        <span id="struk-kasir">{{ $kasir['nama'] ?? 'Kasir' }}</span>
                    </div>
                </div>

                {{-- Daftar Rincian Barang --}}
                <div id="struk-items" class="py-3 border-b border-dashed border-slate-300 space-y-2 text-xs">
                    {{-- Dirender secara dinamis oleh JavaScript --}}
                </div>

                {{-- Total dan Pembayaran --}}
                <div class="py-3 border-b border-dashed border-slate-300 space-y-1 text-xs">
                    <div class="flex justify-between font-bold text-base text-slate-900">
                        <span>Total</span>
                        <span id="struk-total">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span id="struk-metode-label">Tunai</span>
                        <span id="struk-bayar">Rp 0</span>
                    </div>
                    <div class="flex justify-between font-semibold text-slate-800">
                        <span>Kembalian</span>
                        <span id="struk-kembalian">Rp 0</span>
                    </div>
                </div>

                {{-- Footer Struk --}}
                <div class="pt-3 text-center text-xs text-slate-500">
                    <div>Terima kasih telah berbelanja!</div>
                    <div class="text-[10px] text-slate-400 mt-1">Barang yang sudah dibeli dapat ditukar maksimal 1x24 jam</div>
                </div>

                {{-- Tombol Tutup & Cetak Struk --}}
                <div class="mt-5 pt-3 border-t border-slate-100 font-sans space-y-2">
                    <button
                        type="button"
                        onclick="cetakResiFisik()"
                        class="touch-target w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 rounded-lg text-base min-h-[2.75rem] transition-colors cursor-pointer"
                    >
                        Cetak Resi
                    </button>
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

    {{-- Script Vanilla JavaScript Reaktif untuk Transaksi POS Kasir --}}
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        let currentStep = 1;
        let cart = @json($keranjang);
        let metodeBayar = 'TUNAI';
        let uangDiterima = 0;
        let lastTransaksi = null;
        let lastCartSnapshot = [];

        // Inisialisasi awal keranjang saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            renderCart();
            setupSearchFilter();
        });

        function formatRupiah(angka) {
            const num = Math.round(Number(angka) || 0);
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function hitungTotalBelanja() {
            return cart.reduce((total, item) => total + (Number(item.subtotal) || 0), 0);
        }

        function hitungTotalItem() {
            return cart.reduce((total, item) => total + (Number(item.qty) || 0), 0);
        }

        function alertToast(pesan, tipe = 'error') {
            const toast = document.getElementById('toast-notif');
            if (!toast) return;

            toast.textContent = pesan;
            if (tipe === 'success') {
                toast.className = 'fixed top-6 right-6 z-50 bg-emerald-600 text-white font-medium px-5 py-3 rounded-xl shadow-xl transition-all duration-300 opacity-100 text-base border border-emerald-500';
            } else {
                toast.className = 'fixed top-6 right-6 z-50 bg-red-600 text-white font-medium px-5 py-3 rounded-xl shadow-xl transition-all duration-300 opacity-100 text-base border border-red-500';
            }

            setTimeout(() => {
                toast.className = 'fixed top-6 right-6 z-50 bg-slate-800 text-white font-medium px-5 py-3 rounded-xl shadow-xl transition-all duration-300 opacity-0 pointer-events-none text-base border border-slate-700';
            }, 3500);
        }

        function renderCart() {
            const container = document.getElementById('daftar-keranjang');
            const totalStep1 = document.getElementById('total-belanja-step1');
            const jumlahBadge = document.getElementById('jumlah-item-badge');
            const totalBadge = document.getElementById('total-item-badge');

            const totalBelanja = hitungTotalBelanja();
            const totalItem = hitungTotalItem();

            if (jumlahBadge) jumlahBadge.textContent = cart.length;
            if (totalBadge) totalBadge.textContent = totalItem + ' Item Terpilih';
            if (totalStep1) totalStep1.textContent = 'Rp ' + formatRupiah(totalBelanja);

            if (!container) return;

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="py-12 text-center text-slate-400 text-base">
                        Keranjang belanja masih kosong.<br>
                        Klik tombol <strong>+ Tambah</strong> pada barang untuk memulai transaksi.
                    </div>
                `;
                return;
            }

            let html = '';
            cart.forEach(item => {
                html += `
                    <div class="pt-3 flex flex-col gap-2">
                        <div class="flex items-start justify-between">
                            <div class="font-medium text-slate-800 text-base">
                                ${escapeHtml(item.nama)}
                            </div>
                            <div class="font-semibold text-slate-900 text-base whitespace-nowrap">
                                Rp ${formatRupiah(item.subtotal)}
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">@ Rp ${formatRupiah(item.harga)}</span>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    onclick="ubahQty('${item.kode}', -1)"
                                    class="touch-target w-11 h-11 bg-slate-100 hover:bg-slate-200 rounded-lg text-base font-bold text-slate-700 flex items-center justify-center cursor-pointer"
                                >-</button>
                                <span class="w-8 text-center font-semibold text-base text-slate-900">${item.qty}</span>
                                <button
                                    type="button"
                                    onclick="ubahQty('${item.kode}', 1)"
                                    class="touch-target w-11 h-11 bg-slate-100 hover:bg-slate-200 rounded-lg text-base font-bold text-slate-700 flex items-center justify-center cursor-pointer"
                                >+</button>
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        function tambahItemKeranjang(kode, nama, harga, stok, satuan) {
            const existing = cart.find(i => i.kode === kode);
            const currentQty = existing ? existing.qty : 0;

            if (currentQty >= stok) {
                alertToast(`Stok ${nama} tidak mencukupi (sisa: ${stok}).`);
                return;
            }

            if (existing) {
                existing.qty += 1;
                existing.subtotal = existing.qty * existing.harga;
            } else {
                cart.push({
                    kode: kode,
                    nama: nama,
                    harga: Number(harga),
                    qty: 1,
                    stok: Number(stok),
                    satuan: satuan || 'pcs',
                    subtotal: Number(harga)
                });
            }

            renderCart();
            alertToast(`${nama} ditambahkan ke keranjang.`, 'success');

            // Sinkronisasi ke backend session
            fetch('{{ route("kasir.transaksi.keranjang") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: 'tambah', kode_item: kode, qty: 1 })
            }).catch(() => {});
        }

        function ubahQty(kode, delta) {
            const idx = cart.findIndex(i => i.kode === kode);
            if (idx === -1) return;

            const item = cart[idx];
            const newQty = item.qty + delta;
            let action = delta > 0 ? 'tambah' : 'kurang';

            if (newQty <= 0) {
                cart.splice(idx, 1);
                action = 'hapus';
            } else {
                if (delta > 0 && newQty > item.stok) {
                    alertToast(`Stok ${item.nama} tidak mencukupi (sisa: ${item.stok}).`);
                    return;
                }
                item.qty = newQty;
                item.subtotal = item.qty * item.harga;
            }

            renderCart();

            // Sinkronisasi ke backend session
            fetch('{{ route("kasir.transaksi.keranjang") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: action, kode_item: kode, qty: Math.abs(delta) })
            }).catch(() => {});
        }

        function lanjutKeReview() {
            if (cart.length === 0) {
                alertToast('Keranjang belanja masih kosong. Pilih minimal 1 barang terlebih dahulu.');
                return;
            }

            renderReviewTable();
            setStep(2);
        }

        function renderReviewTable() {
            const tbody = document.getElementById('tabel-review-body');
            const totalReview = document.getElementById('total-review');
            const totalBelanja = hitungTotalBelanja();

            if (totalReview) totalReview.textContent = 'Rp ' + formatRupiah(totalBelanja);
            if (!tbody) return;

            let html = '';
            cart.forEach(item => {
                html += `
                    <tr>
                        <td class="py-3.5 px-4 font-medium text-slate-800">${escapeHtml(item.nama)}</td>
                        <td class="py-3.5 px-4 text-right text-slate-600">Rp ${formatRupiah(item.harga)}</td>
                        <td class="py-3.5 px-4 text-center font-semibold text-slate-800">${item.qty}</td>
                        <td class="py-3.5 px-4 text-right font-bold text-slate-900">Rp ${formatRupiah(item.subtotal)}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function lanjutKeBayar() {
            if (cart.length === 0) {
                alertToast('Keranjang belanja masih kosong.');
                setStep(1);
                return;
            }

            const totalBelanja = hitungTotalBelanja();
            const tagihanEl = document.getElementById('total-tagihan-step3');
            if (tagihanEl) tagihanEl.textContent = 'Rp ' + formatRupiah(totalBelanja);

            // Default uang pas saat pertama kali membuka pembayaran
            isiUangPas();
            setStep(3);
        }

        function setStep(step) {
            currentStep = step;

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

            document.getElementById('step-container-1')?.classList.toggle('hidden', step !== 1);
            document.getElementById('step-container-2')?.classList.toggle('hidden', step !== 2);
            document.getElementById('step-container-3')?.classList.toggle('hidden', step !== 3);

            const modalStep4 = document.getElementById('step-container-4');
            if (modalStep4) {
                modalStep4.classList.toggle('hidden', step !== 4);
            }
        }

        function pilihMetode(metode) {
            metodeBayar = metode;
            const btnTunai = document.getElementById('btn-metode-tunai');
            const btnQris = document.getElementById('btn-metode-qris');
            const btnTransfer = document.getElementById('btn-metode-transfer');
            const seksiTunai = document.getElementById('seksi-tunai');
            const seksiQris = document.getElementById('seksi-qris');

            const btnActive = 'touch-target p-3 rounded-xl border border-emerald-600 bg-emerald-600 text-white font-semibold text-base min-h-[2.75rem] transition-colors cursor-pointer';
            const btnInactive = 'touch-target p-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-base min-h-[2.75rem] transition-colors cursor-pointer';

            btnTunai.className = metode === 'TUNAI' ? btnActive : btnInactive;
            btnQris.className = metode === 'QRIS' ? btnActive : btnInactive;
            if (btnTransfer) btnTransfer.className = metode === 'TRANSFER' ? btnActive : btnInactive;

            if (metode === 'TUNAI') {
                if (seksiTunai) seksiTunai.classList.remove('hidden');
                if (seksiQris) seksiQris.classList.add('hidden');
            } else {
                if (seksiTunai) seksiTunai.classList.add('hidden');
                if (seksiQris) {
                    seksiQris.classList.remove('hidden');
                    const textP = seksiQris.querySelector('p');
                    if (textP) {
                        textP.textContent = metode === 'QRIS'
                            ? 'Pastikan pembayaran QRIS telah terverifikasi sukses pada layar mesin scanner/EDC sebelum menyelesaikan transaksi.'
                            : 'Pastikan bukti transfer bank pelanggan telah dicek dan dana efektif masuk ke rekening toko.';
                    }
                }
                // Non-tunai otomatis set uang pas
                isiUangPas();
            }
        }

        function isiUangPas() {
            const total = hitungTotalBelanja();
            isiUangCepat(total);
        }

        function isiUangCepat(nominal) {
            uangDiterima = Number(nominal) || 0;
            const input = document.getElementById('input-uang-diterima');
            if (input) {
                input.value = 'Rp ' + formatRupiah(uangDiterima);
            }
            hitungKembalian();
        }

        function onInputUang(val) {
            const clean = val.toString().replace(/[^0-9]/g, '');
            uangDiterima = parseInt(clean, 10) || 0;
            const input = document.getElementById('input-uang-diterima');
            if (input) {
                input.value = 'Rp ' + formatRupiah(uangDiterima);
            }
            hitungKembalian();
        }

        function hitungKembalian() {
            const total = hitungTotalBelanja();
            const kembalian = uangDiterima - total;
            const el = document.getElementById('teks-kembalian');
            const kotak = document.getElementById('kotak-kembalian');
            const label = document.getElementById('label-kembalian');

            if (!el || !kotak) return;

            if (kembalian >= 0) {
                kotak.className = 'p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center';
                if (label) label.textContent = 'Kembalian';
                el.className = 'text-2xl font-bold text-emerald-800 mt-1';
                el.textContent = 'Kembalian Rp ' + formatRupiah(kembalian);
            } else {
                kotak.className = 'p-4 bg-red-50 border border-red-200 rounded-xl text-center';
                if (label) label.textContent = 'Uang Kurang';
                el.className = 'text-2xl font-bold text-red-700 mt-1';
                el.textContent = 'Kurang Rp ' + formatRupiah(Math.abs(kembalian));
            }
        }

        function prosesBayarTransaksi() {
            if (cart.length === 0) {
                alertToast('Keranjang belanja kosong.');
                setStep(1);
                return;
            }

            const total = hitungTotalBelanja();
            const bayarNominal = metodeBayar === 'QRIS' ? total : uangDiterima;

            if (bayarNominal < total) {
                const kekurangan = total - bayarNominal;
                alertToast(`Uang pembayaran kurang sebesar Rp ${formatRupiah(kekurangan)}.`);
                return;
            }

            const btnSelesai = document.getElementById('btn-selesai-transaksi');
            if (btnSelesai) {
                btnSelesai.disabled = true;
                btnSelesai.textContent = 'Menyimpan Transaksi...';
            }

            // Simpan salinan keranjang sebelum dikosongkan untuk struk nota
            lastCartSnapshot = JSON.parse(JSON.stringify(cart));

            const payload = {
                items: cart.map(i => ({ kode_item: i.kode, barang_id: i.kode, qty: i.qty })),
                nominal_bayar: bayarNominal,
                bayar: bayarNominal,
                diskon: 0,
                metode_bayar: metodeBayar,
                nama_pelanggan: 'Umum'
            };

            fetch('{{ route("kasir.transaksi.simpan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(async res => {
                const data = await res.json();
                return { ok: res.ok, status: res.status, data: data };
            })
            .then(res => {
                if (btnSelesai) {
                    btnSelesai.disabled = false;
                    btnSelesai.textContent = '✓ Selesaikan Transaksi';
                }

                if (!res.ok || res.data.status === 'error') {
                    const pesanError = res.data.message || (res.data.errors ? Object.values(res.data.errors).flat().join(', ') : 'Gagal menyimpan transaksi.');
                    alertToast(pesanError);
                    return;
                }

                // Transaksi Sukses Tercatat di Database MariaDB
                lastTransaksi = res.data;
                const nomorNota = res.data.nomor_nota || (res.data.data ? res.data.data.kode_transaksi : 'TRX-BARU');
                const kembalian = res.data.kembalian !== undefined ? res.data.kembalian : (bayarNominal - total);

                // Perbarui Tampilan Step 4
                const subteksEl = document.getElementById('sukses-subteks');
                if (subteksEl) {
                    subteksEl.textContent = `Pembayaran telah diterima. Kembalian: Rp ${formatRupiah(kembalian)} (${nomorNota})`;
                }

                // Siapkan data struk termal
                siapkanModalStruk(nomorNota, total, bayarNominal, kembalian, lastCartSnapshot);

                // Bersihkan keranjang lokal
                cart = [];
                renderCart();

                // Tampilkan Modal Berhasil (Step 4)
                setStep(4);
            })
            .catch(err => {
                if (btnSelesai) {
                    btnSelesai.disabled = false;
                    btnSelesai.textContent = '✓ Selesaikan Transaksi';
                }
                alertToast('Terjadi kendala jaringan saat menghubungi server.');
            });
        }

        function siapkanModalStruk(nota, total, bayar, kembalian, items) {
            document.getElementById('struk-nota').textContent = nota;
            document.getElementById('struk-tanggal').textContent = new Date().toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' });
            document.getElementById('struk-total').textContent = 'Rp ' + formatRupiah(total);
            document.getElementById('struk-metode-label').textContent = metodeBayar === 'QRIS' ? 'QRIS' : 'Tunai';
            document.getElementById('struk-bayar').textContent = 'Rp ' + formatRupiah(bayar);
            document.getElementById('struk-kembalian').textContent = 'Rp ' + formatRupiah(kembalian);

            const container = document.getElementById('struk-items');
            if (container) {
                let html = '';
                (items || []).forEach(it => {
                    html += `
                        <div>
                            <div class="font-medium text-slate-900">${escapeHtml(it.nama)}</div>
                            <div class="flex justify-between text-slate-600">
                                <span>${it.qty} x Rp ${formatRupiah(it.harga)}</span>
                                <span class="font-bold text-slate-900">Rp ${formatRupiah(it.subtotal)}</span>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            }
        }

        function bukaModalStruk() {
            const m = document.getElementById('modal-struk');
            if (m) m.classList.remove('hidden');
        }

        function tutupModalStruk() {
            const m = document.getElementById('modal-struk');
            if (m) m.classList.add('hidden');
        }

        function cetakResiFisik() {
            if (lastTransaksi && lastTransaksi.url_cetak) {
                window.open(lastTransaksi.url_cetak, '_blank');
            } else {
                window.print();
            }
        }

        function resetTransaksi() {
            tutupModalStruk();
            lastTransaksi = null;
            lastCartSnapshot = [];
            const searchInput = document.getElementById('input-cari-barang');
            if (searchInput) {
                searchInput.value = '';
                setupSearchFilter();
            }
            setStep(1);
        }

        function setupSearchFilter() {
            const searchInput = document.getElementById('input-cari-barang');
            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                const q = this.value.toLowerCase().trim();
                const cards = document.querySelectorAll('.kartu-produk');
                cards.forEach(card => {
                    const kode = card.getAttribute('data-kode') || '';
                    const nama = card.getAttribute('data-nama') || '';
                    if (q === '' || kode.includes(q) || nama.includes(q)) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            });

            // Tekan ENTER pada scanner: jika cocok persis 1 barang, tambahkan otomatis
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const q = this.value.toLowerCase().trim();
                    if (!q) return;

                    const visibleCards = Array.from(document.querySelectorAll('.kartu-produk:not(.hidden)'));
                    if (visibleCards.length === 1) {
                        const btn = visibleCards[0].querySelector('button');
                        if (btn) btn.click();
                        this.value = '';
                        setupSearchFilter();
                    }
                }
            });
        }

        // Tutup modal struk jika area luar diklik
        document.getElementById('modal-struk')?.addEventListener('click', function (e) {
            if (e.target === this) {
                tutupModalStruk();
            }
        });
    </script>
</body>
</html>
