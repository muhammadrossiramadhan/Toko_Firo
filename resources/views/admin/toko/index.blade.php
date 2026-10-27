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
<body class="min-h-screen flex bg-slate-50 text-gray-800 text-base antialiased">

    {{-- Sidebar Navigasi Admin Terpusat --}}
    @include('partials.sidebar_admin')

    {{-- Area Konten Utama --}}
    <main class="flex-1 min-w-0 p-8">
        {{-- Header Halaman --}}
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Pengaturan Toko
            </h1>
            <p class="text-slate-500 text-base mt-1">
                Konfigurasi profil toko, identitas struk kasir, dan integrasi WhatsApp bot Toko Firo.
            </p>
        </header>

        {{-- Flash Message Alerts --}}
        @if (session('success'))
            <div class="mb-6 max-w-4xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl text-base font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 max-w-4xl bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl text-base font-medium">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.toko.update') }}" class="space-y-6 max-w-4xl">
            @csrf
            @method('PUT')

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
                            value="{{ old('nama_toko', $pengaturan['nama_toko'] ?? $infoToko['nama']) }}"
                            class="touch-target w-full px-4 py-2 text-base rounded-lg border @error('nama_toko') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                            required
                        >
                        @error('nama_toko')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
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
                                value="{{ old('alamat', $pengaturan['alamat'] ?? $infoToko['alamat']) }}"
                                class="touch-target w-full px-4 py-2 text-base rounded-lg border @error('alamat') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                                required
                            >
                            @error('alamat')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="telepon" class="block text-base font-medium text-gray-700 mb-1">
                                Telepon
                            </label>
                            <input
                                type="text"
                                id="telepon"
                                name="telepon"
                                value="{{ old('telepon', $pengaturan['telepon'] ?? $infoToko['telepon']) }}"
                                class="touch-target w-full px-4 py-2 text-base rounded-lg border @error('telepon') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                                required
                            >
                            @error('telepon')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
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
                            value="{{ old('header_struk', $pengaturan['header_struk'] ?? $pengaturanStruk['header']) }}"
                            class="touch-target w-full px-4 py-2 text-base rounded-lg border @error('header_struk') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                        >
                        @error('header_struk')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="footer_struk" class="block text-base font-medium text-gray-700 mb-1">
                            Footer Struk
                        </label>
                        <input
                            type="text"
                            id="footer_struk"
                            name="footer_struk"
                            value="{{ old('footer_struk', $pengaturan['footer_struk'] ?? $pengaturanStruk['footer']) }}"
                            class="touch-target w-full px-4 py-2 text-base rounded-lg border @error('footer_struk') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                        >
                        @error('footer_struk')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Kartu 3: WhatsApp Bot (Status Informatif Bersih) --}}
            <section class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        WhatsApp Bot
                    </h2>
                    <p class="text-base text-slate-600 mt-1">
                        Status: <span class="font-semibold text-slate-800">Nonaktif</span> — Integrasi WhatsApp Resmi Belum Dikonfigurasi
                    </p>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Layanan notifikasi otomatis sistem saat ini menggunakan Bot Telegram resmi Toko Firo.
                    </p>
                </div>
                <div>
                    <button
                        type="button"
                        disabled
                        class="touch-target px-5 py-2.5 rounded-lg border border-slate-300 bg-slate-100 text-slate-400 font-medium text-base shadow-none cursor-not-allowed inline-flex items-center justify-center"
                    >
                        Belum Aktif
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
</body>
</html>
