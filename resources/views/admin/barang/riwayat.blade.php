<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Transaksi - Toko Firo</title>

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

    {{-- Sidebar Navigasi Admin Terpusat (Kepatuhan K12 & K18) --}}
    @include('partials.sidebar_admin')

    {{-- Area Konten Utama --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
        {{-- Header Halaman --}}
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Riwayat Transaksi
                </h1>
                <p class="text-gray-600 text-base mt-1">
                    Daftar catatan transaksi penjualan pada modul kelola data barang.
                </p>
            </div>
            <div>
                <a
                    href="{{ route('admin.barang') }}"
                    class="touch-target px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-xl text-base shadow-sm transition-colors flex items-center justify-center"
                >
                    Kembali ke Data Barang
                </a>
            </div>
        </header>

        {{-- Bar Filter & Aksi di Atas Tabel --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Indikator Rentang Tanggal (Tanpa Ikon Kalender) --}}
                <div class="touch-target px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-base text-gray-700 font-medium flex items-center">
                    Rentang: 1 Okt — 3 Okt 2026
                </div>

                {{-- Dropdown Filter Kasir --}}
                <div class="flex items-center gap-2">
                    <label for="filter-kasir" class="text-base text-gray-600 font-medium">
                        Kasir:
                    </label>
                    <select
                        id="filter-kasir"
                        class="touch-target px-4 py-2 bg-white border border-gray-300 rounded-lg text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        <option value="Semua">Semua</option>
                        <option value="Rina">Rina</option>
                        <option value="Deni">Deni</option>
                        <option value="Siti">Siti</option>
                    </select>
                </div>
            </div>

            {{-- Tombol Aksi Ekspor (Tanpa Ikon) --}}
            <div>
                <button
                    type="button"
                    onclick="alert('Fitur ekspor data dalam pengembangan.')"
                    class="touch-target px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-xl text-base shadow-sm transition-colors flex items-center justify-center"
                >
                    Ekspor Data
                </button>
            </div>
        </div>

        {{-- Tabel Data Transaksi --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-base">
                    <thead>
                        <tr class="bg-gray-50 text-gray-700 border-b border-gray-200">
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Tanggal</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">No. Transaksi</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Kasir</th>
                            <th class="py-3 px-4 font-semibold">Items</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Total</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Metode</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-800">
                        @forelse ($transaksi as $item)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3 px-4 whitespace-nowrap text-gray-600">
                                    {{ $item->tanggal }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-mono font-medium text-gray-900">
                                    {{ $item->no_transaksi }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                                    {{ $item->kasir }}
                                </td>
                                <td class="py-3 px-4 text-gray-800 max-w-md">
                                    {{ $item->items }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-semibold text-gray-900">
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                                    {{ $item->metode }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-semibold text-sm">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-gray-500">
                                    Tidak ada data riwayat transaksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>

