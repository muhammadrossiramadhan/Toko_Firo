<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Akun Pengguna - Toko Firo</title>

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
                    Kelola Akun Pengguna
                </h1>
                <p class="text-gray-600 text-base mt-1">
                    Daftar pengguna dan hak akses sistem Toko Firo.
                </p>
            </div>
        </header>

        {{-- Bar Pencarian & Tombol Aksi --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="w-full sm:w-80">
                <input
                    type="text"
                    placeholder="Cari pengguna..."
                    class="touch-target w-full px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
            </div>
            <div>
                <button
                    type="button"
                    onclick="alert('Fitur tambah akun dalam pengembangan.')"
                    class="touch-target px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-base shadow-sm transition-colors flex items-center justify-center whitespace-nowrap"
                >
                    + Tambah Akun
                </button>
            </div>
        </div>

        {{-- Tabel Akun Pengguna --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-base">
                    <thead>
                        <tr class="bg-gray-50 text-gray-700 border-b border-gray-200">
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Nama Lengkap</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Username</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Role</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Status</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Login Terakhir</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-800">
                        @forelse ($pengguna as $item)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        {{-- Inisial Avatar --}}
                                        @php
                                            $bgInisial = match($item->inisial) {
                                                'J' => 'bg-emerald-600',
                                                'R' => 'bg-blue-600',
                                                default => 'bg-slate-500',
                                            };
                                        @endphp
                                        <div class="w-9 h-9 rounded-full {{ $bgInisial }} text-white font-bold flex items-center justify-center text-sm shrink-0">
                                            {{ $item->inisial }}
                                        </div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $item->nama }}
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-mono text-gray-700">
                                    {{ $item->username }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if ($item->role === 'ADMIN')
                                        <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-semibold text-sm">
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold text-sm">
                                            Kasir
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-medium">
                                    @if ($item->status === 'AKTIF')
                                        <span class="text-emerald-600">
                                            &bull; Aktif
                                        </span>
                                    @else
                                        <span class="text-gray-500">
                                            &bull; Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-gray-600">
                                    {{ $item->login_terakhir }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            type="button"
                                            class="touch-target px-3 py-1.5 border border-blue-500 text-blue-600 hover:bg-blue-50 font-medium rounded-lg text-base flex items-center justify-center transition-colors"
                                        >
                                            Ubah
                                        </button>
                                        <button
                                            type="button"
                                            class="touch-target px-3 py-1.5 border border-amber-500 text-amber-600 hover:bg-amber-50 font-medium rounded-lg text-base flex items-center justify-center transition-colors"
                                        >
                                            Reset Password
                                        </button>
                                        @if ($item->status === 'AKTIF')
                                            <button
                                                type="button"
                                                class="touch-target px-3 py-1.5 border border-red-500 text-red-600 hover:bg-red-50 font-medium rounded-lg text-base flex items-center justify-center transition-colors"
                                            >
                                                Nonaktifkan
                                            </button>
                                        @else
                                            <button
                                                type="button"
                                                class="touch-target px-3 py-1.5 border border-emerald-500 text-emerald-600 hover:bg-emerald-50 font-medium rounded-lg text-base flex items-center justify-center transition-colors"
                                            >
                                                Aktifkan
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">
                                    Tidak ada data pengguna terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Tabel --}}
            <div class="p-4 bg-gray-50 border-t border-gray-200 text-gray-600 text-base">
                Menampilkan {{ count($pengguna) }} pengguna terdaftar
            </div>
        </div>
    </main>
</body>
</html>

