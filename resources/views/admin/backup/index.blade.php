<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Backup Data - Toko Firo</title>

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

    {{-- Notifikasi Prototype --}}
    <div id="notif-feedback" class="hidden fixed top-6 right-8 bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg text-base font-medium z-50 transition-opacity">
        Aksi backup berhasil dijalankan (prototype).
    </div>

    {{-- Sidebar Navigasi (Sesuai Standar UI Toko Firo) --}}
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
                    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Dashboard
                    </a>
                    <a href="{{ Route::has('admin.laporan.penjualan') ? route('admin.laporan.penjualan') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Laporan Penjualan
                    </a>
                    <a href="{{ Route::has('admin.barang') ? route('admin.barang') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Kelola Data Barang
                    </a>
                    <a href="{{ Route::has('admin.pembelian') ? route('admin.pembelian') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Pembelian Barang
                    </a>
                    <a href="{{ Route::has('admin.akun') ? route('admin.akun') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Kelola Akun Pengguna
                    </a>
                    <a href="{{ Route::has('admin.batas-stok') ? route('admin.batas-stok') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Batas Stok Minimum
                    </a>
                    <a href="{{ Route::has('admin.backup') ? route('admin.backup') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold text-base shadow-sm">
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
            <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                @csrf
                <button type="submit" class="touch-target w-full text-left flex items-center px-3 py-2 rounded-lg text-red-300 hover:text-white hover:bg-red-900/40 text-base transition-colors">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Area Konten Utama --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
        {{-- Header Halaman --}}
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Backup Data
            </h1>
            <p class="text-slate-500 text-base mt-1">
                Pencadangan database toko untuk mengamankan riwayat transaksi dan data master barang Toko Firo.
            </p>
        </header>

        {{-- Kartu Pengaturan Backup (Grid 2 Kolom Sesuai Figma P14/P15) --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- Kartu Kiri: Backup Manual --}}
            <div class="border-2 border-emerald-500 bg-white p-6 rounded-xl shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl font-bold text-gray-900">
                            Backup Manual
                        </h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            Tersedia
                        </span>
                    </div>
                    <p class="text-base text-gray-600 mb-6 leading-relaxed">
                        Unduh salinan cadangan database Toko Firo saat ini ke file SQL terenkripsi.
                    </p>
                </div>
                <div>
                    <button
                        type="button"
                        onclick="tampilkanNotif('Proses backup manual sedang disiapkan (prototype).')"
                        class="touch-target w-full sm:w-auto px-6 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-base shadow-sm transition-colors text-center cursor-pointer"
                    >
                        Jalankan Backup Sekarang
                    </button>
                </div>
            </div>

            {{-- Kartu Kanan: Backup Otomatis --}}
            <div class="border border-slate-200 bg-white p-6 rounded-xl shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl font-bold text-gray-900">
                            Backup Otomatis
                        </h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            Aktif
                        </span>
                    </div>
                    <p class="text-base text-gray-600 mb-6 leading-relaxed">
                        Jadwal aktif: Setiap hari pukul 22.00 WIB. Disimpan di server toko.
                    </p>
                </div>
                <div>
                    <button
                        type="button"
                        onclick="tampilkanNotif('Pengaturan jadwal backup otomatis (prototype).')"
                        class="touch-target w-full sm:w-auto px-6 py-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-base shadow-sm transition-colors text-center cursor-pointer"
                    >
                        Atur Jadwal Backup
                    </button>
                </div>
            </div>
        </section>

        {{-- Tabel Riwayat Backup --}}
        <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="p-5 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">
                    Riwayat Backup Tersimpan
                </h3>
                <span class="text-sm font-medium text-gray-500">
                    Total 4 file backup
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-base">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase font-semibold text-gray-600 tracking-wider">
                            <th class="px-6 py-4">Waktu Backup</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4">Ukuran File</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-700">
                        @foreach ($riwayatBackup as $item)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    {{ $item->waktu }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md bg-gray-100 text-gray-700">
                                        {{ $item->tipe }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono font-medium text-gray-700">
                                    {{ $item->ukuran }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        type="button"
                                        onclick="tampilkanNotif('Mengunduh salinan backup {{ $item->waktu }}...')"
                                        class="touch-target text-emerald-700 hover:text-emerald-800 hover:underline font-medium min-h-[44px] inline-flex items-center justify-end cursor-pointer"
                                    >
                                        Unduh
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Kotak Peringatan Bawah --}}
        <section class="bg-blue-50 border border-blue-200 text-blue-900 rounded-lg p-4 text-base mt-2">
            <div>
                <span class="font-bold">Pemberitahuan:</span>
                Backup disimpan di server lokal. Untuk keamanan tambahan, unduh file backup secara berkala ke perangkat terpisah.
            </div>
        </section>
    </main>

    {{-- Script Vanilla JavaScript Feedback --}}
    <script>
        function tampilkanNotif(pesan) {
            const notif = document.getElementById('notif-feedback');
            notif.textContent = pesan;
            notif.classList.remove('hidden');
            setTimeout(() => {
                notif.classList.add('hidden');
            }, 3000);
        }
    </script>
</body>
</html>

