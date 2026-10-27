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

        <div class="space-y-6 max-w-4xl">
            {{-- Kartu 1: Ringkasan Akun (Header Profil) --}}
            <section class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex items-center space-x-5">
                <div class="w-16 h-16 rounded-full bg-emerald-700 text-white font-bold text-2xl flex items-center justify-center shrink-0">
                    {{ $profil['inisial'] ?? 'AF' }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $profil['nama'] ?? 'Ahmad Firo' }}
                    </h2>
                    <p class="text-slate-500 text-base">
                        {{ $profil['role'] ?? 'Admin / Pemilik' }}
                    </p>
                </div>
            </section>

            {{-- Kartu 2: Form Edit Profil --}}
            <section class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    Edit Profil
                </h2>

                <form method="POST" action="{{ route('admin.profil.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nama" class="block text-base font-medium text-gray-700 mb-1">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                id="nama"
                                name="nama"
                                value="{{ old('nama', $pengguna->nama ?? $profil['nama']) }}"
                                class="touch-target w-full px-4 py-2 text-base rounded-lg border @error('nama') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                                required
                            >
                            @error('nama')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="username" class="block text-base font-medium text-gray-700 mb-1">
                                Username
                            </label>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="{{ old('username', $pengguna->username ?? $profil['username']) }}"
                                class="touch-target w-full px-4 py-2 text-base rounded-lg border @error('username') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                                required
                            >
                            @error('username')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="telepon" class="block text-base font-medium text-gray-700 mb-1">
                            Nomor Telepon
                        </label>
                        <input
                            type="text"
                            id="telepon"
                            name="nomor_telepon"
                            value="{{ old('nomor_telepon', old('telepon', $pengguna->nomor_telepon ?? $pengguna->telepon ?? $profil['telepon'])) }}"
                            class="touch-target w-full px-4 py-2 text-base rounded-lg border @error('nomor_telepon') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                        >
                        @error('nomor_telepon')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="touch-target bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-3 rounded-lg text-base shadow-sm transition-colors inline-flex items-center justify-center cursor-pointer"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </section>

            {{-- Kartu 3: Form Ganti Password --}}
            <section class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    Ganti Password
                </h2>

                <form method="POST" action="{{ route('admin.profil.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="password_lama" class="block text-base font-medium text-gray-700 mb-1">
                            Password Lama
                        </label>
                        <input
                            type="password"
                            id="password_lama"
                            name="password_lama"
                            placeholder="Masukkan password saat ini"
                            class="touch-target w-full px-4 py-2 text-base rounded-lg border @error('password_lama') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                            required
                        >
                        @error('password_lama')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
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
                                placeholder="Minimal 6 karakter"
                                class="touch-target w-full px-4 py-2 text-base rounded-lg border @error('password_baru') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                                required
                            >
                            @error('password_baru')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_baru_confirmation" class="block text-base font-medium text-gray-700 mb-1">
                                Konfirmasi Password
                            </label>
                            <input
                                type="password"
                                id="password_baru_confirmation"
                                name="password_baru_confirmation"
                                placeholder="Ulangi password baru"
                                class="touch-target w-full px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                                required
                            >
                        </div>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="touch-target bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-3 rounded-lg text-base shadow-sm transition-colors inline-flex items-center justify-center cursor-pointer"
                        >
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
