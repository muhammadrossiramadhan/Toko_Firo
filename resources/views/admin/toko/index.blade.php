<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengaturan Toko - Toko Firo</title>

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
        Pengaturan toko berhasil disimpan (prototype).
    </div>

    {{-- Sidebar Navigasi Admin Terpusat --}}
    @include('partials.sidebar_admin')

    {{-- Area Konten Utama --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
        {{-- Header Halaman --}}
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Pengaturan Toko
            </h1>
            <p class="text-slate-500 text-base mt-1">
                Konfigurasi profil toko, identitas struk kasir, dan integrasi WhatsApp bot Toko Firo.
            </p>
        </header>

        <form onsubmit="simpanPengaturan(event)" class="space-y-6 max-w-4xl">
            {{-- Kartu 1: Informasi Toko --}}
            <section class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    Informasi Toko
                </h2>
                <div class="space-y-4">
                    <div>
                        <label for="nama_toko" class="block text-base font-medium text-gray-700 mb-1">
                            Nama Toko
                        </label>
                        <input
                            type="text"
                            id="nama_toko"
                            name="nama_toko"
                            value="{{ $infoToko['nama'] }}"
                            class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="alamat" class="block text-base font-medium text-gray-700 mb-1">
                                Alamat
                            </label>
                            <input
                                type="text"
                                id="alamat"
                                name="alamat"
                                value="{{ $infoToko['alamat'] }}"
                                class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                            >
                        </div>
                        <div>
                            <label for="telepon" class="block text-base font-medium text-gray-700 mb-1">
                                Telepon
                            </label>
                            <input
                                type="text"
                                id="telepon"
                                name="telepon"
                                value="{{ $infoToko['telepon'] }}"
                                class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                            >
                        </div>
                    </div>
                </div>
            </section>

            {{-- Kartu 2: Pengaturan Struk --}}
            <section class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    Pengaturan Struk
                </h2>
                <div class="space-y-4">
                    <div>
                        <label for="header_struk" class="block text-base font-medium text-gray-700 mb-1">
                            Header Struk
                        </label>
                        <input
                            type="text"
                            id="header_struk"
                            name="header_struk"
                            value="{{ $pengaturanStruk['header'] }}"
                            class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                        >
                    </div>
                    <div>
                        <label for="footer_struk" class="block text-base font-medium text-gray-700 mb-1">
                            Footer Struk
                        </label>
                        <input
                            type="text"
                            id="footer_struk"
                            name="footer_struk"
                            value="{{ $pengaturanStruk['footer'] }}"
                            class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                        >
                    </div>
                </div>
            </section>

            {{-- Kartu 3: WhatsApp Bot --}}
            <section class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        WhatsApp Bot
                    </h2>
                    <p class="text-base text-slate-700 mt-1">
                        <span class="text-emerald-600 font-bold">•</span> Bot Aktif — Terhubung ke {{ $whatsappBot['terhubung'] }}
                    </p>
                </div>
                <div>
                    <button
                        type="button"
                        onclick="tampilkanNotif('Status bot WhatsApp berhasil diubah (prototype).')"
                        class="touch-target px-5 py-2.5 rounded-lg border border-red-300 hover:bg-red-50 text-red-600 font-medium text-base shadow-sm transition-colors cursor-pointer"
                    >
                        Nonaktifkan
                    </button>
                </div>
            </section>

            {{-- Tombol Aksi Utama --}}
            <div>
                <button
                    type="submit"
                    class="touch-target bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-3 rounded-lg text-base shadow-sm transition-colors inline-flex items-center justify-center cursor-pointer"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
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

        function simpanPengaturan(e) {
            e.preventDefault();
            tampilkanNotif('Pengaturan toko berhasil disimpan (prototype).');
        }
    </script>
</body>
</html>

