<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Data Barang - Toko Firo</title>

    {{-- Tailwind CSS CDN untuk preview mandiri tanpa build step, fallback jika Vite belum dijalankan --}}
    <script src="https://cdn.tailwindcss.com"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        /* Aturan tampilan: Font minimal 16px dan tinggi elemen interaktif minimal 44px */
        html, body {
            font-size: 16px;
        }
        .touch-target {
            min-height: 44px;
            min-width: 44px;
        }
    </style>
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
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Laporan Penjualan
                    </a>
                    <a href="{{ route('demo.barang') }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold text-base shadow-sm">
                        Kelola Data Barang
                    </a>
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Pembelian Barang
                    </a>
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Kelola Akun Pengguna
                    </a>
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Batas Stok Minimum
                    </a>
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Backup Data
                    </a>
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Log Aktivitas
                    </a>
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
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
                <div class="font-medium text-white text-base">
                    Admin
                </div>
            </div>
            <button type="button" disabled class="touch-target flex items-center px-3 py-2 rounded-lg text-red-300 text-base cursor-not-allowed">
                Keluar
            </button>
        </div>
    </aside>

    {{-- Area Konten Utama --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
        {{-- Header Halaman (Tanpa Ikon Lonceng) --}}
        <header class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Kelola Data Barang
            </h1>
        </header>

        {{-- Baris Pencarian dan Dua Dropdown (Disabled Sesuai Prototype, Tanpa Ikon) --}}
        <div class="flex flex-col md:flex-row items-center gap-4 mb-4">
            <input
                type="text"
                disabled
                placeholder="Cari nama atau kode barang..."
                class="touch-target flex-1 w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-400 cursor-not-allowed shadow-sm"
            >
            <select
                disabled
                class="touch-target bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-400 cursor-not-allowed shadow-sm min-w-[150px]"
            >
                <option>Jenis: Semua</option>
            </select>
            <select
                disabled
                class="touch-target bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-400 cursor-not-allowed shadow-sm min-w-[170px]"
            >
                <option>Urutkan: Terbaru</option>
            </select>
        </div>

        {{-- Kartu Tabel Utama --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Wrapper scroll horizontal untuk layar kecil --}}
            <div class="overflow-x-auto w-full">
                <table class="w-full min-w-[1050px] border-collapse text-left text-base">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-700 text-base font-semibold">
                            <th scope="col" class="py-3.5 px-4">Kode</th>
                            <th scope="col" class="py-3.5 px-4">Nama Item</th>
                            <th scope="col" class="py-3.5 px-4">Jenis</th>
                            <th scope="col" class="py-3.5 px-4">Merek</th>
                            <th scope="col" class="py-3.5 px-4">Rak</th>
                            <th scope="col" class="py-3.5 px-4">Satuan</th>
                            <th scope="col" class="py-3.5 px-4">Harga Pokok</th>
                            <th scope="col" class="py-3.5 px-4">Harga Jual</th>
                            <th scope="col" class="py-3.5 px-4">Stok</th>
                            <th scope="col" class="py-3.5 px-4">Status</th>
                            <th scope="col" class="py-3.5 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-800">
                        @forelse ($items as $item)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $item['kode'] }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-900 whitespace-nowrap">
                                    {{ $item['nama_item'] }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-700 whitespace-nowrap">
                                    {{ $item['jenis'] }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-700 whitespace-nowrap">
                                    {{ $item['merek'] }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-700 whitespace-nowrap">
                                    {{ $item['rak'] }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-700 whitespace-nowrap">
                                    {{ $item['satuan'] }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($item['harga_pokok'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($item['harga_jual'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-900 whitespace-nowrap">
                                    {{ $item['stok'] }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($item['status'] === 'Aman')
                                        <span class="inline-block px-3 py-1 font-semibold text-emerald-800 bg-emerald-100 rounded-full text-base">
                                            Aman
                                        </span>
                                    @elseif ($item['status'] === 'Menipis')
                                        <span class="inline-block px-3 py-1 font-semibold text-amber-800 bg-amber-100 rounded-full text-base">
                                            Menipis
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 font-semibold text-red-800 bg-red-100 rounded-full text-base">
                                            Habis
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-base">
                                    {{-- Tombol Aksi Teks Disabled (Tanpa Ikon) --}}
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            disabled
                                            class="touch-target px-2 text-gray-400 cursor-not-allowed text-base font-medium"
                                        >
                                            Ubah
                                        </button>
                                        <button
                                            type="button"
                                            disabled
                                            class="touch-target px-2 text-gray-400 cursor-not-allowed text-base font-medium"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-8 text-center text-gray-500 text-base">
                                    Tidak ada data barang yang ditampilkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Paginasi (Manual Array, 7 per halaman, total 7 halaman) --}}
            <div class="px-6 py-4 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                {{-- Informasi Data --}}
                <div class="text-base text-gray-600">
                    Menampilkan {{ $from }}-{{ $to }} dari {{ $total }} barang
                </div>

                {{-- Tombol Navigasi Paginasi --}}
                <div class="flex items-center gap-2">
                    {{-- Tombol Sebelumnya (Nonaktif di Halaman 1) --}}
                    @if ($currentPage > 1)
                        <a
                            href="{{ route('demo.barang', ['page' => $currentPage - 1]) }}"
                            class="touch-target flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Sebelumnya
                        </a>
                    @else
                        <button
                            type="button"
                            disabled
                            class="touch-target flex items-center justify-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-base font-medium text-gray-400 cursor-not-allowed"
                        >
                            Sebelumnya
                        </button>
                    @endif

                    {{-- Tombol Halaman Sesuai Prototype (Sebelumnya, 1, 2, 3, 5, ..., Selanjutnya) --}}
                    @foreach ($pageElements as $elem)
                        @if ($elem === '...')
                            <span class="touch-target flex items-center justify-center px-3 py-2 text-base text-gray-400 font-bold">
                                ...
                            </span>
                        @elseif ($elem === $currentPage)
                            <span class="touch-target flex items-center justify-center px-4 py-2 bg-emerald-600 border border-emerald-600 rounded-lg text-base font-bold text-white shadow-sm">
                                {{ $elem }}
                            </span>
                        @else
                            <a
                                href="{{ route('demo.barang', ['page' => $elem]) }}"
                                class="touch-target flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                {{ $elem }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Tombol Selanjutnya --}}
                    @if ($currentPage < $totalPages)
                        <a
                            href="{{ route('demo.barang', ['page' => $currentPage + 1]) }}"
                            class="touch-target flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Selanjutnya
                        </a>
                    @else
                        <button
                            type="button"
                            disabled
                            class="touch-target flex items-center justify-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-base font-medium text-gray-400 cursor-not-allowed"
                        >
                            Selanjutnya
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </main>

</body>
</html>
