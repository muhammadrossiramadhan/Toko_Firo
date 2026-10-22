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
<body class="min-h-screen flex bg-slate-50 text-gray-800 text-base antialiased">

    {{-- Sidebar Navigasi Admin Terpusat --}}
    @include('partials.sidebar_admin')

    {{-- Area Konten Utama --}}
    <main class="flex-1 min-w-0 p-8">
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
                                    Catat Restock
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

