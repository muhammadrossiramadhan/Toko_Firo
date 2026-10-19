<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembelian Barang - Toko Firo</title>

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

    {{-- Sidebar Navigasi (Sesuai Desain UI Prototype Toko Firo) --}}
    <aside class="w-64 bg-[#08331e] text-white flex flex-col shrink-0 min-h-screen p-4 justify-between">
        <div class="space-y-6">
            {{-- Brand Card Toko Firo (Tanpa Ikon) --}}
            <div class="bg-white rounded-xl p-3 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-600 text-white font-bold flex items-center justify-center text-lg">
                    F
                </div>
                <div class="font-bold text-gray-900 text-lg">
                    Toko Firo
                </div>
            </div>

            {{-- Menu Admin --}}
            <div>
                <div class="text-xs uppercase tracking-wider text-emerald-300 font-semibold px-3 mb-2">
                    Menu Admin
                </div>
                <nav class="space-y-1">
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Dashboard
                    </a>
                    <a href="{{ Route::has('admin.laporan.penjualan') ? route('admin.laporan.penjualan') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Laporan Penjualan
                    </a>
                    <a href="{{ Route::has('admin.barang') ? route('admin.barang') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Kelola Data Barang
                    </a>
                    <a href="{{ Route::has('admin.pembelian') ? route('admin.pembelian') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold text-base shadow-sm">
                        Pembelian Barang
                    </a>
                    <a href="{{ Route::has('admin.akun') ? route('admin.akun') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Kelola Akun Pengguna
                    </a>
                    <a href="{{ Route::has('admin.batas-stok') ? route('admin.batas-stok') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Batas Stok Minimum
                    </a>
                    <a href="{{ Route::has('admin.backup') ? route('admin.backup') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Backup Data
                    </a>
                    <a href="{{ Route::has('admin.log') ? route('admin.log') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Log Aktivitas
                    </a>
                    <a href="{{ Route::has('admin.notifikasi') ? route('admin.notifikasi') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Pengaturan Notifikasi
                    </a>
                </nav>
            </div>

            {{-- Akses Kasir --}}
            <div>
                <div class="text-xs uppercase tracking-wider text-emerald-300 font-semibold px-3 mb-2">
                    Akses Kasir
                </div>
                <nav class="space-y-1">
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Transaksi Penjualan
                    </a>
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Lihat Stok Barang
                    </a>
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Saldo Harian
                    </a>
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Info Barang Baru
                    </a>
                </nav>
            </div>
        </div>

        {{-- Profil Pengguna di Bawah Sidebar (Tanpa Avatar / Ikon) --}}
        <div class="pt-4 border-t border-emerald-900/60">
            <div class="bg-[#052314] rounded-xl p-3 mb-2">
                <div class="font-medium text-white text-base truncate">
                    {{ Auth::user()?->nama ?? 'Admin / Pemilik' }}
                </div>
                <div class="text-sm text-emerald-300">
                    Admin / Pemilik
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="touch-target w-full text-left flex items-center px-3 py-2 rounded-lg text-red-300 hover:text-white hover:bg-red-900/40 text-base transition-colors">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Area Konten Utama --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
        {{-- Header Halaman (Tanpa Lonceng) --}}
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Pembelian Barang (Restock)
            </h1>
        </header>

        {{-- Navigasi Tab (JavaScript Biasa) --}}
        <div class="flex items-center gap-3 mb-6">
            <button
                type="button"
                id="btn-tab-baru"
                onclick="bukaTab('baru')"
                class="touch-target px-6 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold text-base shadow-sm transition-colors"
            >
                Pembelian Baru
            </button>
            <button
                type="button"
                id="btn-tab-riwayat"
                onclick="bukaTab('riwayat')"
                class="touch-target px-6 py-2.5 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium text-base transition-colors"
            >
                Riwayat Pembelian
            </button>
        </div>

        {{-- Konten Tab 1: Pembelian Baru --}}
        <div id="tab-konten-baru" class="space-y-6">
            {{-- Kartu Perlu Segera Restock --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">
                    Perlu Segera Restock
                </h2>
                <p class="text-gray-600 text-base mb-4">
                    Barang dengan stok menipis atau habis:
                </p>

                <div class="space-y-3">
                    @forelse ($barangRestock as $item)
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                <span class="font-bold text-gray-900 text-base">
                                    {{ $item->nama }}
                                </span>
                                <span class="text-red-600 font-semibold text-base">
                                    {{ $item->stok }} {{ $item->satuan }} ({{ $item->status }})
                                </span>
                            </div>
                            <div>
                                {{-- TODO: modal pembelian belum ada di Figma --}}
                                <button
                                    type="button"
                                    class="touch-target px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg text-base shadow-sm transition-colors flex items-center justify-center"
                                >
                                    Beli
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500">
                            Tidak ada barang yang perlu restock saat ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Konten Tab 2: Riwayat Pembelian --}}
        {{-- TODO: desain tab ini belum ada di Figma --}}
        <div id="tab-konten-riwayat" class="space-y-6 hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">
                        Riwayat Pembelian
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-base">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 border-b border-gray-200">
                                <th class="py-3 px-4 font-semibold">Waktu</th>
                                <th class="py-3 px-4 font-semibold">Kode Pembelian</th>
                                <th class="py-3 px-4 font-semibold">Barang</th>
                                <th class="py-3 px-4 font-semibold">Jumlah</th>
                                <th class="py-3 px-4 font-semibold">Total</th>
                                <th class="py-3 px-4 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-800">
                            @forelse ($riwayatPembelian as $riwayat)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3 px-4">{{ $riwayat->waktu }}</td>
                                    <td class="py-3 px-4 font-mono font-medium">{{ $riwayat->kode }}</td>
                                    <td class="py-3 px-4">{{ $riwayat->barang }}</td>
                                    <td class="py-3 px-4">{{ $riwayat->jumlah }}</td>
                                    <td class="py-3 px-4 font-medium">Rp {{ number_format($riwayat->total, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                            {{ $riwayat->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-500">
                                        Belum ada riwayat pembelian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    {{-- Script Interaktivitas Tab (Vanilla JavaScript) --}}
    <script>
        function bukaTab(namaTab) {
            const tabPembelianBaru = document.getElementById('tab-konten-baru');
            const tabRiwayat = document.getElementById('tab-konten-riwayat');
            const btnTabBaru = document.getElementById('btn-tab-baru');
            const btnTabRiwayat = document.getElementById('btn-tab-riwayat');

            if (namaTab === 'baru') {
                tabPembelianBaru.classList.remove('hidden');
                tabRiwayat.classList.add('hidden');
                btnTabBaru.className = 'touch-target px-6 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold text-base shadow-sm transition-colors';
                btnTabRiwayat.className = 'touch-target px-6 py-2.5 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium text-base transition-colors';
            } else {
                tabPembelianBaru.classList.add('hidden');
                tabRiwayat.classList.remove('hidden');
                btnTabBaru.className = 'touch-target px-6 py-2.5 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium text-base transition-colors';
                btnTabRiwayat.className = 'touch-target px-6 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold text-base shadow-sm transition-colors';
            }
        }
    </script>
</body>
</html>

