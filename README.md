# Toko Firo

Sistem informasi berbasis web untuk pengelolaan data barang, stok, transaksi penjualan, akun pengguna, batas stok minimum, dan laporan pada Toko Firo (usaha ATK dan fotokopi).

Dikerjakan untuk mata kuliah Sistem Informasi / Rekayasa Perangkat Lunak, Universitas Negeri Malang, 2026.

## Anggota Kelompok
- Muhammad Rossi Ramadhan (250535625057), Ketua Kelompok, Database, Modul Data Barang, Laporan Penjualan, Pembelian Barang, Kelola Akun Pengguna, Batas Stok Minimum, dan Dashboard Admin
- Muhammad Totti Al Fikri (250535630175), Backend
- Refina Aprilia Dewi (250535624776), UI/UX dan Analisis
- Teofilus Aditya Suseno Pandeangan (250535626205), Frontend, Backend, dan QA

## Teknologi
- Framework: Laravel 12, PHP 8.2 ke atas (dikembangkan dengan PHP 8.4 via FlyEnv)
- Database: MariaDB 13.1.1, dengan logika bisnis pada Stored Procedure & Trigger
- Frontend: Blade dan Tailwind CSS (tanpa Livewire dan tanpa Alpine.js; visualisasi diagram batang dan interaktivitas via HTML/Tailwind CSS & Vanilla JavaScript)
- Lingkungan Pengembangan: Windows dengan FlyEnv

## Struktur Dokumentasi
- `HANDOFF_CRUD_TOKO_FIRO.md`: Panduan serah terima resmi arsitektur teknis, isolasi rute, dan mitigasi merge modul admin
- `DATABASE.md`: ERD, skema tabel, view, stored procedure, trigger, hak akses, dan catatan prosedur
- `database/sql/`: Skrip SQL berurutan 00 sampai 09 (skema inti, tabel baru, view, SP, trigger, dan SP v2)
- `TRACKING.md`: Log pengerjaan berkala dan status tiap tahapan milestone proyek
- `koreksi_copilot.md`: Keputusan teknis final pemilik (K1 sampai K19) dan verifikasi langkah kerja
- `PROJECT_STATUS.md`: Laporan status teknis menyeluruh internal (status branch, file, test suite, dan kesiapan merge)

## Status Fitur: Modul Dashboard Admin (Admin)
Modul Dashboard Admin (sesuai prototype Figma frame P10) telah diimplementasikan secara mandiri dan terisolasi pada branch ini:
- **Header Atas & Navigasi Akun**:
  - Judul modul "Dashboard Admin" dengan penanda status notifikasi berbasis teks bersih dan avatar profil inisial "J" berdampingan dengan nama "Junaidi Saleh" di pojok kanan atas.
- **Sapaan & Ringkasan Toko**:
  - Teks sapaan profesional "Halo, Junaidi" dengan tanggal dinamis ("Jumat, 3 Oktober 2026 · Ringkasan toko hari ini") tanpa emoji tangan sesuai standar K12.
- **4 Kartu Metrik Utama (KPI)**:
  - **Penjualan Hari Ini**: `Rp 3.180.000` dengan badge pertumbuhan hijau `+12%`.
  - **Jumlah Transaksi**: `14` dengan badge pertumbuhan hijau `+3`.
  - **Stok Menipis**: `7` dengan keterangan peringatan stok `2 habis`.
  - **Laba Hari Ini**: `Rp 680.000` dengan badge estimasi laba `+8%`.
  - Seluruh kartu didesain minimalis elegan tanpa menggunakan emoji atau ikon grafis berlebih.
- **Diagram Batang Penjualan 7 Hari Terakhir**:
  - Visualisasi grafik batang vertikal murni menggunakan Tailwind CSS (tanpa beban pustaka pihak ketiga seperti Chart.js).
  - Skala hari Senin s.d. Minggu dengan highlight hari puncak (Jumat) menggunakan warna hijau emerald pekat (`bg-emerald-600`) dan hari lainnya menggunakan warna pastel lembut (`bg-emerald-100`).
- **Kartu Produk Terlaris (Top Barang Hari Ini)**:
  - Daftar 3 produk teratas hari ini: Kertas HVS A4 (8 terjual), Pulpen Pilot G2 (6 terjual), dan Fotocopy A4 (5 layanan).
- **Log Aktivitas Terbaru**:
  - Linimasa riwayat aksi sistem secara real-time dengan penanda titik hijau lembut dan penunjuk waktu (14:32 transaksi TRX-001, 13:50 info stok habis, 12:00 sesi login kasir, 10:30 penambahan item).
- **Kepatuhan UI/UX (K12) & Skala Global 80% (K18)**:
  - Bebas tag `<svg>`, bebas ikon grafis, dan bebas emoji.
  - Ukuran font minimal 16px (`text-base`), touch-target tombol minimal 44px (`min-h-[2.75rem]`), dan proporsional pada browser zoom 100%.

## Arsitektur Skala Tampilan Global (K12 Adaptif)
Untuk memastikan antarmuka pada zoom browser 100% presisi dengan desain Figma (lebar sidebar ~260px, bukan 325px yang terjadi jika font-size akar tidak disesuaikan):
- Menggunakan partial global terpusat: `resources/views/partials/skala.blade.php`.
- Skala akar halaman ditetapkan sebesar `80%` (`:root { --skala: 80%; } html { font-size: var(--skala); }`).
- Seluruh elemen tetap mematuhi standar touch-target K12: ukuran tombol minimal `2.75rem` (setara 44px pada font basis 16px) dan font minimum `1rem` (`text-base`).
- Kontainer sidebar menerapkan `min-h-screen flex flex-col justify-between` sehingga kartu profil pengguna ("Admin / Pemilik") dan tombol "Keluar" di pojok kiri bawah selalu terkunci rapi tanpa terpotong.

## Isolasi Arsitektur & Decoupling Sesi
Untuk mencegah konflik (*conflict*) merge dengan modul Autentikasi dan 2FA yang dikembangkan rekan tim:
- Rute modul didaftarkan mandiri di `routes/admin_dashboard.php`.
- Rute dimuat melalui blok `then:` pada `bootstrap/app.php` tanpa memodifikasi `routes/web.php`.
- Controller mandiri ditempatkan di `app/Http/Controllers/Admin/DashboardController.php`.
- Middleware `auth` dan `role:ADMIN` dilepas sementara di tingkat rute lokal untuk mempermudah verifikasi tampilan independen (dengan pengingat pemulihan sebelum penggabungan ke branch `staging`/`main`).
- Pembacaan profil pengguna pada view menggunakan fallback aman null-safe: `Auth::user()?->nama ?? 'Admin / Pemilik'`.

## Cara Menjalankan (Lingkungan Lokal)
1. Beralih ke branch kerja:
   ```bash
   git checkout Rossi/Dashboard-admin