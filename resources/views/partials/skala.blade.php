<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
<style id="skala-tampilan">
/* K12 dikecualikan untuk halaman ini atas permintaan pemilik agar sama
   dengan Figma. Ubah --skala ke 100% untuk kembali ke 16px/44px. */
:root { --skala: 80%; }
html { font-size: var(--skala); margin: 0 !important; padding: 0 !important; }
body { margin: 0 !important; padding: 0 !important; }

/* Hilangkan kotak putih focus outline browser di sidebar, menu navigasi, dan seluruh elemen */
*, *:focus, *:focus-visible,
aside, aside:focus, aside:focus-visible, aside *, aside *:focus, aside *:focus-visible,
nav, nav *, nav a, nav a:focus, nav a:focus-visible,
button, button:focus, button:focus-visible,
a, a:focus, a:focus-visible {
    outline: none !important;
    outline-color: transparent !important;
    outline-style: none !important;
    outline-width: 0 !important;
    box-shadow: none !important;
    -webkit-tap-highlight-color: transparent !important;
    -webkit-focus-ring-color: transparent !important;
}
aside {
    outline: none !important;
    -webkit-tap-highlight-color: transparent !important;
}

/* Override: Hapus min-height 44px pada link di dalam footer agar tidak renggang */
footer a,
footer ul li a {
    min-height: 0 !important;
    height: auto !important;
    padding-top: 2px !important;
    padding-bottom: 2px !important;
    line-height: 1.5 !important;
}

/* ==========================================================================
   OPTIMASI RESPONSIVE MOBILE UNTUK HALAMAN ADMIN & KASIR (Max-Width: 768px)
   ========================================================================== */
@media (max-width: 768px) {
    /* 1. Body beralih ke orientasi vertikal penuh */
    body {
        flex-direction: column !important;
        width: 100% !important;
        min-width: 100% !important;
        overflow-x: hidden !important;
    }

    /* 2. Area Konten Utama mengambil lebar 100% layar HP dengan padding lega */
    main {
        width: 100% !important;
        max-width: 100% !important;
        flex: 1 1 100% !important;
        padding: 1rem !important;
        box-sizing: border-box !important;
        overflow-x: hidden !important;
    }

    /* 3. Header halaman vertikal pada layar sempit */
    main > header {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 1rem !important;
        padding-bottom: 1rem !important;
        margin-bottom: 1rem !important;
    }

    main > header > div:last-child {
        align-self: flex-start !important;
        width: 100% !important;
        justify-content: space-between !important;
    }

    /* 4. Sidebar Navigasi beralih menjadi Drawer Samping (Off-Canvas) */
    aside {
        position: fixed !important;
        top: 0 !important;
        bottom: 0 !important;
        left: 0 !important;
        width: 280px !important;
        max-width: 85vw !important;
        height: 100vh !important;
        z-index: 9999 !important;
        transform: translateX(-100%) !important;
        transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 8px 0 32px rgba(0, 0, 0, 0.45) !important;
    }

    aside.mobile-drawer-open {
        transform: translateX(0) !important;
    }

    /* 5. Backdrop Gelap saat Drawer Terbuka */
    #mobileDrawerBackdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.65);
        z-index: 9998;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease-in-out;
        backdrop-filter: blur(2px);
    }

    #mobileDrawerBackdrop.active {
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    /* 6. Topbar Navigasi Khusus Mobile */
    .mobile-header-bar {
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        background-color: #08331e;
        color: #ffffff;
        padding: 0.75rem 1rem;
        position: sticky;
        top: 0;
        z-index: 9990;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid rgba(16, 185, 129, 0.25);
    }

    .mobile-close-btn {
        display: flex !important;
    }

    /* 7. Penyesuaian grid & stepper pada layar kecil */
    .grid-cols-3 {
        gap: 0.35rem !important;
    }

    .grid-cols-3 > div {
        padding: 0.5rem 0.25rem !important;
        font-size: 0.75rem !important;
        line-height: 1.15 !important;
    }
}

@media (min-width: 769px) {
    .mobile-header-bar {
        display: none !important;
    }
    #mobileDrawerBackdrop {
        display: none !important;
    }
    .mobile-close-btn {
        display: none !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var aside = document.querySelector('aside');
    if (!aside) return;

    // 1. Buat elemen Backdrop Overlay jika belum ada
    var backdrop = document.getElementById('mobileDrawerBackdrop');
    if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.id = 'mobileDrawerBackdrop';
        document.body.appendChild(backdrop);
    }

    // 2. Buat Topbar Navigasi Mobile jika belum ada
    var existingBar = document.querySelector('.mobile-header-bar');
    if (!existingBar) {
        var topbar = document.createElement('div');
        topbar.className = 'mobile-header-bar';

        var menuBtn = document.createElement('button');
        menuBtn.type = 'button';
        menuBtn.className = 'touch-target px-3 py-2 bg-emerald-800 hover:bg-emerald-700 active:bg-emerald-900 text-white font-bold rounded-none flex items-center gap-2 cursor-pointer shadow-sm text-base';
        menuBtn.setAttribute('aria-label', 'Buka Menu Navigasi');
        menuBtn.innerHTML = '<span style="font-size: 1.25rem; line-height: 1;">&#9776;</span><span class="text-sm">Menu</span>';

        var brand = document.createElement('div');
        brand.className = 'flex items-center gap-2';
        brand.innerHTML = '<span class="font-bold text-lg tracking-wider text-white">TOKO FIRO</span>';

        var userBadge = document.createElement('div');
        userBadge.className = 'w-9 h-9 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none text-sm shadow-sm';
        var userInitial = '{{ strtoupper(substr(Auth::user()->nama ?? Auth::user()->username ?? "U", 0, 1)) }}';
        userBadge.textContent = userInitial || 'U';

        topbar.appendChild(menuBtn);
        topbar.appendChild(brand);
        topbar.appendChild(userBadge);

        document.body.insertBefore(topbar, document.body.firstChild);

        menuBtn.addEventListener('click', function () {
            aside.classList.add('mobile-drawer-open');
            backdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    // 3. Tambahkan tombol Tutup di dalam Sidebar khusus tampilan mobile
    var existingClose = aside.querySelector('.mobile-close-btn');
    if (!existingClose) {
        var closeContainer = document.createElement('div');
        closeContainer.className = 'mobile-close-btn flex items-center justify-between pb-3 mb-2 border-b border-emerald-800/80';

        var label = document.createElement('span');
        label.className = 'text-xs uppercase tracking-wider text-emerald-300 font-bold';
        label.textContent = 'Navigasi Toko Firo';

        var closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'touch-target px-3 py-1.5 bg-emerald-900/80 hover:bg-emerald-800 text-white rounded-none text-sm font-semibold cursor-pointer border border-emerald-700/50';
        closeBtn.setAttribute('aria-label', 'Tutup Menu Navigasi');
        closeBtn.innerHTML = '&#10005; Tutup';

        closeContainer.appendChild(label);
        closeContainer.appendChild(closeBtn);

        aside.insertBefore(closeContainer, aside.firstChild);

        function closeDrawer() {
            aside.classList.remove('mobile-drawer-open');
            backdrop.classList.remove('active');
            document.body.style.overflow = '';
        }

        closeBtn.addEventListener('click', closeDrawer);
        backdrop.addEventListener('click', closeDrawer);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && aside.classList.contains('mobile-drawer-open')) {
                closeDrawer();
            }
        });

        // Tutup drawer otomatis saat salah satu link navigasi ditekan di HP
        var navLinks = aside.querySelectorAll('nav a, a');
        navLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    closeDrawer();
                }
            });
        });
    }
});
</script>
