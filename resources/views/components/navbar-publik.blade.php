<nav class="sticky top-0 z-50 bg-green-900 border-b border-white/10 h-[66px]">
    <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px] h-full flex items-center justify-between">
        <!-- Left: Logo -->
        <a href="/" class="flex items-center">
            <img src="/logo.png" alt="Toko Firo" class="h-[40px] w-auto object-contain">
        </a>

        <!-- Center: Nav links -->
        <div class="hidden md:flex items-center gap-6 lg:gap-8">
            <a href="/" class="text-sm font-medium text-white/70 hover:text-white transition">Beranda</a>
            <a href="/#tentang" class="text-sm font-medium text-white/70 hover:text-white transition">Tentang</a>
            <a href="/#layanan" class="text-sm font-medium text-white/70 hover:text-white transition">Layanan</a>
            <a href="/katalog" class="text-sm font-medium text-white/70 hover:text-white transition">Katalog</a>
            <a href="/#info-toko" class="text-sm font-medium text-white/70 hover:text-white transition">Info Toko</a>
        </div>

        <!-- Right: Actions -->
        <div class="hidden md:flex items-center">
            <a href="/katalog" class="bg-[#FFEE10] text-ink text-sm font-semibold px-5 py-2 rounded-lg hover:bg-[#e6d60e] transition">Lihat Katalog</a>
        </div>

        <!-- Mobile hamburger button -->
        <div class="md:hidden flex items-center">
            <button id="mobile-menu-btn" type="button" class="text-white p-2 rounded-lg hover:bg-white/10 focus:outline-none" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
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
        <a href="/katalog" class="block text-center bg-[#FFEE10] text-ink text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-[#e6d60e] transition">Lihat Katalog</a>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        if (menu) menu.classList.toggle('hidden');
    });
</script>
