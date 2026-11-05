@extends('layouts.publik')

@section('title', 'Toko Firo - ATK & Fotocopy Terlengkap')

@section('content')
    {{-- HERO SECTION --}}
    <section id="beranda" class="bg-bg py-16 md:py-20 lg:py-24">
        <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-8 lg:gap-12 items-center">
                {{-- Left Column --}}
                <div>
                    <h1 class="font-heading font-extrabold text-4xl md:text-[36px] md:leading-[44px] lg:text-[40px] lg:leading-[48px] text-ink">
                        Butuh ATK atau fotocopy? Cek stok langsung di sini.
                    </h1>
                    <p class="text-lg text-text-muted mt-6 max-w-[480px] leading-7">
                        Langsung cek stok dan harga dari sini. Diperbarui tiap hari, jadi nggak perlu nanya dulu ke toko.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 mt-8">
                        <a href="/katalog" class="bg-green-700 text-white font-medium px-7 py-3 hover:bg-green-800 transition inline-block text-center">
                            Lihat Katalog
                        </a>
                        <a href="https://wa.me/6285234789169" target="_blank" rel="noopener noreferrer" data-turbo="false" class="border border-green-700 text-green-700 font-medium px-7 py-3.5 hover:bg-green-50 transition inline-block text-center">
                            Hubungi Toko
                        </a>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="hidden sm:flex items-center justify-center">
                    <div class="w-full max-w-[440px] h-[300px] md:h-[340px] lg:h-[400px] overflow-hidden">
                        <img src="/foto-atk.webp" alt="Toko Firo - Alat Tulis Kantor" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TENTANG SECTION --}}
    <section id="tentang" class="bg-surface py-16 md:py-18 lg:py-20">
        <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-10 lg:gap-16 items-center">
                {{-- Left Column (Image Placeholder) --}}
                <div class="flex justify-center md:justify-start">
                    <div class="w-full max-w-[400px] h-[260px] md:h-[280px] lg:h-[320px] overflow-hidden">
                        <img src="/foto-nulis.webp" alt="Foto Toko Firo" class="w-full h-full object-cover">
                    </div>
                </div>

                {{-- Right Column --}}
                <div>
                    <span class="text-xs font-medium text-text-muted uppercase tracking-[0.15em] block">
                        TENTANG TOKO FIRO
                    </span>
                    <h2 class="font-heading font-bold text-3xl md:text-[28px] md:leading-[36px] lg:text-[32px] lg:leading-[40px] text-ink mt-3">
                        Toko ATK & fotocopy langganan warga Kraksaan
                    </h2>
                    <p class="text-text-muted leading-relaxed mt-4 max-w-[440px]">
                        Didirikan Bapak Junaidi Saleh di Jl. Yos Sudarso, Kraksaan. Kami menjual alat tulis, perlengkapan sekolah, kantor, dan jasa fotocopy. Harga pas, pelayanan cepat.
                    </p>
                    <div class="flex flex-row gap-8 mt-8">
                        <div class="text-center">
                            <div class="text-sm text-text-muted">Jenis barang</div>
                            <div class="font-heading font-bold text-2xl text-ink mt-1">100+</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-text-muted">Buka</div>
                            <div class="font-heading font-bold text-2xl text-ink mt-1">Setiap hari</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-text-muted">Fotocopy</div>
                            <div class="font-heading font-bold text-2xl text-ink mt-1">Cepat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- LAYANAN SECTION --}}
    <section id="layanan" class="bg-bg py-16 md:py-18 lg:py-20">
        <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px]">
            <div class="text-center max-w-[640px] mx-auto">
                <span class="text-xs font-medium text-text-muted uppercase tracking-[0.15em] block">
                    LAYANAN
                </span>
                <h2 class="font-heading font-bold text-3xl lg:text-[32px] text-ink mt-3">
                    Yang kami jual dan layani
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-10 md:mt-12 max-w-[1000px] mx-auto">
                {{-- Card 1 --}}
                <div class="bg-surface border border-border p-6 shadow-sm flex items-start gap-4">
                    <div class="w-11 h-11 bg-green-50 flex items-center justify-center text-green-700 font-bold text-base shrink-0">
                        1
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-base text-ink">
                            Alat Tulis Kantor
                        </h3>
                        <p class="text-sm text-text-muted leading-relaxed mt-1">
                            Pulpen, pensil, spidol, penghapus, perlengkapan tulis lengkap.
                        </p>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div class="bg-surface border border-border p-6 shadow-sm flex items-start gap-4">
                    <div class="w-11 h-11 bg-green-50 flex items-center justify-center text-green-700 font-bold text-base shrink-0">
                        2
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-base text-ink">
                            Jasa Fotocopy
                        </h3>
                        <p class="text-sm text-text-muted leading-relaxed mt-1">
                            Hitam putih atau warna, cepat dan rapi.
                        </p>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div class="bg-surface border border-border p-6 shadow-sm flex items-start gap-4">
                    <div class="w-11 h-11 bg-green-50 flex items-center justify-center text-green-700 font-bold text-base shrink-0">
                        3
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-base text-ink">
                            Perlengkapan Sekolah & Kantor
                        </h3>
                        <p class="text-sm text-text-muted leading-relaxed mt-1">
                            Buku tulis, map, stapler, amplop, sampai lakban.
                        </p>
                    </div>
                </div>

                {{-- Card 4 --}}
                <div class="bg-surface border border-border p-6 shadow-sm flex items-start gap-4">
                    <div class="w-11 h-11 bg-green-50 flex items-center justify-center text-green-700 font-bold text-base shrink-0">
                        4
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-base text-ink">
                            Cek Stok Online
                        </h3>
                        <p class="text-sm text-text-muted leading-relaxed mt-1">
                            Cek stok dulu sebelum ke toko lewat katalog kami.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- KENAPA TOKO FIRO SECTION --}}
    <section class="bg-surface py-16 md:py-18 lg:py-20">
        <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px]">
            <div class="text-center max-w-[640px] mx-auto">
                <span class="text-xs font-medium text-text-muted uppercase tracking-[0.15em] block">
                    KENAPA TOKO FIRO
                </span>
                <h2 class="font-heading font-bold text-3xl lg:text-[32px] text-ink mt-3">
                    Kenapa belanja di sini?
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-12 max-w-[1200px] mx-auto">
                {{-- Feature 1 --}}
                <div class="bg-surface border border-border p-6 flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-50 flex items-center justify-center text-green-700 font-bold text-sm shrink-0">
                        A
                    </div>
                    <div>
                        <h3 class="font-medium text-ink">
                            Informasi Stok Terbarukan
                        </h3>
                        <p class="text-sm text-text-muted mt-1 leading-relaxed">
                            Pantau stok produk dengan informasi yang selalu diperbarui.
                        </p>
                    </div>
                </div>

                {{-- Feature 2 --}}
                <div class="bg-surface border border-border p-6 flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-50 flex items-center justify-center text-green-700 font-bold text-sm shrink-0">
                        B
                    </div>
                    <div>
                        <h3 class="font-medium text-ink">
                            Harga Jelas dan Transparan
                        </h3>
                        <p class="text-sm text-text-muted mt-1 leading-relaxed">
                            Nikmati kemudahan berbelanja dengan informasi harga yang jelas.
                        </p>
                    </div>
                </div>

                {{-- Feature 3 --}}
                <div class="bg-surface border border-border p-6 flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-50 flex items-center justify-center text-green-700 font-bold text-sm shrink-0">
                        C
                    </div>
                    <div>
                        <h3 class="font-medium text-ink">
                            Ketersediaan Produk Terjaga
                        </h3>
                        <p class="text-sm text-text-muted mt-1 leading-relaxed">
                            Temukan beragam produk ATK dan fotokopi sesuai kebutuhan Anda.
                        </p>
                    </div>
                </div>

                {{-- Feature 4 --}}
                <div class="bg-surface border border-border p-6 flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-50 flex items-center justify-center text-green-700 font-bold text-sm shrink-0">
                        D
                    </div>
                    <div>
                        <h3 class="font-medium text-ink">
                            Pelayanan Cepat dan Responsif
                        </h3>
                        <p class="text-sm text-text-muted mt-1 leading-relaxed">
                            Dapatkan pelayanan yang cepat, ramah, dan responsif.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- REVIEW SECTION --}}
    <section class="bg-bg py-16 md:py-18 lg:py-20 overflow-hidden">
        <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px]">
            <div class="text-center max-w-[640px] mx-auto">
                <span class="text-xs font-medium text-text-muted uppercase tracking-[0.15em] block">ULASAN PELANGGAN</span>
                <h2 class="font-heading font-bold text-3xl lg:text-[32px] text-ink mt-3">Kata mereka tentang Toko Firo</h2>
                <div class="flex items-center justify-center gap-1.5 mt-3">
                    <span class="px-2 py-0.5 bg-yellow text-ink font-bold text-xs uppercase">Google</span>
                    <span class="text-sm font-semibold text-ink">5.0</span>
                    <span class="text-sm text-text-muted">dari 5 ulasan di Google</span>
                </div>
            </div>
            <div class="mt-10 relative" id="review-carousel">
                <!-- Left arrow - desktop only -->
                <button type="button" id="review-prev" class="hidden md:flex items-center justify-center absolute -left-6 top-1/2 -translate-y-1/2 w-8 h-8 text-text-muted hover:text-ink transition text-xl select-none cursor-pointer z-10" aria-label="Sebelumnya">&#8249;</button>
                <div class="overflow-hidden">
                    <div id="review-track" class="flex transition-transform duration-500 ease-in-out">

                        <div class="flex-shrink-0 w-full sm:w-1/2 md:w-1/3">
                            <div class="border border-border bg-surface p-6 h-full flex flex-col justify-between">
                                <p class="text-sm text-ink leading-relaxed">Terlengkap di daerah Kalibuntu Kraksaan. Mulai dari pengetikan sampai pasang parabola dan CCTV wifi.</p>
                            </div>
                        </div>

                        <div class="flex-shrink-0 w-full sm:w-1/2 md:w-1/3">
                            <div class="border border-border bg-surface p-6 h-full flex flex-col justify-between">
                                <p class="text-sm text-ink leading-relaxed">Tempatnya bagus, semua kebutuhan kantor tersedia. Mantap pokoknya.</p>
                            </div>
                        </div>

                        <div class="flex-shrink-0 w-full sm:w-1/2 md:w-1/3">
                            <div class="border border-border bg-surface p-6 h-full flex flex-col justify-between">
                                <p class="text-sm text-ink leading-relaxed">Pelayanannya baik.</p>
                            </div>
                        </div>

                        <div class="flex-shrink-0 w-full sm:w-1/2 md:w-1/3">
                            <div class="border border-border bg-surface p-6 h-full flex flex-col justify-between">
                                <p class="text-sm text-ink leading-relaxed">Pelayanannya oke, dan lengkap. Toko ATK yang sangat lengkap dengan pelayanan yang memuaskan. Tersedia layanan fotokopi dan print dengan hasil rapi. Prosesnya cepat, harga terjangkau, pelayanannya ramah.</p>
                            </div>
                        </div>

                        <div class="flex-shrink-0 w-full sm:w-1/2 md:w-1/3">
                            <div class="border border-border bg-surface p-6 h-full flex flex-col justify-between">
                                <p class="text-sm text-ink leading-relaxed">Pelayanannya oke banget, sabar banget. Pulpen dicoba-coba pun gapapa.</p>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Right arrow - desktop only -->
                <button type="button" id="review-next" class="hidden md:flex items-center justify-center absolute -right-6 top-1/2 -translate-y-1/2 w-8 h-8 text-text-muted hover:text-ink transition text-xl select-none cursor-pointer z-10" aria-label="Selanjutnya">&#8250;</button>
                <div class="flex justify-center gap-2 mt-6" id="review-dots"></div>
            </div>
        </div>
    </section>

    {{-- INFO TOKO SECTION --}}
    <section id="info-toko" class="bg-surface py-16 md:py-18 lg:py-20">
        <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-8 lg:gap-12 items-start">
                {{-- Left Column --}}
                <div>
                    <span class="text-xs font-medium text-text-muted uppercase tracking-[0.15em] block">
                        INFO TOKO
                    </span>
                    <h2 class="font-heading font-bold text-3xl lg:text-[32px] text-ink mt-3">
                        Kunjungi Toko Firo
                    </h2>
                    <div class="space-y-6 mt-6">
                        <div>
                            <div class="text-xs font-medium text-text-muted uppercase tracking-wider">
                                Alamat
                            </div>
                            <div class="text-text-muted mt-1">
                                <a href="https://maps.app.goo.gl/XH8QjJezgEqBwtkm9" target="_blank" rel="noopener noreferrer" data-turbo="false" class="hover:text-green-700 transition">
                                    Jl. Yos Sudarso No.BA-9, Krajan, Sidopekso, Kec. Kraksaan, Kabupaten Probolinggo, Jawa Timur 67282, Indonesia
                                </a>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-text-muted uppercase tracking-wider">
                                Telepon
                            </div>
                            <div class="mt-1">
                                <a href="tel:+6285234789169" data-turbo="false" class="text-text-muted hover:text-green-700 transition">
                                    +62 852-3478-9169
                                </a>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-text-muted uppercase tracking-wider">
                                Jam Buka
                            </div>
                            <div class="mt-1" id="jam-buka"></div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="https://wa.me/6285234789169" target="_blank" rel="noopener noreferrer" data-turbo="false" class="inline-block bg-green-900 text-white px-6 py-3 hover:bg-green-800 transition font-medium">
                            Hubungi Toko
                        </a>
                    </div>
                </div>

                {{-- Right Column (Map Placeholder) --}}
                <div class="flex justify-center md:justify-end">
                    <div class="w-full max-w-[520px] h-[320px] md:h-[360px] lg:h-[400px] overflow-hidden border border-border">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.37389456605!2d113.42310361182076!3d-7.7501081768125655!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd700fee90c7771%3A0x4b6bdcea4e8fe1f5!2sTOKO%20FIRO!5e0!3m2!1sen!2sus!4v1791613296253!5m2!1sen!2sus"
                            width="100%"
                            height="100%"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- BANNER CTA SECTION --}}
    <section class="bg-green-900 py-12">
        <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px]">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
                <div>
                    <h2 class="font-heading font-bold text-2xl text-white">
                        Mau tau stok sebelum ke toko?
                    </h2>
                    <p class="text-white/70 mt-2">
                        Buka katalog, cek langsung.
                    </p>
                </div>
                <div>
                    <a href="/katalog" class="bg-green-700 text-white font-medium px-7 py-3 hover:bg-green-800 transition shrink-0 inline-block text-center">
                        Lihat Katalog
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
// Review carousel - auto-rotate, infinite loop
(function() {
    const track = document.getElementById('review-track');
    if (!track) return;
    const cards = Array.from(track.children);
    const total = cards.length;
    if (total === 0) return;

    // Clone first 3 cards to end and last 3 to start for infinite loop
    cards.slice(0, 3).forEach(c => track.appendChild(c.cloneNode(true)));
    const firstCard = track.firstChild;
    cards.slice(-3).forEach(c => track.insertBefore(c.cloneNode(true), firstCard));

    let index = 3; // start at first real card (after 3 clones prepended)
    let perView = 3;

    const dotsContainer = document.getElementById('review-dots');
    function updateDots(realIdx) {
        if (!dotsContainer) return;
        Array.from(dotsContainer.children).forEach((dot, idx) => {
            if (idx === realIdx) {
                dot.className = 'w-6 h-2 bg-green-700 transition-all duration-300';
            } else {
                dot.className = 'w-2 h-2 bg-green-700/30 hover:bg-green-700/50 transition-all duration-300';
            }
        });
    }

    if (dotsContainer) {
        dotsContainer.innerHTML = '';
        for (let d = 0; d < total; d++) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'w-2 h-2 bg-green-700/30 hover:bg-green-700/50 transition-all duration-300';
            dot.setAttribute('aria-label', `Ulasan ${d + 1}`);
            dot.addEventListener('click', () => {
                slideTo(3 + d);
            });
            dotsContainer.appendChild(dot);
        }
    }

    function getPerView() {
        if (window.innerWidth < 640) return 1;
        if (window.innerWidth < 768) return 2;
        return 3;
    }

    function updateSizes() {
        perView = getPerView();
        const allCards = track.children;
        const gap = perView === 1 ? 0 : 12;
        for (let i = 0; i < allCards.length; i++) {
            allCards[i].style.width = (100 / perView) + '%';
            allCards[i].style.flexShrink = '0';
            allCards[i].style.paddingLeft = '0';
            allCards[i].style.paddingRight = gap + 'px';
            allCards[i].style.boxSizing = 'border-box';
        }
        track.style.transition = 'none';
        track.style.transform = `translateX(-${index * (100 / perView)}%)`;
    }

    function slideTo(i, animate = true) {
        index = i;
        track.style.transition = animate ? 'transform 500ms ease-in-out' : 'none';
        track.style.transform = `translateX(-${index * (100 / perView)}%)`;
        const realIdx = ((index - 3) % total + total) % total;
        updateDots(realIdx);
    }

    // After transition ends, check if we need to jump (infinite loop)
    track.addEventListener('transitionend', () => {
        // If we scrolled past the real cards into the end clones
        if (index >= total + 3) {
            index = 3;
            track.style.transition = 'none';
            track.style.transform = `translateX(-${index * (100 / perView)}%)`;
        }
        // If we scrolled before the real cards into the start clones
        if (index < 3) {
            index = total + 3 - 1;
            track.style.transition = 'none';
            track.style.transform = `translateX(-${index * (100 / perView)}%)`;
        }
    });

    // Auto-advance every 4 seconds
    let timer = setInterval(() => slideTo(index + 1), 4000);

    // Pause on hover
    const carousel = document.getElementById('review-carousel');
    if (carousel) {
        carousel.addEventListener('mouseenter', () => clearInterval(timer));
        carousel.addEventListener('mouseleave', () => {
            timer = setInterval(() => slideTo(index + 1), 4000);
        });
    }

    // Responsive
    window.addEventListener('resize', () => {
        updateSizes();
    });

    updateSizes();
    // Initial position without animation
    requestAnimationFrame(() => {
        slideTo(3, false);
    });

    // Desktop arrow nav
    const prevBtn = document.getElementById('review-prev');
    const nextBtn = document.getElementById('review-next');
    if (prevBtn) prevBtn.addEventListener('click', () => { clearInterval(timer); slideTo(index - 1); timer = setInterval(() => slideTo(index + 1), 4000); });
    if (nextBtn) nextBtn.addEventListener('click', () => { clearInterval(timer); slideTo(index + 1); timer = setInterval(() => slideTo(index + 1), 4000); });

    // Touch swipe support
    let touchStartX = 0;
    let touchStartY = 0;
    let isSwiping = false;
    const overflowEl = track.parentElement;
    overflowEl.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        isSwiping = false;
    }, { passive: true });
    overflowEl.addEventListener('touchmove', (e) => {
        if (!touchStartX) return;
        const dx = Math.abs(e.touches[0].clientX - touchStartX);
        const dy = Math.abs(e.touches[0].clientY - touchStartY);
        if (dx > dy && dx > 10) {
            isSwiping = true;
            e.preventDefault();
        }
    }, { passive: false });
    overflowEl.addEventListener('touchend', (e) => {
        if (!isSwiping) return;
        const touchEndX = e.changedTouches[0].clientX;
        const diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 30) {
            clearInterval(timer);
            slideTo(diff > 0 ? index + 1 : index - 1);
            timer = setInterval(() => slideTo(index + 1), 4000);
        }
        touchStartX = 0;
        isSwiping = false;
    });
})();

// Show today's operating hours only
(function() {
    var el = document.getElementById('jam-buka');
    if (!el) return;
    var now = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Jakarta' }));
    var day = now.getDay();
    var names = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    var jam = day === 0 ? '07.00 - 21.00' : '06.30 - 21.00';
    el.innerHTML = '<span class="text-ink font-medium">' + names[day] + ', ' + jam + ' WIB</span>';
})();
</script>
@endpush
