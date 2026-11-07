<nav class="sticky top-0 relative z-50 bg-green-900 border-b border-white/10 h-[66px]">
    <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px] h-full flex items-center justify-between">
        <!-- Left: Logo -->
        <a href="{{ url('/') }}" class="flex items-center outline-none focus:outline-none focus:ring-0" style="outline:none!important;">
            <img src="{{ asset('images/logo.png') }}" alt="Toko Firo" class="h-[40px] w-auto object-contain">
        </a>

        <!-- Center: Nav links -->
        <div class="hidden md:flex items-center gap-4 lg:gap-6">
            <!-- Menu Utama (Kembali ke halaman utama/beranda) -->
            <a href="{{ url('/') }}#beranda" class="text-white hover:text-emerald-300 px-3 py-2 font-semibold outline-none focus:outline-none focus:ring-0" style="outline:none!important;">Beranda</a>
            <a href="{{ url('/') }}#tentang" class="text-white hover:text-emerald-300 px-3 py-2 font-semibold outline-none focus:outline-none focus:ring-0" style="outline:none!important;">Tentang</a>
            <a href="{{ url('/') }}#layanan" class="text-white hover:text-emerald-300 px-3 py-2 font-semibold outline-none focus:outline-none focus:ring-0" style="outline:none!important;">Layanan</a>
            <a href="{{ url('/') }}#info-toko" class="text-white hover:text-emerald-300 px-3 py-2 font-semibold outline-none focus:outline-none focus:ring-0" style="outline:none!important;">Info Toko</a>
        </div>

        <!-- Right: Actions -->
        <div class="hidden md:flex items-center">
            <!-- Tombol Lihat Katalog (Mengarah ke route /katalog) -->
            <a href="{{ route('katalog') }}" class="bg-yellow-400 text-black font-bold px-6 py-2 min-h-[2.75rem] flex items-center justify-center rounded-none hover:bg-yellow-500 transition-colors shadow-sm outline-none focus:outline-none focus:ring-0" style="outline:none!important; box-shadow:none!important;">
                Lihat Katalog
            </a>
        </div>

        <!-- Mobile hamburger button -->
        <div class="md:hidden flex items-center">
            <button id="mobile-menu-btn" type="button" class="touch-target min-h-[2.75rem] text-white text-sm font-semibold px-3 py-2 border border-white/20 hover:bg-white/10 focus:outline-none inline-flex items-center justify-center cursor-pointer rounded-none outline-none focus:ring-0" style="outline:none!important;" aria-label="Menu">
                Menu
            </button>
        </div>
    </div>

    <!-- Mobile menu dropdown -->
    <div id="mobile-menu" class="hidden md:hidden bg-green-900 border-b border-white/10 px-5 py-4 space-y-3">
        <a href="{{ url('/') }}#beranda" class="block text-white hover:text-emerald-300 px-3 py-2 font-semibold outline-none focus:outline-none focus:ring-0" style="outline:none!important;">Beranda</a>
        <a href="{{ url('/') }}#tentang" class="block text-white hover:text-emerald-300 px-3 py-2 font-semibold outline-none focus:outline-none focus:ring-0" style="outline:none!important;">Tentang</a>
        <a href="{{ url('/') }}#layanan" class="block text-white hover:text-emerald-300 px-3 py-2 font-semibold outline-none focus:outline-none focus:ring-0" style="outline:none!important;">Layanan</a>
        <a href="{{ url('/') }}#info-toko" class="block text-white hover:text-emerald-300 px-3 py-2 font-semibold outline-none focus:outline-none focus:ring-0" style="outline:none!important;">Info Toko</a>
        <a href="{{ route('katalog') }}" class="bg-yellow-400 text-black font-bold px-6 py-2 min-h-[2.75rem] flex items-center justify-center rounded-none hover:bg-yellow-500 transition-colors shadow-sm text-center outline-none focus:outline-none focus:ring-0" style="outline:none!important; box-shadow:none!important;">
            Lihat Katalog
        </a>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        if (menu) menu.classList.toggle('hidden');
    });

    document.querySelectorAll('nav a[href*="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const hashIndex = href.indexOf('#');
            if (hashIndex !== -1) {
                const targetHash = href.substring(hashIndex);
                if (window.location.pathname === '/' || window.location.pathname === '') {
                    const targetEl = document.querySelector(targetHash);
                    if (targetEl) {
                        e.preventDefault();
                        targetEl.scrollIntoView({ behavior: 'smooth' });
                        history.pushState(null, '', targetHash);
                        const mobileMenu = document.getElementById('mobile-menu');
                        if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                            mobileMenu.classList.add('hidden');
                        }
                    }
                }
            }
        });
    });
</script>
