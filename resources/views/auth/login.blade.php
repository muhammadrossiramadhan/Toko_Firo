<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Toko Firo</title>

    {{-- Tailwind CSS CDN untuk preview mandiri tanpa build step, fallback jika Vite belum dijalankan --}}
    <script src="https://cdn.tailwindcss.com"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        /* Aturan tampilan: Font minimal 16px dan tinggi elemen interaktif minimal 44px */
        html, body {
            font-size: 16px;
        }
        .touch-target {
            min-height: 44px;
            min-width: 44px;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 text-base antialiased min-h-screen flex items-center justify-center p-4">

    <main class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
        {{-- Header Identitas (Tanpa Ikon atau Emoji) --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-600 text-white font-bold text-xl mb-3">
                F
            </div>
            <h1 class="text-2xl font-bold text-gray-900">
                Toko Firo
            </h1>
            <p class="text-gray-600 mt-1 text-base">
                Silakan masuk untuk mengelola data toko
            </p>
        </div>

        {{-- Pesan Error Global / Autentikasi --}}
        @if ($errors->has('login'))
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-base" role="alert">
                {{ $errors->first('login') }}
            </div>
        @endif

        {{-- Form Login --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="username" class="block text-base font-medium text-gray-700 mb-1">
                    Username
                </label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="touch-target w-full px-4 py-2 text-base rounded-xl border @error('username') border-red-300 bg-red-50 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                >
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-base font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    autocomplete="current-password"
                    class="touch-target w-full px-4 py-2 text-base rounded-xl border @error('password') border-red-300 bg-red-50 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="touch-target w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-base transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2"
                >
                    Masuk
                </button>
            </div>
        </form>
    </main>

</body>
</html>

