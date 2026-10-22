<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - Toko Firo</title>

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
<body class="min-h-screen flex bg-slate-50 text-gray-800 text-base antialiased">

    {{-- Sidebar Navigasi Admin Terpusat --}}
    @include('partials.sidebar_admin')

    {{-- Area Konten Utama --}}
    <main class="flex-1 min-w-0 p-8">
        {{-- Top Header Bar --}}
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-gray-200 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Dashboard Admin
                </h1>
            </div>

            <div class="flex items-center gap-4">
                {{-- Tombol Notifikasi (Teks & Badge Murni, Tanpa Ikon Lonceng) --}}
                <button type="button" class="touch-target px-4 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium text-base shadow-sm transition-colors flex items-center gap-2 cursor-pointer">
                    <span>Notifikasi</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        3 Baru
                    </span>
                </button>

                {{-- Profil Topbar (Inisial & Nama, Tanpa Ikon) --}}
                <div class="flex items-center gap-3 pl-2 border-l border-gray-300">
                    <div class="w-10 h-10 rounded-full bg-emerald-700 text-white font-bold flex items-center justify-center text-base">
                        J
                    </div>
                    <div class="hidden md:block text-left">
                        <div class="font-semibold text-gray-900 text-base leading-tight">
                            Junaidi Saleh
                        </div>
                        <div class="text-xs text-gray-500 font-medium">
                            Pemilik Toko
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Greeting & Tanggal --}}
        <section class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                Halo, Junaidi
            </h2>
            <p class="text-slate-500 text-base mt-1">
                Jumat, 3 Oktober 2026 · Ringkasan toko hari ini
            </p>
        </section>

        {{-- Baris 4 Kartu Metrik Ringkasan --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Kartu 1: Penjualan Hari Ini --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">
                        {{ $ringkasan['penjualan_hari_ini']['label'] }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        {{ $ringkasan['penjualan_hari_ini']['badge'] }}
                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-900">
                    {{ $ringkasan['penjualan_hari_ini']['nilai'] }}
                </div>
            </div>

            {{-- Kartu 2: Jumlah Transaksi --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">
                        {{ $ringkasan['jumlah_transaksi']['label'] }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        {{ $ringkasan['jumlah_transaksi']['badge'] }}
                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-900">
                    {{ $ringkasan['jumlah_transaksi']['nilai'] }}
                </div>
            </div>

            {{-- Kartu 3: Stok Menipis --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">
                        {{ $ringkasan['stok_menipis']['label'] }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        {{ $ringkasan['stok_menipis']['subteks'] }}
                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-900">
                    {{ $ringkasan['stok_menipis']['nilai'] }}
                </div>
            </div>

            {{-- Kartu 4: Laba Hari Ini --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">
                        {{ $ringkasan['laba_hari_ini']['label'] }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        {{ $ringkasan['laba_hari_ini']['badge'] }}
                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-900">
                    {{ $ringkasan['laba_hari_ini']['nilai'] }}
                </div>
            </div>
        </section>

        {{-- Area Utama Grid (2 Kolom) --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolom Kiri (Lebar): Diagram Batang "Penjualan 7 Hari Terakhir" --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-between">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">
                            Penjualan 7 Hari Terakhir
                        </h3>
                        <p class="text-sm text-gray-500">
                            Volume tren penjualan harian toko (tertutup per hari)
                        </p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 self-start sm:self-auto">
                        Puncak Tertinggi: Jum (Rp 3.180.000)
                    </span>
                </div>

                {{-- Diagram Batang Murni Tailwind CSS (Tanpa Chart.js Eksternal) --}}
                <div class="h-64 sm:h-72 flex items-end justify-between gap-3 sm:gap-6 px-4 pt-8 pb-2 border-b border-gray-200">
                    @foreach ($penjualanMingguan as $item)
                        <div class="flex-1 flex flex-col items-center h-full justify-end group">
                            {{-- Label nominal / persentase di atas batang --}}
                            <div class="text-xs font-semibold mb-2 text-gray-500 {{ $item['is_puncak'] ? 'text-emerald-700 font-bold' : '' }}">
                                {{ $item['persen'] }}%
                            </div>

                            {{-- Batang diagram --}}
                            <div class="w-full max-w-[48px] flex items-end justify-center h-full">
                                <div
                                    class="w-full rounded-t-md transition-all duration-300 {{ $item['is_puncak'] ? 'bg-emerald-600 shadow-sm' : 'bg-emerald-100 group-hover:bg-emerald-200' }}"
                                    style="height: {{ $item['persen'] }}%;"
                                    title="{{ $item['hari'] }}: {{ $item['nominal'] }}"
                                ></div>
                            </div>

                            {{-- Label nama hari di bawah batang --}}
                            <div class="text-base font-medium mt-3 {{ $item['is_puncak'] ? 'text-emerald-800 font-bold' : 'text-gray-700' }}">
                                {{ $item['hari'] }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Keterangan Diagram --}}
                <div class="flex items-center justify-between text-xs text-gray-500 mt-4 px-2">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-sm bg-emerald-600 inline-block"></span>
                            <span class="text-sm text-gray-600">Hari Ini (Puncak)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-sm bg-emerald-100 inline-block"></span>
                            <span class="text-sm text-gray-600">Hari Sebelumnya</span>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">Pembaruan real-time</span>
                </div>
            </div>

            {{-- Kolom Kanan (Lebih Ramping): Top Barang & Aktivitas Terbaru --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Kartu 1: Top Barang Hari Ini --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        Top Barang Hari Ini
                    </h3>
                    <div class="space-y-3">
                        @foreach ($topBarang as $barang)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-emerald-50/40 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-base">
                                        {{ $barang->nomor }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 text-base">
                                            {{ $barang->nama }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $barang->kategori }}
                                        </div>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-full">
                                    {{ $barang->terjual }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Kartu 2: Aktivitas Terbaru --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        Aktivitas Terbaru
                    </h3>
                    <div class="space-y-3">
                        @foreach ($aktivitasTerbaru as $akt)
                            <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100 last:border-b-0 last:pb-0">
                                <div class="flex items-start gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0 {{ $akt->tipe === 'peringatan' ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                    <p class="text-base text-gray-800 leading-snug">
                                        {{ $akt->keterangan }}
                                    </p>
                                </div>
                                <span class="text-xs font-semibold text-gray-400 shrink-0 mt-0.5">
                                    {{ $akt->waktu }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>

