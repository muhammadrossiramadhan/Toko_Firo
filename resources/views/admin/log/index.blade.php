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

    {{-- Notifikasi Prototype --}}
    <div id="notif-ekspor" class="hidden fixed top-6 right-8 bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg text-base font-medium z-50 transition-opacity">
        File log aktivitas (CSV) berhasil diunduh (prototype).
    </div>

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
                <button
                    type="button"
                    onclick="eksporCsv()"
                    class="touch-target bg-white border border-slate-300 text-slate-700 px-5 py-2.5 rounded-lg font-medium hover:bg-slate-50 text-base shadow-sm transition-colors inline-flex items-center justify-center cursor-pointer"
                >
                    Ekspor CSV
                </button>
            </div>
        </header>

        {{-- Baris Filter & Pencarian --}}
        <section class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm mb-6 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            <div class="flex-1 max-w-md">
                <input
                    type="text"
                    id="input-cari"
                    placeholder="Cari keterangan..."
                    onkeyup="filterLog()"
                    class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                >
            </div>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Dropdown Tanggal --}}
                <select
                    id="filter-waktu"
                    onchange="filterLog()"
                    class="touch-target px-4 py-2 text-base rounded-lg border border-gray-300 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    <option value="">Hari ini</option>
                    <option value="7hari">7 Hari Terakhir</option>
                    <option value="bulan">Bulan Ini</option>
                </select>

                {{-- Dropdown Pengguna --}}
                <select
                    id="filter-user"
                    onchange="filterLog()"
                    class="touch-target px-4 py-2 text-base rounded-lg border border-gray-300 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    <option value="">User: Semua</option>
                    <option value="Rina Putri">Rina Putri</option>
                    <option value="Deni Saputra">Deni Saputra</option>
                    <option value="Junaidi Saleh">Junaidi Saleh</option>
                    <option value="Sistem">Sistem</option>
                </select>

                {{-- Dropdown Tipe Aksi --}}
                <select
                    id="filter-tipe"
                    onchange="filterLog()"
                    class="touch-target px-4 py-2 text-base rounded-lg border border-gray-300 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    <option value="">Tipe: Semua</option>
                    <option value="Transaksi">Transaksi</option>
                    <option value="Stok">Stok</option>
                    <option value="Login">Login</option>
                    <option value="Barang">Barang</option>
                    <option value="Harga">Harga</option>
                    <option value="Modal">Modal</option>
                </select>
            </div>
        </section>

        {{-- Tabel Log Aktivitas --}}
        <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
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
                        @foreach ($daftarLog as $log)
                            <tr class="hover:bg-gray-50/80 transition-colors row-log" data-user="{{ $log->user }}" data-tipe="{{ $log->tipe }}">
                                <td class="px-6 py-4 font-mono font-medium text-gray-600 whitespace-nowrap">
                                    {{ $log->waktu }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 kol-user whitespace-nowrap">
                                    {{ $log->user }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($log->tipe === 'Transaksi')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Transaksi
                                        </span>
                                    @elseif ($log->tipe === 'Stok')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-amber-100 text-amber-800">
                                            Stok
                                        </span>
                                    @elseif ($log->tipe === 'Login')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                            Login
                                        </span>
                                    @elseif ($log->tipe === 'Barang')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Barang
                                        </span>
                                    @elseif ($log->tipe === 'Harga')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Harga
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-slate-200 text-slate-800">
                                            Modal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-800 kol-detail">
                                    {{ $log->detail }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer Tabel --}}
            <div class="p-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between text-base text-gray-600 gap-2">
                <div id="info-jumlah-log">
                    Menampilkan 1-10 dari 48 log
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="touch-target px-4 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-500 font-medium text-base hover:bg-gray-100 cursor-not-allowed opacity-60" disabled>
                        Sebelumnya
                    </button>
                    <button type="button" class="touch-target px-4 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium text-base hover:bg-gray-100 cursor-pointer">
                        Berikutnya
                    </button>
                </div>
            </div>
        </section>
    </main>

    {{-- Script Vanilla JavaScript --}}
    <script>
        function eksporCsv() {
            const notif = document.getElementById('notif-ekspor');
            notif.classList.remove('hidden');
            setTimeout(() => {
                notif.classList.add('hidden');
            }, 3000);
        }

        function filterLog() {
            const kataKunci = (document.getElementById('input-cari').value || '').toLowerCase();
            const filterUser = (document.getElementById('filter-user').value || '').toLowerCase();
            const filterTipe = (document.getElementById('filter-tipe').value || '').toLowerCase();
            const rows = document.querySelectorAll('.row-log');
            let tampak = 0;

            rows.forEach(row => {
                const user = (row.getAttribute('data-user') || '').toLowerCase();
                const tipe = (row.getAttribute('data-tipe') || '').toLowerCase();
                const detail = (row.querySelector('.kol-detail').textContent || '').toLowerCase();

                const cocokKunci = detail.includes(kataKunci) || user.includes(kataKunci);
                const cocokUser = (filterUser === '') || (user === filterUser);
                const cocokTipe = (filterTipe === '') || (tipe === filterTipe);

                if (cocokKunci && cocokUser && cocokTipe) {
                    row.style.display = '';
                    tampak++;
                } else {
                    row.style.display = 'none';
                }
            });

            const info = document.getElementById('info-jumlah-log');
            info.textContent = 'Menampilkan 1-' + tampak + ' dari 48 log';
        }
    </script>
</body>
</html>

