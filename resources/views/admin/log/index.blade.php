<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log Aktivitas - Toko Firo</title>

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
        {{-- Header Halaman --}}
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Log Aktivitas
                </h1>
                <p class="text-slate-500 text-base mt-1">
                    Rekam jejak seluruh aktivitas sistem, transaksi, dan perubahan data Toko Firo.
                </p>
            </div>
            <div>
                <a
                    href="{{ route('admin.log', array_merge(request()->query(), ['export' => 'csv'])) }}"
                    class="touch-target bg-white border border-slate-300 text-slate-700 px-5 py-2.5 rounded-none font-medium hover:bg-slate-50 text-base shadow-sm transition-colors inline-flex items-center justify-center cursor-pointer"
                >
                    Ekspor CSV
                </a>
            </div>
        </header>

        {{-- Baris Filter & Pencarian --}}
        <form method="GET" action="{{ route('admin.log') }}" class="bg-white rounded-none border border-gray-200 p-4 shadow-sm mb-6 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            <div class="flex-1 max-w-md">
                <input
                    type="text"
                    name="kata_kunci"
                    id="input-cari"
                    placeholder="Cari keterangan..."
                    value="{{ request('kata_kunci') ?? request('keyword') ?? request('q') ?? '' }}"
                    class="touch-target w-full px-4 py-2 text-base rounded-none border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                >
            </div>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Dropdown Rentang Tanggal --}}
                <div class="flex items-center gap-2">
                    <input
                        type="date"
                        name="tanggal_mulai"
                        value="{{ request('tanggal_mulai') ?? request('start_date') ?? '' }}"
                        aria-label="Tanggal Mulai"
                        class="touch-target px-3 py-2 text-base rounded-none border border-gray-300 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                    <span class="text-gray-500 text-sm">s/d</span>
                    <input
                        type="date"
                        name="tanggal_akhir"
                        value="{{ request('tanggal_akhir') ?? request('end_date') ?? '' }}"
                        aria-label="Tanggal Akhir"
                        class="touch-target px-3 py-2 text-base rounded-none border border-gray-300 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>

                {{-- Dropdown Tipe Aksi --}}
                <select
                    name="tipe"
                    id="filter-tipe"
                    class="touch-target px-4 py-2 text-base rounded-none border border-gray-300 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    <option value="">Tipe: Semua</option>
                    @foreach ($tipeAksiList as $tipeOption)
                        <option value="{{ $tipeOption }}" {{ (request('tipe') === $tipeOption || ($filter['tipe'] ?? '') === $tipeOption) ? 'selected' : '' }}>
                            {{ $tipeOption }}
                        </option>
                    @endforeach
                </select>

                <button
                    type="submit"
                    class="touch-target px-5 py-2.5 rounded-none bg-emerald-600 text-white font-medium hover:bg-emerald-700 text-base shadow-sm transition-colors cursor-pointer"
                >
                    Filter
                </button>

                @if(request()->hasAny(['kata_kunci', 'tipe', 'tanggal_mulai', 'tanggal_akhir', 'keyword', 'q', 'start_date', 'end_date']))
                    <a
                        href="{{ route('admin.log') }}"
                        class="touch-target px-4 py-2.5 rounded-none border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50 text-base shadow-sm inline-flex items-center justify-center cursor-pointer"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Tabel Log Aktivitas --}}
        <section class="bg-white rounded-none border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-base" id="tabel-log">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase font-semibold text-gray-600 tracking-wider">
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Tipe Aksi</th>
                            <th class="px-6 py-4">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-700">
                        @forelse ($daftarLog as $log)
                            <tr class="hover:bg-gray-50/80 transition-colors row-log" data-user="{{ $log->user }}" data-tipe="{{ $log->tipe }}">
                                <td class="px-6 py-4 font-mono font-medium text-gray-600 whitespace-nowrap">
                                    {{ $log->waktu }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 kol-user whitespace-nowrap">
                                    {{ $log->user }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($log->tipe === 'Transaksi')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-none bg-blue-100 text-blue-800">
                                            Transaksi
                                        </span>
                                    @elseif ($log->tipe === 'Stok')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-none bg-amber-100 text-amber-800">
                                            Stok
                                        </span>
                                    @elseif ($log->tipe === 'Login')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-none bg-emerald-100 text-emerald-800">
                                            Login
                                        </span>
                                    @elseif ($log->tipe === 'Barang')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-none bg-purple-100 text-purple-800">
                                            Barang
                                        </span>
                                    @elseif ($log->tipe === 'Harga')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-none bg-yellow-100 text-yellow-800">
                                            Harga
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-none bg-slate-200 text-slate-800">
                                            Modal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-800 kol-detail">
                                    {{ $log->detail }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                    Tidak ada log aktivitas ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Tabel & Paginasi --}}
            <div class="p-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between text-base text-gray-600 gap-4">
                <div id="info-jumlah-log">
                    @if($daftarLog->total() > 0)
                        Menampilkan {{ $daftarLog->firstItem() }}-{{ $daftarLog->lastItem() }} dari {{ $daftarLog->total() }} log
                    @else
                        Menampilkan 0 log
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if ($daftarLog->onFirstPage())
                        <span class="touch-target inline-flex items-center justify-center px-4 py-2 rounded-none border border-gray-300 bg-white text-gray-400 font-medium text-base cursor-not-allowed opacity-60">
                            Sebelumnya
                        </span>
                    @else
                        <a href="{{ $daftarLog->previousPageUrl() }}" class="touch-target inline-flex items-center justify-center px-4 py-2 rounded-none border border-gray-300 bg-white text-gray-700 font-medium text-base hover:bg-gray-100">
                            Sebelumnya
                        </a>
                    @endif

                    <span class="text-sm font-medium text-gray-700 px-2">
                        Halaman {{ $daftarLog->currentPage() }} dari {{ $daftarLog->lastPage() ?: 1 }}
                    </span>

                    @if ($daftarLog->hasMorePages())
                        <a href="{{ $daftarLog->nextPageUrl() }}" class="touch-target inline-flex items-center justify-center px-4 py-2 rounded-none border border-gray-300 bg-white text-gray-700 font-medium text-base hover:bg-gray-100">
                            Berikutnya
                        </a>
                    @else
                        <span class="touch-target inline-flex items-center justify-center px-4 py-2 rounded-none border border-gray-300 bg-white text-gray-400 font-medium text-base cursor-not-allowed opacity-60">
                            Berikutnya
                        </span>
                    @endif
                </div>
            </div>
        </section>
    </main>
</body>
</html>
