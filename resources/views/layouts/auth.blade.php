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
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Panel (Branding) -->
        <div class="hidden lg:flex lg:w-[600px] bg-green-900 text-white flex-col justify-between p-16 shrink-0">
            <div>
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="/logo.png" alt="Toko Firo" class="h-8 w-auto object-contain">
                </div>

                <!-- Lime Accent Bar -->
                <div class="w-[10px] h-[100px] bg-lime rounded-full mt-12 mb-8"></div>

                <!-- Headline -->
                <h1 class="font-heading font-extrabold text-4xl text-white max-w-[400px] leading-tight">Kelola toko lebih rapi</h1>

                <!-- Feature List -->
                <div class="flex flex-col gap-12 mt-12">
                    <div class="flex items-center gap-3 text-white/80 font-medium">
                        <span class="w-2 h-2 rounded-full bg-lime shrink-0"></span>
                        Stok terpantau otomatis
                    </div>
                    <div class="flex items-center gap-3 text-white/80 font-medium">
                        <span class="w-2 h-2 rounded-full bg-lime shrink-0"></span>
                        Saldo harian tercatat rapi
                    </div>
                    <div class="flex items-center gap-3 text-white/80 font-medium">
                        <span class="w-2 h-2 rounded-full bg-lime shrink-0"></span>
                        Notifikasi stok & laporan
                    </div>
                </div>
            </div>

            <div class="text-xs text-white/50">
                &copy; {{ date('Y') }} Toko Firo. Hak cipta dilindungi.
            </div>
        </div>

        <!-- Right Panel (Content) -->
        <div class="flex-1 lg:min-w-[840px] w-full bg-surface flex items-center justify-center p-6 lg:p-12">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
