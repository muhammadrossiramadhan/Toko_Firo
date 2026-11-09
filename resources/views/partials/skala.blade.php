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

    /* 4. Penyesuaian grid & stepper pada layar kecil */
    .grid-cols-3 {
        gap: 0.35rem !important;
    }

    .grid-cols-3 > div {
        padding: 0.5rem 0.25rem !important;
        font-size: 0.75rem !important;
        line-height: 1.15 !important;
    }
}
</style>
