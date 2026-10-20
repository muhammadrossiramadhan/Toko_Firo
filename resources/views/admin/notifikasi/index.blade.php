<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengaturan Notifikasi Bot - Toko Firo</title>

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
        Pemberitahuan berhasil diproses (prototype).
    </div>

    {{-- Sidebar Navigasi Admin Terpusat --}}
    @include('partials.sidebar_admin')

    {{-- Area Konten Utama --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
        {{-- Header Halaman --}}
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Pengaturan Notifikasi Bot
            </h1>
            <p class="text-slate-500 text-base mt-1">
                Konfigurasi bot perpesanan instan untuk notifikasi otomatis toko, peringatan stok, dan laporan transaksi.
            </p>
        </header>

        {{-- Grid Konfigurasi Bot (2 Kolom Sesuai Figma P17) --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Kartu Kiri: Telegram Bot --}}
            <div class="border-2 border-emerald-500 bg-white p-6 rounded-xl shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl font-bold text-gray-900">
                            Telegram Bot
                        </h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800">
                            • Terhubung (Prototipe)
                        </span>
                    </div>
                    <div class="text-base text-slate-600 mt-4 mb-6 font-mono bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <div><span class="text-gray-500 font-sans font-medium">Bot Token:</span> {{ $telegramConfig['bot_token'] }}</div>
                        <div class="mt-1"><span class="text-gray-500 font-sans font-medium">Chat ID:</span> {{ $telegramConfig['chat_id'] }}</div>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        onclick="tampilkanNotif('Pesan tes berhasil dikirim ke bot Telegram (prototype).')"
                        class="touch-target px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-base shadow-sm transition-colors cursor-pointer"
                    >
                        Kirim Pesan Tes
                    </button>
                    <button
                        type="button"
                        onclick="tampilkanNotif('Koneksi bot Telegram diputuskan (prototype).')"
                        class="touch-target px-5 py-2.5 rounded-lg border border-red-300 hover:bg-red-50 text-red-600 font-medium text-base shadow-sm transition-colors cursor-pointer"
                    >
                        Putuskan
                    </button>
                </div>
            </div>

            {{-- Kartu Kanan: WhatsApp Bot --}}
            <div class="border border-slate-200 bg-white p-6 rounded-xl shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl font-bold text-gray-900">
                            WhatsApp Bot
                        </h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-slate-100 text-slate-700">
                            Segera Hadir
                        </span>
                    </div>
                    <p class="text-base text-gray-600 mt-4 mb-6 leading-relaxed">
                        Integrasi WhatsApp Business API sedang dalam tahap pengembangan.
                    </p>
                </div>
                <div>
                    <button
                        type="button"
                        disabled
                        class="touch-target px-5 py-2.5 rounded-lg border border-gray-300 bg-gray-50 text-gray-400 font-medium text-base cursor-not-allowed"
                    >
                        Hubungkan WhatsApp
                    </button>
                </div>
            </div>
        </section>

        {{-- Kartu Pengaturan: Jenis Notifikasi yang Dikirim --}}
        <section class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">
                Jenis Notifikasi yang Dikirim
            </h2>
            <div class="divide-y divide-gray-100">
                @foreach ($daftarPengaturan as $pengaturan)
                    <label class="flex items-center justify-between py-3.5 cursor-pointer min-h-[2.75rem]">
                        <span class="text-base text-gray-800 font-medium pr-4">
                            {{ $pengaturan->label }}
                        </span>
                        <div class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input
                                type="checkbox"
                                {{ $pengaturan->aktif ? 'checked' : '' }}
                                onchange="tampilkanNotif('Pengaturan notifikasi berhasil diperbarui (prototype).')"
                                class="sr-only peer"
                            >
                            <div class="w-12 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </div>
                    </label>
                @endforeach
            </div>
        </section>

        {{-- Tabel Riwayat Notifikasi Terkirim --}}
        <section class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="p-5 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">
                    Riwayat Notifikasi Terkirim
                </h2>
                <span class="text-sm font-medium text-gray-500">
                    4 entri terakhir
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-base">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase font-semibold text-gray-600 tracking-wider">
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4">Pesan</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-700">
                        @foreach ($riwayatNotifikasi as $notif)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 font-mono font-medium text-gray-600 whitespace-nowrap">
                                    {{ $notif->waktu }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md bg-gray-100 text-gray-700">
                                        {{ $notif->tipe }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-800">
                                    {{ $notif->pesan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($notif->status === 'Terkirim')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                            Terkirim
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-700">
                                            Gagal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

