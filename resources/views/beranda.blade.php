<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Firo — Toko ATK &amp; Fotocopy Terlengkap</title>
    <meta name="description" content="Toko ATK dan jasa fotocopy terlengkap dengan harga terjangkau dan pelayanan terbaik.">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-['Inter',sans-serif] bg-white text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- 1. NAVBAR (Sticky Top) -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="#" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-blue-600 rounded-lg p-1">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xl shadow-md shadow-blue-600/20 group-hover:bg-blue-700 transition">
                        F
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-black tracking-tight text-blue-600 leading-none">FIRO</span>
                        <span class="text-xs font-semibold text-slate-500 tracking-wider uppercase mt-1">Toko ATK</span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="#" class="text-sm font-semibold text-slate-700 hover:text-blue-600 transition">Beranda</a>
                    <a href="#layanan" class="text-sm font-semibold text-slate-700 hover:text-blue-600 transition">Layanan</a>
                    <a href="#katalog" class="text-sm font-semibold text-slate-700 hover:text-blue-600 transition">Katalog</a>
                    <a href="#tentang" class="text-sm font-semibold text-slate-700 hover:text-blue-600 transition">Tentang</a>
                    <a href="#kontak" class="text-sm font-semibold text-slate-700 hover:text-blue-600 transition">Kontak</a>
                </nav>

                <!-- Right Action -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="/login" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                        Masuk
                    </a>
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex md:hidden">
                    <button id="mobile-menu-btn" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="Buka menu navigasi" class="p-2 rounded-lg text-slate-600 hover:text-blue-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <svg id="hamburger-icon" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg id="close-icon" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 bg-white px-4 pt-3 pb-6 space-y-3 shadow-lg">
            <a href="#" class="mobile-nav-link block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 hover:text-blue-600 hover:bg-blue-50 transition">Beranda</a>
            <a href="#layanan" class="mobile-nav-link block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 hover:text-blue-600 hover:bg-blue-50 transition">Layanan</a>
            <a href="#katalog" class="mobile-nav-link block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 hover:text-blue-600 hover:bg-blue-50 transition">Katalog</a>
            <a href="#tentang" class="mobile-nav-link block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 hover:text-blue-600 hover:bg-blue-50 transition">Tentang</a>
            <a href="#kontak" class="mobile-nav-link block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 hover:text-blue-600 hover:bg-blue-50 transition">Kontak</a>
            <div class="pt-2">
                <a href="/login" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 transition">
                    Masuk
                </a>
            </div>
        </div>
    </header>

    <!-- 2. HERO SECTION -->
    <section class="relative overflow-hidden bg-gradient-to-b from-blue-50/60 via-slate-50/40 to-white py-14 sm:py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <!-- Left: Text -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200 shadow-2xs">
                        <span>✨ Solusi Kebutuhan ATK Anda</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                        Toko ATK &amp; Fotocopy <span class="text-blue-600">Terlengkap</span>
                    </h1>

                    <p class="text-base sm:text-lg lg:text-xl text-slate-600 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Menyediakan berbagai kebutuhan alat tulis kantor, perlengkapan sekolah, dan jasa fotocopy dengan harga terjangkau dan pelayanan terbaik.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="#katalog" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 text-base font-semibold text-white bg-blue-600 rounded-xl shadow-md shadow-blue-600/25 hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/30 transition-all">
                            Lihat Katalog
                        </a>
                        <a href="#kontak" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 text-base font-semibold text-blue-600 border-2 border-blue-600 rounded-xl hover:bg-blue-50 transition-all">
                            Hubungi Kami
                        </a>
                    </div>

                    <!-- Stats Row -->
                    <div class="pt-8 border-t border-slate-200/80 grid grid-cols-3 gap-4 sm:gap-8 max-w-lg mx-auto lg:mx-0">
                        <div class="text-center lg:text-left">
                            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900">200+</p>
                            <p class="text-xs sm:text-sm font-medium text-slate-500 mt-0.5">Produk</p>
                        </div>
                        <div class="text-center lg:text-left">
                            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900">500+</p>
                            <p class="text-xs sm:text-sm font-medium text-slate-500 mt-0.5">Pelanggan</p>
                        </div>
                        <div class="text-center lg:text-left">
                            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 flex items-center justify-center lg:justify-start gap-1">
                                <span>5★</span>
                            </p>
                            <p class="text-xs sm:text-sm font-medium text-slate-500 mt-0.5">Rating</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Decorative Illustration Area -->
                <div class="lg:col-span-5 flex items-center justify-center">
                    <div class="relative w-full max-w-md lg:max-w-none aspect-4/3 rounded-3xl bg-gradient-to-tr from-blue-100/70 via-blue-50 to-indigo-50 border border-blue-100 p-6 flex items-center justify-center shadow-lg shadow-blue-500/5">
                        <!-- Abstract Background Blobs -->
                        <div class="absolute -top-6 -right-6 w-28 h-28 bg-blue-200/50 rounded-full blur-xl pointer-events-none"></div>
                        <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-indigo-200/40 rounded-full blur-xl pointer-events-none"></div>

                        <!-- Store / Office Supplies SVG Illustration -->
                        <svg class="w-full h-full max-h-72 drop-shadow-sm" viewBox="0 0 420 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Storefront Backdrop Frame -->
                            <rect x="50" y="40" width="320" height="220" rx="20" fill="#FFFFFF" stroke="#DBEAFE" stroke-width="2"/>

                            <!-- Store Awning Stripes -->
                            <path d="M50 60 C50 49 59 40 70 40 L350 40 C361 40 370 49 370 60 L370 78 L50 78 Z" fill="#2563EB"/>
                            <path d="M95 40 L135 40 L135 78 L95 78 Z" fill="#1D4ED8"/>
                            <path d="M185 40 L235 40 L235 78 L185 78 Z" fill="#1D4ED8"/>
                            <path d="M285 40 L325 40 L325 78 L285 78 Z" fill="#1D4ED8"/>

                            <!-- Shop Signboard -->
                            <rect x="140" y="90" width="140" height="32" rx="8" fill="#EFF6FF" stroke="#2563EB" stroke-width="1.5"/>
                            <text x="210" y="111" text-anchor="middle" font-family="Inter, sans-serif" font-size="13" font-weight="800" fill="#2563EB" letter-spacing="1">TOKO FIRO</text>

                            <!-- Printer / Copier Station (Left) -->
                            <g transform="translate(80, 140)">
                                <rect x="0" y="30" width="75" height="55" rx="8" fill="#F1F5F9" stroke="#94A3B8" stroke-width="2"/>
                                <rect x="10" y="12" width="55" height="24" rx="4" fill="#E2E8F0" stroke="#94A3B8" stroke-width="2"/>
                                <!-- Paper input -->
                                <rect x="18" y="0" width="39" height="16" rx="2" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="1.5"/>
                                <!-- Output tray -->
                                <rect x="15" y="60" width="45" height="16" rx="3" fill="#FFFFFF" stroke="#2563EB" stroke-width="1.5"/>
                                <!-- Controls -->
                                <circle cx="58" cy="45" r="4" fill="#2563EB"/>
                                <rect x="12" y="42" width="28" height="6" rx="3" fill="#94A3B8"/>
                            </g>

                            <!-- Center Display Shelf / Goods -->
                            <g transform="translate(175, 145)">
                                <!-- Books Stack -->
                                <rect x="0" y="48" width="70" height="10" rx="2" fill="#2563EB"/>
                                <rect x="3" y="36" width="64" height="10" rx="2" fill="#3B82F6"/>
                                <rect x="6" y="24" width="58" height="10" rx="2" fill="#60A5FA"/>
                                <rect x="8" y="12" width="54" height="10" rx="2" fill="#93C5FD"/>
                                <!-- Bookmark line -->
                                <path d="M50 12 L50 28" stroke="#F59E0B" stroke-width="2" stroke-linecap="round"/>
                            </g>

                            <!-- Pen Stand & Calculator (Right) -->
                            <g transform="translate(270, 145)">
                                <!-- Pen Holder Cup -->
                                <rect x="10" y="25" width="38" height="42" rx="6" fill="#F8FAFC" stroke="#94A3B8" stroke-width="2"/>
                                <rect x="14" y="32" width="30" height="3" rx="1.5" fill="#DBEAFE"/>
                                <!-- Pens sticking out -->
                                <line x1="20" y1="25" x2="12" y2="2" stroke="#2563EB" stroke-width="4" stroke-linecap="round"/>
                                <line x1="28" y1="25" x2="28" y2="-4" stroke="#DC2626" stroke-width="4" stroke-linecap="round"/>
                                <line x1="36" y1="25" x2="44" y2="4" stroke="#F59E0B" stroke-width="4" stroke-linecap="round"/>
                                <polygon points="12,2 8,-2 15,-1" fill="#1D4ED8"/>
                                <!-- Ruler -->
                                <rect x="54" y="15" width="8" height="52" rx="2" fill="#FDE047" stroke="#EAB308" stroke-width="1.5"/>
                                <line x1="54" y1="25" x2="58" y2="25" stroke="#713F12" stroke-width="1"/>
                                <line x1="54" y1="35" x2="58" y2="35" stroke="#713F12" stroke-width="1"/>
                                <line x1="54" y1="45" x2="58" y2="45" stroke="#713F12" stroke-width="1"/>
                                <line x1="54" y1="55" x2="58" y2="55" stroke="#713F12" stroke-width="1"/>
                            </g>

                            <!-- Floating badge: 100% Kualitas -->
                            <g transform="translate(290, 75)">
                                <circle cx="20" cy="20" r="18" fill="#2563EB"/>
                                <path d="M14 20 L18 24 L27 15" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. LAYANAN KAMI (Services) — id="layanan" -->
    <section id="layanan" class="py-16 sm:py-20 lg:py-24 bg-slate-50 border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">Pelayanan Terbaik</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mt-2">
                    Layanan Kami
                </h2>
                <p class="mt-3 text-base sm:text-lg text-slate-600 leading-relaxed">
                    Menyediakan ragam solusi ATK dan dokumen untuk perkantoran, instansi, sekolah, dan umum.
                </p>
            </div>

            <!-- 3 Cards in Responsive Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <!-- Card 1: Alat Tulis Kantor -->
                <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition flex flex-col group border border-slate-100/80">
                    <div class="w-14 h-14 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-5 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                        <!-- Pencil / Pen SVG -->
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 group-hover:text-blue-600 transition">
                        Alat Tulis Kantor
                    </h3>
                    <p class="mt-2.5 text-slate-600 text-sm sm:text-base leading-relaxed">
                        Pilihan lengkap pulpen, pensil, spidol, kertas HVS berbagai ukuran, map, stapler, dan aneka perlengkapan kantor untuk operasional harian Anda.
                    </p>
                </div>

                <!-- Card 2: Fotocopy & Print -->
                <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition flex flex-col group border border-slate-100/80">
                    <div class="w-14 h-14 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-5 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                        <!-- Printer SVG -->
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 group-hover:text-blue-600 transition">
                        Fotocopy &amp; Print
                    </h3>
                    <p class="mt-2.5 text-slate-600 text-sm sm:text-base leading-relaxed">
                        Layanan fotocopy hitam-putih &amp; warna, cetak dokumen digital, scan dokumen, laminating, dan jilid proposal dengan hasil jernih dan proses cepat.
                    </p>
                </div>

                <!-- Card 3: Perlengkapan Sekolah -->
                <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition flex flex-col group border border-slate-100/80">
                    <div class="w-14 h-14 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-5 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                        <!-- Backpack / Book SVG -->
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 group-hover:text-blue-600 transition">
                        Perlengkapan Sekolah
                    </h3>
                    <p class="mt-2.5 text-slate-600 text-sm sm:text-base leading-relaxed">
                        Perlengkapan belajar siswa mulai dari buku tulis, alat gambar &amp; mewarnai, sampul buku, tempat pensil, gunting, hingga lem kertas dengan harga ramah kantong.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. PRODUK UNGGULAN (Featured Products) — id="katalog" -->
    <section id="katalog" class="py-16 sm:py-20 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">Katalog Pilihan</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mt-2">
                    Produk Unggulan
                </h2>
                <p class="mt-3 text-base sm:text-lg text-slate-600 leading-relaxed">
                    Daftar item terpopuler dan paling dicari dengan stok siap beli dan jaminan harga terbaik.
                </p>
            </div>

            <!-- Product Grid: 2 cols mobile, 4 cols desktop -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                @forelse($produkUnggulan as $item)
                    <div class="bg-white rounded-xl shadow overflow-hidden group hover:shadow-lg transition border border-slate-100 flex flex-col">
                        <div class="h-48 bg-slate-100 flex items-center justify-center p-4 relative overflow-hidden group-hover:bg-slate-200/60 transition-colors">
                            <!-- Placeholder Icon -->
                            <svg class="w-16 h-16 text-slate-400 group-hover:text-blue-600 group-hover:scale-110 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            @if(!empty($item->merek) && $item->merek !== '-')
                                <span class="absolute top-2.5 right-2.5 text-[10px] font-semibold bg-white/90 backdrop-blur px-2 py-0.5 rounded text-slate-600 border border-slate-200/60">
                                    {{ $item->merek }}
                                </span>
                            @endif
                        </div>
                        <div class="p-4 flex flex-col flex-1">
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full w-max font-medium">{{ $item->jenis ?? 'ATK' }}</span>
                            <h3 class="font-semibold mt-2 text-slate-900 line-clamp-1 text-sm sm:text-base" title="{{ $item->nama_item }}">{{ $item->nama_item }}</h3>
                            <p class="text-blue-600 font-bold mt-1 text-base sm:text-lg">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</p>
                            <div class="flex items-center gap-2 mt-2 pt-1 border-t border-slate-50">
                                @if(($item->tipe_item ?? '') === 'JASA')
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="text-xs font-medium {{ $item->stok > 5 ? 'text-green-600' : 'text-orange-500' }}">
                                        Stok: {{ $item->stok }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                        <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-base font-semibold text-slate-700">Belum ada produk tersedia</p>
                        <p class="text-xs text-slate-500 mt-1">Silakan kunjungi toko langsung atau hubungi kami untuk pemesanan khusus.</p>
                    </div>
                @endforelse
            </div>

            <!-- Lihat Semua Produk Button -->
            <div class="mt-12 text-center">
                <a href="#katalog" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-blue-600 text-blue-600 font-bold hover:bg-blue-600 hover:text-white transition group">
                    <span>Lihat Semua Produk</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- 5. TENTANG KAMI (About) — id="tentang" -->
    <section id="tentang" class="py-16 sm:py-20 lg:py-24 bg-slate-50 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                <!-- Text Left -->
                <div class="lg:col-span-7 space-y-6">
                    <div>
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">Tentang Toko Firo</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mt-2">
                            Mengapa Memilih Toko Firo?
                        </h2>
                        <p class="mt-3 text-base sm:text-lg text-slate-600 leading-relaxed">
                            Kami hadir sebagai mitra terpercaya bagi pelajar, mahasiswa, pekerja kantor, dan masyarakat umum dalam memenuhi segala kebutuhan tulis menulis serta percetakan dokumen.
                        </p>
                    </div>

                    <!-- 4 Advantages List with Check Icons -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <!-- 1. Harga Terjangkau -->
                        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs hover:shadow-sm transition flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Harga Terjangkau</h3>
                                <p class="text-sm text-slate-600 mt-0.5">Harga bersaing dengan kualitas terjamin</p>
                            </div>
                        </div>

                        <!-- 2. Produk Lengkap -->
                        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs hover:shadow-sm transition flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Produk Lengkap</h3>
                                <p class="text-sm text-slate-600 mt-0.5">Tersedia berbagai kebutuhan ATK dan sekolah</p>
                            </div>
                        </div>

                        <!-- 3. Pelayanan Cepat -->
                        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs hover:shadow-sm transition flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Pelayanan Cepat</h3>
                                <p class="text-sm text-slate-600 mt-0.5">Layanan fotocopy dan print yang cepat dan rapi</p>
                            </div>
                        </div>

                        <!-- 4. Lokasi Strategis -->
                        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-2xs hover:shadow-sm transition flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Lokasi Strategis</h3>
                                <p class="text-sm text-slate-600 mt-0.5">Mudah dijangkau di pusat kota</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Decorative Right -->
                <div class="lg:col-span-5">
                    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-md border border-slate-100 relative overflow-hidden">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-2xl shadow-md shadow-blue-500/20">
                                F
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-lg">Komitmen Mutu Toko Firo</h4>
                                <p class="text-xs text-slate-500">Melayani dengan Sepenuh Hati</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <span class="text-sm font-semibold text-slate-700">Ketersediaan Stok</span>
                                <span class="text-xs font-bold text-green-700 bg-green-100 px-2.5 py-1 rounded-full">Terjamin Real-Time</span>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <span class="text-sm font-semibold text-slate-700">Kualitas Hasil Cetak</span>
                                <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full">Tajam &amp; Bersih</span>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                <span class="text-sm font-semibold text-slate-700">Garansi Pelayanan</span>
                                <span class="text-xs font-bold text-indigo-700 bg-indigo-100 px-2.5 py-1 rounded-full">Ramah &amp; Responsif</span>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                            <p class="text-xs text-slate-500">
                                Buka setiap hari kerja untuk mendukung kesuksesan belajar dan produktivitas kerja Anda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. CTA BANNER -->
    <section class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 text-white py-14 sm:py-18 lg:py-20">
        <!-- Background Decorative Shapes -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -left-20 -top-20 w-80 h-80 bg-blue-400/20 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight">
                Butuh ATK atau Jasa Fotocopy?
            </h2>
            <p class="mt-4 text-base sm:text-lg lg:text-xl text-blue-100 max-w-2xl mx-auto leading-relaxed">
                Kunjungi toko kami atau hubungi via WhatsApp untuk pemesanan
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="https://wa.me/6285234789169?text=Halo%20Toko%20Firo,%20saya%20ingin%20memesan%20ATK%20atau%20jasa%20fotocopy" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 text-base font-bold text-blue-600 bg-white rounded-xl shadow-lg hover:bg-blue-50 hover:shadow-xl transition-all">
                    <!-- WhatsApp / Chat Bubble SVG -->
                    <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0012.04 2zm.01 1.67c2.2 0 4.26.86 5.82 2.42a8.18 8.18 0 012.41 5.82c0 4.54-3.7 8.24-8.24 8.24-1.45 0-2.88-.38-4.14-1.11l-.3-.17-3.12.82.83-3.04-.19-.31a8.21 8.21 0 01-1.26-4.43c0-4.54 3.7-8.24 8.24-8.24zm4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.25-1.5-1.4-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1s.9 2.44 1.03 2.61c.13.17 1.77 2.7 4.28 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.17-.48-.29z"/>
                    </svg>
                    <span>Hubungi via WhatsApp</span>
                </a>
                <a href="#kontak" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 text-base font-bold text-white border-2 border-white rounded-xl hover:bg-white/10 transition-all">
                    <span>Lihat Lokasi</span>
                </a>
            </div>
        </div>
    </section>

    <!-- 7. FOOTER — Dark Bg (Slate-900) — id="kontak" -->
    <footer id="kontak" class="bg-slate-900 text-slate-400 pt-16 pb-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12 pb-12 border-b border-slate-800">
                <!-- Col 1: Logo FIRO + short description -->
                <div class="space-y-4">
                    <a href="#" class="flex items-center gap-3 group focus:outline-none">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-lg shadow-sm">
                            F
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-black tracking-tight text-white leading-none">FIRO</span>
                            <span class="text-[11px] font-semibold text-slate-400 tracking-wider uppercase mt-1">Toko ATK</span>
                        </div>
                    </a>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Toko perlengkapan alat tulis kantor, perlengkapan sekolah, dan jasa fotocopy berkualitas terpercaya di Kraksaan.
                    </p>
                </div>

                <!-- Col 2: "Menu" Links -->
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Menu</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="#katalog" class="hover:text-white transition">Katalog</a></li>
                        <li><a href="#tentang" class="hover:text-white transition">Tentang</a></li>
                        <li><a href="#kontak" class="hover:text-white transition">Kontak</a></li>
                    </ul>
                </div>

                <!-- Col 3: "Layanan" Links -->
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Layanan</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#layanan" class="hover:text-white transition">Alat Tulis</a></li>
                        <li><a href="#layanan" class="hover:text-white transition">Fotocopy</a></li>
                        <li><a href="#layanan" class="hover:text-white transition">Print Dokumen</a></li>
                        <li><a href="#layanan" class="hover:text-white transition">Perlengkapan Sekolah</a></li>
                    </ul>
                </div>

                <!-- Col 4: "Kontak" Info -->
                <div class="space-y-3">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Kontak</h3>
                    <div class="space-y-3 text-sm text-slate-400">
                        <div class="flex items-start gap-3">
                            <!-- Map Pin SVG -->
                            <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Jl. Yos Sudarso No.BA-9, Krajan, Sidopekso, Kec. Kraksaan, Kab. Probolinggo</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Phone SVG -->
                            <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="tel:+6285234789169" class="hover:text-white transition">+62 852-3478-9169</a>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Mail SVG -->
                            <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:tokofiro@gmail.com" class="hover:text-white transition">tokofiro@gmail.com</a>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Clock SVG -->
                            <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Senin - Sabtu, 08:00 - 21:00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; 2026 Toko Firo. All rights reserved.</p>
                <p>Kebutuhan ATK &amp; Fotocopy Terpercaya Anda.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Hamburger Menu Script -->
    <script>
        {{-- ponytail: vanilla toggle covers mobile navigation without external dependencies; upgrade to Alpine/Vue if modal state grows --}}
        (function() {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const hamburgerIcon = document.getElementById('hamburger-icon');
            const closeIcon = document.getElementById('close-icon');

            if (!btn || !menu) return;

            function toggleMenu(show) {
                const isOpen = show !== undefined ? show : menu.classList.contains('hidden');
                if (isOpen) {
                    menu.classList.remove('hidden');
                    hamburgerIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                    btn.setAttribute('aria-expanded', 'true');
                } else {
                    menu.classList.add('hidden');
                    hamburgerIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                }
            }

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleMenu();
            });

            // Close menu when clicking on any mobile nav link
            document.querySelectorAll('.mobile-nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    toggleMenu(false);
                });
            });

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !menu.classList.contains('hidden')) {
                    toggleMenu(false);
                }
            });
        })();
    </script>
</body>
</html>
