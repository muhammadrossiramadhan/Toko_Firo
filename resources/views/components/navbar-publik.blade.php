<nav class="sticky top-0 z-50 bg-green-900 border-b border-white/10 h-[66px]">
    <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px] h-full flex items-center justify-between">
        <!-- Left: Logo -->
        <a href="/" class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="Toko Firo" class="h-[40px] w-auto object-contain">
        </a>

        <!-- Center: Nav links -->
        <div class="hidden md:flex items-center gap-6 lg:gap-8">
            <a href="/" class="touch-target min-h-[2.75rem] inline-flex items-center text-sm font-medium text-white/70 hover:text-white transition">Beranda</a>
            <a href="/#tentang" class="touch-target min-h-[2.75rem] inline-flex items-center text-sm font-medium text-white/70 hover:text-white transition">Tentang</a>
            <a href="/#layanan" class="touch-target min-h-[2.75rem] inline-flex items-center text-sm font-medium text-white/70 hover:text-white transition">Layanan</a>
            <a href="/katalog" class="touch-target min-h-[2.75rem] inline-flex items-center text-sm font-medium text-white/70 hover:text-white transition">Katalog</a>
            <a href="/#info-toko" class="touch-target min-h-[2.75rem] inline-flex items-center text-sm font-medium text-white/70 hover:text-white transition">Info Toko</a>
        </div>

        <!-- Right: Actions -->
        <div class="hidden md:flex items-center">
            <a href="/katalog" class="touch-target min-h-[2.75rem] inline-flex items-center bg-[#FFEE10] text-ink text-sm font-semibold px-5 py-2 hover:bg-[#e6d60e] transition">Lihat Katalog</a>
        </div>

        <!-- Mobile hamburger button -->
        <div class="md:hidden flex items-center">
            <button id="mobile-menu-btn" type="button" class="touch-target min-h-[2.75rem] text-white text-sm font-semibold px-3 py-2 border border-white/20 hover:bg-white/10 focus:outline-none inline-flex items-center justify-center cursor-pointer" aria-label="Menu">
                Menu
            </button>
        </div>
    </div>

    <!-- Mobile menu dropdown -->
    <div id="mobile-menu" class="hidden md:hidden bg-green-900 border-b border-white/10 px-5 py-4 space-y-3">
        <a href="/" class="block py-2 text-sm font-medium text-white/70 hover:text-white">Beranda</a>
        <a href="/#tentang" class="block py-2 text-sm font-medium text-white/70 hover:text-white">Tentang</a>
        <a href="/#layanan" class="block py-2 text-sm font-medium text-white/70 hover:text-white">Layanan</a>
        <a href="/katalog" class="block py-2 text-sm font-medium text-white/70 hover:text-white">Katalog</a>
        <a href="/#info-toko" class="block py-2 text-sm font-medium text-white/70 hover:text-white">Info Toko</a>
        <a href="/katalog" class="block text-center bg-[#FFEE10] text-ink text-sm font-semibold px-5 py-2.5 hover:bg-[#e6d60e] transition">Lihat Katalog</a>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        if (menu) menu.classList.toggle('hidden');
    });
</script>
