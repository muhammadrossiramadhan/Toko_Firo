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
</style>
