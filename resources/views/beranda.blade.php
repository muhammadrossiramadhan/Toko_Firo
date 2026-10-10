@extends('layouts.publik')

@section('title', 'Toko Firo - ATK & Fotocopy Terlengkap')

@section('content')
    {{-- HERO SECTION --}}
    <section id="beranda" class="bg-bg py-16 lg:py-24">
        <div class="max-w-[1280px] mx-auto px-5 lg:px-[80px]">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                {{-- Left Column --}}
                <div>
                    <h1 class="font-heading font-extrabold text-4xl lg:text-[40px] lg:leading-[48px] text-ink">
                        Butuh ATK atau fotocopy? Cek stok langsung di sini.
                    </h1>
                    <p class="text-lg text-text-muted mt-6 max-w-[480px] leading-7">
                        Langsung cek stok dan harga dari sini. Diperbarui tiap hari, jadi nggak perlu nanya dulu ke toko.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 mt-8">
                        <a href="/katalog" class="bg-green-700 text-white font-medium px-7 py-3 rounded-lg hover:bg-green-800 transition inline-block text-center">
                            Lihat Katalog
                        </a>
                        <a href="https://wa.me/6285234789169" target="_blank" rel="noopener noreferrer" class="border border-green-700 text-green-700 font-medium px-7 py-3.5 rounded-lg hover:bg-green-50 transition inline-block text-center">
                            Hubungi Toko
                        </a>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="hidden sm:flex items-center justify-center">
                    <div class="w-full max-w-[440px] h-[340px] lg:h-[400px] rounded-2xl overflow-hidden">
                        <img src="/foto1.jpeg" alt="Toko Firo" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TENTANG SECTION --}}
    <section id="tentang" class="bg-surface py-16 lg:py-20">
        <div class="max-w-[1280px] mx-auto px-5 lg:px-[80px]">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                {{-- Left Column (Image Placeholder) --}}
                <div class="flex justify-center lg:justify-start">
                    <div class="w-full max-w-[400px] h-[260px] lg:h-[320px] rounded-xl overflow-hidden">
                        <img src="/foto2.jpeg" alt="Foto Toko Firo" class="w-full h-full object-cover">
                    </div>
                </div>

                {{-- Right Column --}}
                <div>
                    <span class="text-xs font-medium text-text-muted uppercase tracking-[0.15em] block">
                        TENTANG TOKO FIRO
                    </span>
                    <h2 class="font-heading font-bold text-3xl lg:text-[32px] lg:leading-[40px] text-ink mt-3">
                        Toko ATK & fotocopy langganan warga Kraksaan
                    </h2>
                    <p class="text-text-muted leading-relaxed mt-4 max-w-[440px]">
                        Didirikan Bapak Junaidi Saleh di Jl. Yos Sudarso, Kraksaan. Kami jual alat tulis, perlengkapan sekolah, kantor, dan terima fotocopy. Harga pas, pelayanan cepat.
                    </p>
                    <div class="flex gap-8 mt-8">
                        <div>
                            <div class="font-heading font-bold text-2xl text-ink">100+</div>
                            <div class="text-sm text-text-muted mt-1">Jenis barang</div>
                        </div>
                        <div>
                            <div class="font-heading font-bold text-2xl text-ink">Setiap hari</div>
                            <div class="text-sm text-text-muted mt-1">Buka</div>
                        </div>
                        <div>
                            <div class="font-heading font-bold text-2xl text-ink">Cepat</div>
                            <div class="text-sm text-text-muted mt-1">Fotocopy</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- LAYANAN SECTION --}}
    <section id="layanan" class="bg-bg py-16 lg:py-20">
        <div class="max-w-[1280px] mx-auto px-5 lg:px-[80px]">
            <div class="text-center max-w-[640px] mx-auto">
                <span class="text-xs font-medium text-text-muted uppercase tracking-[0.15em] text-center block">
                    LAYANAN
                </span>
                <h2 class="font-heading font-bold text-3xl lg:text-[32px] text-ink mt-3 text-center">
                    Yang kami jual dan layani
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-12">
                {{-- Card 1 --}}
                <div class="bg-surface border border-border rounded-xl p-6 shadow-sm flex items-start gap-4">
                    <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                        </svg>
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
                <div class="bg-surface border border-border rounded-xl p-6 shadow-sm flex items-start gap-4">
                    <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                        </svg>
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
                <div class="bg-surface border border-border rounded-xl p-6 shadow-sm flex items-start gap-4">
                    <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
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
                <div class="bg-surface border border-border rounded-xl p-6 shadow-sm flex items-start gap-4">
                    <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
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
    <section class="bg-surface py-16 lg:py-20">
        <div class="max-w-[1280px] mx-auto px-5 lg:px-[80px]">
            <div class="text-center max-w-[640px] mx-auto">
                <span class="text-xs font-medium text-text-muted uppercase tracking-[0.15em] block">
                    KENAPA TOKO FIRO
                </span>
                <h2 class="font-heading font-bold text-3xl lg:text-[32px] text-ink mt-3">
                    Kenapa belanja di sini?
                </h2>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-12 max-w-[1200px] mx-auto">
                {{-- Feature 1 --}}
                <div class="bg-surface border border-border rounded-xl p-6 flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-ink">
                            Stok langsung keliatan
                        </h3>
                        <p class="text-sm text-text-muted mt-1 leading-relaxed">
                            Stok langsung berubah tiap ada transaksi masuk.
                        </p>
                    </div>
                </div>

                {{-- Feature 2 --}}
                <div class="bg-surface border border-border rounded-xl p-6 flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.386a11.025 11.025 0 003.882-3.882c.486-.827.313-1.908-.386-2.607L9.76 3.659A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-ink">
                            Harga transparan
                        </h3>
                        <p class="text-sm text-text-muted mt-1 leading-relaxed">
                            Harga yang tertulis di katalog ya segitu, nggak ada tambahan.
                        </p>
                    </div>
                </div>

                {{-- Feature 3 --}}
                <div class="bg-surface border border-border rounded-xl p-6 flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-ink">
                            Barang baru rutin masuk
                        </h3>
                        <p class="text-sm text-text-muted mt-1 leading-relaxed">
                            Kami restok rutin, apalagi barang yang sering dicari.
                        </p>
                    </div>
                </div>

                {{-- Feature 4 --}}
                <div class="bg-surface border border-border rounded-xl p-6 flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-ink">
                            Pelayanan cepat
                        </h3>
                        <p class="text-sm text-text-muted mt-1 leading-relaxed">
                            Nggak usah nunggu lama, langsung kami siapin.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- REVIEW SECTION --}}
    <section class="bg-bg py-16 lg:py-20 overflow-hidden">
        <div class="max-w-[1280px] mx-auto px-5 lg:px-[80px]">
            <div class="text-center max-w-[640px] mx-auto">
                <span class="text-xs font-medium text-text-muted uppercase tracking-[0.15em] block">ULASAN PELANGGAN</span>
                <h2 class="font-heading font-bold text-3xl lg:text-[32px] text-ink mt-3">Kata mereka tentang Toko Firo</h2>
                <div class="flex items-center justify-center gap-1.5 mt-3">
                    <svg class="w-5 h-5 text-[#FFEE10]" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <span class="text-sm font-semibold text-ink">5.0</span>
                    <span class="text-sm text-text-muted">dari 5 ulasan di Google</span>
                </div>
            </div>
            <div class="mt-10 relative" id="review-carousel">
                <!-- Left arrow - desktop only -->
                <button type="button" id="review-prev" class="hidden lg:flex items-center justify-center absolute -left-6 top-1/2 -translate-y-1/2 w-8 h-8 text-text-muted hover:text-ink transition text-xl select-none cursor-pointer z-10" aria-label="Sebelumnya">&#8249;</button>
                <div class="overflow-hidden">
                    <div id="review-track" class="flex transition-transform duration-500 ease-in-out">

                        <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/3">
                            <div class="bg-surface rounded-xl p-5 h-full border border-border">
                                <p class="text-sm text-ink leading-relaxed">Pelayanannya oke, dan lengkap. Toko ATK yang sangat lengkap dengan pelayanan yang memuaskan. Tersedia layanan fotokopi dan print dengan hasil rapi. Prosesnya cepat, harga terjangkau, pelayanannya ramah.</p>
                                <div class="mt-4 pt-3 border-t border-border">
                                    <div class="text-sm font-medium text-ink">Bryan Kurniawan</div>
                                    <div class="text-xs text-text-muted">3 bulan lalu</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/3">
                            <div class="bg-surface rounded-xl p-5 h-full border border-border">
                                <p class="text-sm text-ink leading-relaxed">Pelayanannya oke banget, sabar banget. Pulpen dicoba-coba pun gapapa.</p>
                                <div class="mt-4 pt-3 border-t border-border">
                                    <div class="text-sm font-medium text-ink">Nana Eyyoo</div>
                                    <div class="text-xs text-text-muted">3 tahun lalu</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/3">
                            <div class="bg-surface rounded-xl p-5 h-full border border-border">
                                <p class="text-sm text-ink leading-relaxed">Terlengkap di daerah Kalibuntu Kraksaan. Mulai dari pengetikan sampai pasang parabola dan CCTV wifi.</p>
                                <div class="mt-4 pt-3 border-t border-border">
                                    <div class="text-sm font-medium text-ink">Jefril Abdul Ro'uf</div>
                                    <div class="text-xs text-text-muted">4 tahun lalu</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/3">
                            <div class="bg-surface rounded-xl p-5 h-full border border-border">
                                <p class="text-sm text-ink leading-relaxed">Tempatnya bagus, semua kebutuhan kantor tersedia. Mantap pokoknya.</p>
                                <div class="mt-4 pt-3 border-t border-border">
                                    <div class="text-sm font-medium text-ink">Andry Syahrizal</div>
                                    <div class="text-xs text-text-muted">7 tahun lalu</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/3">
                            <div class="bg-surface rounded-xl p-5 h-full border border-border">
                                <p class="text-sm text-ink leading-relaxed">Pelayanannya baik.</p>
                                <div class="mt-4 pt-3 border-t border-border">
                                    <div class="text-sm font-medium text-ink">Sulaiman Rhosyid</div>
                                    <div class="text-xs text-text-muted">3 tahun lalu</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Right arrow - desktop only -->
                <button type="button" id="review-next" class="hidden lg:flex items-center justify-center absolute -right-6 top-1/2 -translate-y-1/2 w-8 h-8 text-text-muted hover:text-ink transition text-xl select-none cursor-pointer z-10" aria-label="Selanjutnya">&#8250;</button>
                <div class="flex justify-center gap-2 mt-6" id="review-dots"></div>
            </div>
        </div>
    </section>

    {{-- INFO TOKO SECTION --}}
    <section id="info-toko" class="bg-surface py-16 lg:py-20">
        <div class="max-w-[1280px] mx-auto px-5 lg:px-[80px]">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
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
                                <a href="https://maps.app.goo.gl/XH8QjJezgEqBwtkm9" target="_blank" rel="noopener noreferrer" class="hover:text-green-700 transition">
                                    Jl. Yos Sudarso No.BA-9, Krajan, Sidopekso, Kec. Kraksaan, Kabupaten Probolinggo, Jawa Timur 67282, Indonesia
                                </a>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-text-muted uppercase tracking-wider">
                                Telepon
                            </div>
                            <div class="mt-1">
                                <a href="tel:+6285234789169" class="text-text-muted hover:text-green-700 transition">
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
                        <a href="https://wa.me/6285234789169" target="_blank" rel="noopener noreferrer" class="inline-block bg-green-900 text-white px-6 py-3 rounded-lg hover:bg-green-800 transition font-medium">
                            Hubungi Toko
                        </a>
                    </div>
                </div>

                {{-- Right Column (Map Placeholder) --}}
                <div class="flex justify-center lg:justify-end">
                    <div class="w-full max-w-[520px] h-[400px] rounded-xl overflow-hidden border border-border">
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
        <div class="max-w-[1280px] mx-auto px-5 lg:px-[80px]">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-6 text-center lg:text-left">
                <div>
                    <h2 class="font-heading font-bold text-2xl text-white">
                        Mau tau stok sebelum ke toko?
                    </h2>
                    <p class="text-white/70 mt-2">
                        Buka katalog, cek langsung.
                    </p>
                </div>
                <div>
                    <a href="/katalog" class="bg-green-700 text-white font-medium px-7 py-3 rounded-lg hover:bg-green-800 transition shrink-0 inline-block text-center">
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
                dot.className = 'w-6 h-2 rounded-full bg-green-700 transition-all duration-300';
            } else {
                dot.className = 'w-2 h-2 rounded-full bg-green-700/30 hover:bg-green-700/50 transition-all duration-300';
            }
        });
    }

    if (dotsContainer) {
        dotsContainer.innerHTML = '';
        for (let d = 0; d < total; d++) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'w-2 h-2 rounded-full bg-green-700/30 hover:bg-green-700/50 transition-all duration-300';
            dot.setAttribute('aria-label', `Ulasan ${d + 1}`);
            dot.addEventListener('click', () => {
                slideTo(3 + d);
            });
            dotsContainer.appendChild(dot);
        }
    }

    function getPerView() {
        if (window.innerWidth < 640) return 1;
        if (window.innerWidth < 1024) return 2;
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
