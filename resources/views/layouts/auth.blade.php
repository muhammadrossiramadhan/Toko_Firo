<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Toko Firo')</title>
    <meta name="description" content="@yield('meta_description', 'Toko Firo - Masuk Staf')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-surface text-ink antialiased min-h-screen">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Left Panel (Branding) -->
        <div class="hidden md:flex md:w-[45%] lg:w-[50%] max-w-[600px] bg-green-900 text-white flex-col justify-between p-10 lg:p-16 shrink-0">
            <div>
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="/logo.png" alt="Toko Firo" class="h-8 w-auto object-contain">
                </div>

                <!-- Headline -->
                <h1 class="font-heading font-extrabold text-4xl text-white max-w-[400px] leading-tight mt-12">Kelola toko lebih rapi</h1>

                <!-- Feature List -->
                <div class="flex flex-col gap-4 mt-12">
                    <div class="flex items-start gap-3 text-white/80">
                        <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Stok terpantau otomatis
                    </div>
                    <div class="flex items-start gap-3 text-white/80">
                        <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Saldo harian tercatat rapi
                    </div>
                    <div class="flex items-start gap-3 text-white/80">
                        <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Notifikasi stok via Telegram
                    </div>
                </div>
            </div>

            <div class="text-xs text-white/50">
                &copy; {{ date('Y') }} Toko Firo. Hak cipta dilindungi.
            </div>
        </div>

        <!-- Right Panel (Content) -->
        <div class="flex-1 w-full bg-surface flex items-center justify-center p-6 md:p-10 lg:p-12">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
