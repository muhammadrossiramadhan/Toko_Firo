<footer class="bg-green-900 text-white relative z-40">
    <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px] py-10 lg:py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-6 lg:gap-6">
            <!-- Col 1: Brand & Desc (takes more space via col-span on lg) -->
            <div class="sm:col-span-2 md:col-span-1">
                <div class="flex items-center mb-3">
                    <img src="/logo.png" alt="Toko Firo" class="h-[36px] w-auto object-contain">
                </div>
                <p class="text-white/60 text-sm leading-relaxed max-w-[280px]">
                    Toko ATK dan fotocopy di Kraksaan, Probolinggo. Buka setiap hari.
                </p>
            </div>

            <div class="relative z-50">
                <h3 class="font-bold text-white mb-6 uppercase tracking-wider text-sm">NAVIGASI</h3>
                <div class="flex flex-col gap-4">
                    <a href="{{ url('/') }}#beranda" class="text-emerald-100 hover:text-white transition-colors duration-200 block py-1 cursor-pointer">Beranda</a>
                    <a href="{{ route('katalog') }}" class="text-emerald-100 hover:text-white transition-colors duration-200 block py-1 cursor-pointer">Katalog</a>
                    <a href="{{ route('masuk') }}" class="text-emerald-100 hover:text-white transition-colors duration-200 block py-1 cursor-pointer">Masuk Staf</a>
                </div>
            </div>
            <div class="relative z-50">
                <h3 class="font-bold text-white mb-6 uppercase tracking-wider text-sm">KONTAK</h3>
                <div class="flex flex-col gap-4">
                    <a href="#" class="text-emerald-100 hover:text-white transition-colors duration-200 block py-1 cursor-pointer">WhatsApp Toko</a>
                    <a href="#" class="text-emerald-100 hover:text-white transition-colors duration-200 block py-1 cursor-pointer">Instagram Toko</a>
                    <a href="#" class="text-emerald-100 hover:text-white transition-colors duration-200 block py-1 cursor-pointer">Facebook Toko</a>
                    <a href="#" class="text-emerald-100 hover:text-white transition-colors duration-200 block py-1 cursor-pointer">TikTok Toko</a>
                </div>
            </div>

            <!-- Col 4: Alamat -->
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider mb-3 text-white/80">Alamat</h3>
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
