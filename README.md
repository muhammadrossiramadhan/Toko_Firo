# Toko Firo

Sistem informasi berbasis web untuk pengelolaan data barang, stok, transaksi penjualan, akun pengguna, batas stok minimum, cadangan data (backup), dan laporan pada Toko Firo (usaha ATK dan fotokopi).

Dikerjakan untuk mata kuliah Sistem Informasi / Rekayasa Perangkat Lunak, Universitas Negeri Malang, 2026.

## Anggota Kelompok
- Muhammad Rossi Ramadhan (250535625057), Ketua Kelompok, Database, Modul Data Barang, Laporan Penjualan, Pembelian Barang, Kelola Akun Pengguna, Batas Stok Minimum, Dashboard Admin, dan Backup Data
- Muhammad Totti Al Fikri (250535630175), Backend
- Refina Aprilia Dewi (250535624776), UI/UX dan Analisis
- Teofilus Aditya Suseno Pandeangan (250535626205), Frontend, Backend, dan QA

## Teknologi
- Framework: Laravel 12, PHP 8.2 ke atas (dikembangkan dengan PHP 8.4 via FlyEnv)
- Database: MariaDB 13.1.1, dengan logika bisnis pada Stored Procedure & Trigger
- Frontend: Blade dan Tailwind CSS (tanpa Livewire dan tanpa Alpine.js; interaktivitas via Vanilla JavaScript)
- Lingkungan Pengembangan: Windows dengan FlyEnv

## Struktur Dokumentasi
- `HANDOFF_CRUD_TOKO_FIRO.md`: Panduan serah terima resmi arsitektur teknis, isolasi rute, dan mitigasi merge modul admin
- `DATABASE.md`: ERD, skema tabel, view, stored procedure, trigger, hak akses, dan catatan prosedur
- `database/sql/`: Skrip SQL berurutan 00 sampai 09 (skema inti, tabel baru, view, SP, trigger, dan SP v2)
- `TRACKING.md`: Log pengerjaan berkala dan status tiap tahapan milestone proyek
- `koreksi_copilot.md`: Keputusan teknis final pemilik (K1 sampai K19) dan verifikasi langkah kerja
- `PROJECT_STATUS.md`: Laporan status teknis menyeluruh internal (status branch, file, test suite, dan kesiapan merge)

## Status Fitur: Modul Backup Data (Admin)
Modul Backup Data (sesuai prototype Figma frame P15) telah diimplementasikan secara mandiri dan terisolasi pada branch ini:
- **Dua Kartu Pengaturan Backup**:
  - **Backup Manual**: Kartu berbingkai hijau (`border-emerald-500`) dengan deskripsi pencadangan database SQL terenkripsi dan tombol aksi hijau "Jalankan Backup Sekarang" (`bg-emerald-600`) dengan touch-target minimal 44px.
  - **Backup Otomatis**: Kartu berbingkai netral yang menampilkan jadwal aktif setiap hari pukul 22.00 WIB pada server toko, dilengkapi tombol "Atur Jadwal Backup" dengan touch-target minimal 44px.
- **Tabel Riwayat Backup**:
  - Kolom tabel lengkap: `Waktu Backup`, `Tipe`, `Ukuran File`, `Status`, dan `Aksi`.
  - 4 data contoh riwayat realistis sesuai Figma P15:
    - Hari ini, 14.30 — Manual — 2,4 MB — Berhasil
    - Kemarin, 22.00 — Otomatis — 2,4 MB — Berhasil
    - 1 Okt 2026, 22.00 — Otomatis — 2,3 MB — Berhasil
    - 30 Sep 2026, 22.00 — Otomatis — 2,3 MB — Berhasil
  - Badge status "Berhasil" berwarna hijau lembut (`bg-emerald-100 text-emerald-800 rounded-full px-3 py-1`).
  - Kolom aksi berbasis teks murni "Unduh" warna hijau (`text-emerald-700`) tanpa ketergantungan ikon grafis (K12).
- **Banner Informasi Keamanan**:
  - Kotak pemberitahuan biru muda (`bg-blue-50 border-blue-200 text-blue-900`) terkait penyimpanan backup di server lokal dan anjuran pengunduhan berkala ke perangkat terpisah secara teks bersih.
- **Kepatuhan UI/UX (K12) & Skala Global 80% (K18)**:
  - Bebas tag `<svg>`, bebas ikon grafis (disket, lonceng, unduh ditiadakan), dan bebas emoji.
  - Ukuran font minimal 16px (`text-base`), touch-target tombol minimal 44px (`min-h-[2.75rem]`), dan proporsional pada browser zoom 100%.

## Arsitektur Skala Tampilan Global (K12 Adaptif)
Untuk memastikan antarmuka pada zoom browser 100% presisi dengan desain Figma (lebar sidebar ~260px, bukan 325px yang terjadi jika font-size akar tidak disesuaikan):
- Menggunakan partial global terpusat: `resources/views/partials/skala.blade.php`.
- Skala akar halaman ditetapkan sebesar `80%` (`:root { --skala: 80%; } html { font-size: var(--skala); }`).
- Seluruh elemen tetap mematuhi standar touch-target K12: ukuran tombol minimal `2.75rem` (setara 44px pada font basis 16px) dan font minimum `1rem` (`text-base`).
- Kontainer sidebar menerapkan `min-h-screen flex flex-col justify-between` sehingga kartu profil pengguna ("Admin / Pemilik") dan tombol "Keluar" di pojok kiri bawah selalu terkunci rapi tanpa terpotong.

## Isolasi Arsitektur & Decoupling Sesi
Untuk mencegah konflik (*conflict*) merge dengan modul Autentikasi dan 2FA yang dikembangkan rekan tim:
- Rute modul didaftarkan mandiri di `routes/admin_backup.php`.
- Rute dimuat melalui blok `then:` pada `bootstrap/app.php` tanpa memodifikasi `routes/web.php`.
- Controller mandiri ditempatkan di `app/Http/Controllers/Admin/BackupController.php`.
- Middleware `auth` dan `role:ADMIN` dilepas sementara di tingkat rute lokal untuk mempermudah verifikasi tampilan independen (dengan pengingat pemulihan sebelum penggabungan ke branch `staging`/`main`).
- Pembacaan profil pengguna pada view menggunakan fallback aman null-safe: `Auth::user()?->nama ?? 'Admin / Pemilik'`.

## Cara Menjalankan (Lingkungan Lokal)
1. Beralih ke branch kerja:
   ```bash
   git checkout Rossi/Backup-Data_Admins