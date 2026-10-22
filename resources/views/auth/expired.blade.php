<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesi Kedaluwarsa - Toko Firo</title>

    {{-- Tailwind CSS CDN untuk preview mandiri tanpa build step, fallback jika Vite belum dijalankan --}}
    <script src="https://cdn.tailwindcss.com"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        /* Aturan tampilan K18: Font minimal 16px dan tinggi elemen interaktif minimal 44px */
        html, body {
            font-size: 16px;
        }
        .touch-target {
            min-height: 44px;
            min-width: 44px;
        }
    </style>

    @include('partials.skala')
</head>
<body class="bg-gray-100 text-gray-800 text-base antialiased min-h-screen flex items-center justify-center p-4">

    <main class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
        {{-- Header Identitas (Tanpa Ikon Grafis atau Emoji Sesuai Aturan K12) --}}
        <div class="mb-5">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-600 text-white font-bold text-xl mb-3">
                F
            </div>
            <div class="text-2xl font-bold text-gray-900">
                Toko Firo
            </div>
        </div>

        {{-- Badge Status Teks Murni --}}
        <div class="inline-flex items-center px-3.5 py-1.5 rounded-full text-sm font-semibold bg-amber-50 text-amber-800 border border-amber-200 mb-4">
            Sesi Telah Berakhir
        </div>

        {{-- Judul Halaman --}}
        <h1 class="text-xl font-bold text-gray-900 mb-3">
            Waktu Masuk Kedaluwarsa
        </h1>

        {{-- Penjelasan Keamanan Akun --}}
        <p class="text-gray-600 text-base leading-relaxed mb-6">
            Sesi aktif Anda telah berakhir karena tidak ada aktivitas dalam waktu tertentu atau token keamanan kedaluwarsa. Silakan masuk kembali untuk melanjutkan pengelolaan data toko demi menjaga keamanan akun Anda.
        </p>

        {{-- Tombol Aksi Kepatuhan K18 --}}
        <div class="space-y-3">
            <a
                href="{{ route('login') }}"
                class="touch-target flex items-center justify-center w-full px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-base shadow-sm transition-colors text-center"
            >
                Masuk Kembali
            </a>

            <a
                href="{{ url('/') }}"
                class="touch-target flex items-center justify-center w-full px-5 py-3 rounded-xl border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium text-base transition-colors text-center"
            >
                Kembali ke Beranda
            </a>
        </div>
    </main>

</body>
</html>

