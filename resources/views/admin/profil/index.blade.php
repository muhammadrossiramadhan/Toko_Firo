<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Admin - Toko Firo</title>

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
    <div id="notif-feedback" class="hidden fixed top-6 right-8 bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg text-base font-medium z-50 transition-opacity">
        Perubahan profil berhasil disimpan (prototype).
    </div>

    {{-- Sidebar Navigasi Admin Terpusat --}}
    @include('partials.sidebar_admin')

    {{-- Area Konten Utama --}}
    <main class="flex-1 min-w-0 p-8">
        {{-- Header Halaman --}}
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Profil Saya
            </h1>
            <p class="text-slate-500 text-base mt-1">
                Kelola informasi akun pengguna, kredensial, dan keamanan profil Admin Toko Firo.
            </p>
        </header>

        <form onsubmit="simpanProfil(event)" class="space-y-6 max-w-4xl">
            {{-- Kartu 1: Ringkasan Akun (Header Profil) --}}
            <section class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex items-center space-x-5">
                <div class="w-16 h-16 rounded-full bg-emerald-700 text-white font-bold text-2xl flex items-center justify-center shrink-0">
                    {{ $profil['inisial'] }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $profil['nama'] }}
                    </h2>
                    <p class="text-slate-500 text-base">
                        {{ $profil['role'] }}
                    </p>
                </div>
            </section>

            {{-- Kartu 2: Edit Profil --}}
            <section class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    Edit Profil
                </h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nama" class="block text-base font-medium text-gray-700 mb-1">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                id="nama"
                                name="nama"
                                value="{{ $profil['nama'] }}"
                                class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                            >
                        </div>
                        <div>
                            <label for="username" class="block text-base font-medium text-gray-700 mb-1">
                                Username
                            </label>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="{{ $profil['username'] }}"
                                class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="telepon" class="block text-base font-medium text-gray-700 mb-1">
                            Nomor Telepon
                        </label>
                        <input
                            type="text"
                            id="telepon"
                            name="telepon"
                            value="{{ $profil['telepon'] }}"
                            class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                        >
                    </div>
                </div>
            </section>

            {{-- Kartu 3: Ganti Password --}}
            <section class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    Ganti Password
                </h2>
                <div class="space-y-4">
                    <div>
                        <label for="password_lama" class="block text-base font-medium text-gray-700 mb-1">
                            Password Lama
                        </label>
                        <input
                            type="password"
                            id="password_lama"
                            name="password_lama"
                            value="********"
                            placeholder="••••••••"
                            class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password_baru" class="block text-base font-medium text-gray-700 mb-1">
                                Password Baru
                            </label>
                            <input
                                type="password"
                                id="password_baru"
                                name="password_baru"
                                placeholder="Masukkan password baru"
                                class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                            >
                        </div>
                        <div>
                            <label for="konfirmasi_password" class="block text-base font-medium text-gray-700 mb-1">
                                Konfirmasi Password
                            </label>
                            <input
                                type="password"
                                id="konfirmasi_password"
                                name="konfirmasi_password"
                                placeholder="Ulangi password baru"
                                class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                            >
                        </div>
                    </div>
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

        function simpanProfil(e) {
            e.preventDefault();
            tampilkanNotif('Perubahan profil berhasil disimpan (prototype).');
        }
    </script>
</body>
</html>

