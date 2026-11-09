<style>
    .footer-logo-wrapper {
        margin-top: 0;
    }
    .footer-logo {
        height: 3.5rem; /* 56px (h-14) */
        max-height: 64px;
        width: auto;
    }
    @media (min-width: 768px) {
        .footer-logo {
            height: 4rem; /* 64px (h-16) */
            max-height: 64px;
        }
    }
</style>

<footer class="bg-green-900 text-white relative z-40">
    <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px] pt-4 pb-10 md:pt-6 md:pb-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-6 lg:gap-6">
            <!-- Col 1: Brand & Desc (takes more space via col-span on lg) -->
            <div class="sm:col-span-2 md:col-span-1">
                <div class="flex items-center mb-3 md:mb-4 footer-logo-wrapper h-14 md:h-16">
                    <img src="{{ asset('images/logo-footer.png') }}" 
                         alt="Toko Firo" 
                         class="footer-logo object-contain h-14 md:h-16 w-auto" 
                         style="height: auto; max-height: 64px; width: auto;">
                </div>
                <p class="text-white/60 text-sm leading-relaxed max-w-[280px] mt-3 md:mt-4">
                    Toko ATK dan fotocopy di Kraksaan, Probolinggo. Buka setiap hari.
                </p>
            </div>

            <!-- KOLOM NAVIGASI -->
            <div class="flex flex-col gap-1.5 items-start justify-start h-fit">
                <h3 class="font-bold text-white mb-3 uppercase tracking-wider text-sm">NAVIGASI</h3>
                <a href="{{ url('/') }}#beranda" onclick="event.preventDefault(); window.location.href=this.href;" class="text-white/60 hover:text-white block py-1 cursor-pointer relative z-50">Beranda</a>
                <a href="{{ url('/katalog') }}" onclick="event.preventDefault(); window.location.href=this.href;" class="text-white/60 hover:text-white block py-1 cursor-pointer relative z-50">Katalog</a>
                <a href="{{ url('/masuk') }}" onclick="event.preventDefault(); window.location.href=this.href;" class="text-white/60 hover:text-white block py-1 cursor-pointer relative z-50">Masuk Staf</a>
            </div>

            <!-- KOLOM KONTAK -->
            <div class="flex flex-col gap-1.5 items-start justify-start h-fit">
                <h3 class="font-bold text-white mb-3 uppercase tracking-wider text-sm">KONTAK</h3>
                <a href="https://wa.me/6285234789169" target="_blank" rel="noopener noreferrer" class="text-white/60 hover:text-white block relative z-50">WhatsApp Toko</a>
                <a href="https://www.instagram.com/firotoko?stkn=bHpxMDF2d3ZodDFt" target="_blank" rel="noopener noreferrer" class="text-white/60 hover:text-white block relative z-50">Instagram Toko</a>
                <a href="https://www.facebook.com/people/Toko-Firo/100063668996154/" target="_blank" rel="noopener noreferrer" class="text-white/60 hover:text-white block relative z-50">Facebook Toko</a>
                <a href="https://www.tiktok.com/@tokofiro0?_r=1&_t=ZS-99pMK6mNYGb" target="_blank" rel="noopener noreferrer" class="text-white/60 hover:text-white block relative z-50">TikTok Toko</a>
            </div>

            <!-- Col 4: Alamat -->
            <div>
                <h3 class="font-bold text-white mb-3 uppercase tracking-wider text-sm">ALAMAT</h3>
                <p class="text-white/60 text-sm leading-relaxed">
                    Jl. Yos Sudarso No.BA-9, Krajan, Sidopekso, Kec. Kraksaan, Kabupaten Probolinggo, Jawa Timur 67282
                </p>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="border-t border-white/10 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-xs text-white/40">&copy; {{ date('Y') }} Toko Firo. Hak cipta dilindungi.</p>
            <p class="text-xs text-white/40">Kraksaan, Probolinggo</p>
        </div>
    </div>
</footer>
