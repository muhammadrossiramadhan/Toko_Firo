<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Saldo Harian - Toko Firo</title>

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

                    {{-- 4. Saldo (Aktif) --}}
                    <a
                        href="{{ route('kasir.saldo') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors bg-emerald-600 text-white font-semibold shadow-sm"
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

    {{-- Area Konten Utama Saldo Kasir --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
        {{-- Header Halaman Saldo Kasir (Bebas Ikon Lonceng / Grafis) --}}
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-gray-200 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Saldo
                </h1>
                <p class="text-slate-500 text-base mt-0.5">Rekapitulasi penerimaan kas dan riwayat transaksi harian kasir.</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Tombol Aksi Teks Murni (Bebas Emoji) --}}
                <button
                    type="button"
                    class="touch-target border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium px-4 py-2 rounded-lg min-h-[2.75rem] inline-flex items-center text-base transition-colors shadow-sm cursor-pointer"
                >
                    Simpan sebagai PDF
                </button>
                <button
                    type="button"
                    class="touch-target bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2 rounded-lg min-h-[2.75rem] inline-flex items-center text-base transition-colors shadow-sm cursor-pointer"
                >
                    Kirim ke Telegram
                </button>
            </div>
        </header>

        {{-- Grid Kartu Informasi Saldo --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Kartu 1: Saldo Hari Ini (Saldo Akhir) --}}
            <div class="p-6 bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-slate-500 text-sm font-medium block">
                        Saldo Hari Ini
                    </span>
                    <div class="text-2xl font-bold text-emerald-600 my-2">
                        {{ $ringkasan['saldo_akhir'] ?? $ringkasan['nominal_saldo'] }}
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-slate-500 text-sm">
                    <span>Transaksi: <strong class="text-slate-800">{{ $ringkasan['total_transaksi'] ?? 0 }}</strong></span>
                    <span>Rata: <strong class="text-slate-800">{{ $ringkasan['rata_rata'] ?? 'Rp 0' }}</strong></span>
                </div>
            </div>

            {{-- Kartu 2: Modal Awal (Saldo Awal) --}}
            <div class="p-6 bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-slate-500 text-sm font-medium block">
                        Modal Awal
                    </span>
                    <div class="text-2xl font-semibold text-slate-700 my-2">
                        {{ $ringkasan['modal_awal'] ?? $ringkasan['saldo_awal'] }}
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 text-sm text-slate-400">
                    Kas awal sebelum operasional
                </div>
            </div>

            {{-- Kartu 3: Total Penjualan Tunai --}}
            <div class="p-6 bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-slate-500 text-sm font-medium block">
                        Penjualan Tunai
                    </span>
                    <div class="text-2xl font-semibold text-emerald-700 my-2">
                        {{ $ringkasan['total_penjualan_tunai'] ?? 'Rp 0' }}
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 text-sm text-slate-400">
                    Akumulasi penerimaan kas
                </div>
            </div>

            {{-- Kartu 4: Kas Keluar / Retur --}}
            <div class="p-6 bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-slate-500 text-sm font-medium block">
                        Kas Keluar / Retur
                    </span>
                    <div class="text-2xl font-semibold text-rose-600 my-2">
                        {{ $ringkasan['kas_keluar'] ?? 'Rp 0' }}
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 text-sm text-slate-400">
                    Pengembalian barang & kas
                </div>
            </div>
        </div>

        {{-- Tabel Riwayat Saldo Transaksi --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800">
                    Riwayat Transaksi Hari Ini
                </h2>
                <span class="text-sm text-slate-500 font-medium">
                    Total: {{ count($riwayatTransaksi) }} transaksi ditampilkan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-base divide-y divide-slate-200">
                    <thead class="bg-slate-50 text-slate-700 font-semibold text-sm">
                        <tr>
                            <th class="py-3.5 px-6 whitespace-nowrap">Waktu</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">No. Transaksi</th>
                            <th class="py-3.5 px-6">Item</th>
                            <th class="py-3.5 px-6 text-right whitespace-nowrap">Total</th>
                            <th class="py-3.5 px-6 text-center whitespace-nowrap">Metode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($riwayatTransaksi as $trx)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-6 text-slate-500 whitespace-nowrap">
                                    {{ $trx['waktu'] }}
                                </td>
                                <td class="py-3.5 px-6 font-mono text-slate-700 whitespace-nowrap font-medium">
                                    {{ $trx['no_transaksi'] }}
                                </td>
                                <td class="py-3.5 px-6 font-medium text-slate-800">
                                    {{ $trx['item'] }}
                                </td>
                                <td class="py-3.5 px-6 text-right font-semibold text-slate-800 whitespace-nowrap">
                                    {{ $trx['total'] }}
                                </td>
                                <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                    @if ($trx['metode'] === 'Tunai')
                                        <span class="inline-block px-3 py-1 font-semibold text-emerald-800 bg-emerald-100 rounded-md text-sm">
                                            Tunai
                                        </span>
                                    @elseif ($trx['metode'] === 'QRIS')
                                        <span class="inline-block px-3 py-1 font-semibold text-blue-800 bg-blue-100 rounded-md text-sm">
                                            QRIS
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 font-semibold text-purple-800 bg-purple-100 rounded-md text-sm">
                                            {{ $trx['metode'] }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer Tabel --}}
            <div class="px-6 py-4 bg-white border-t border-slate-200 flex items-center justify-between text-slate-500 text-sm">
                <div>
                    Menampilkan 5 transaksi terakhir kasir
                </div>
                <div class="text-slate-400">
                    Sinkronisasi otomatis dengan bot Telegram Toko Firo
                </div>
            </div>
        </div>
    </main>

</body>
</html>

