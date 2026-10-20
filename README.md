# Toko Firo

Sistem informasi berbasis web untuk pengelolaan data barang, stok, transaksi penjualan, akun pengguna, batas stok minimum, dan laporan pada Toko Firo (usaha ATK dan fotokopi).

Dikerjakan untuk mata kuliah Sistem Informasi / Rekayasa Perangkat Lunak, Universitas Negeri Malang, 2026.

## Anggota Kelompok
- Muhammad Rossi Ramadhan (250535625057), Ketua Kelompok, Database, Modul Data Barang, Laporan Penjualan, Pembelian Barang, Kelola Akun Pengguna, dan Batas Stok Minimum
- Muhammad Totti Al Fikri (250535630175), Backend
- Refina Aprilia Dewi (250535624776), UI/UX dan Analisis
- Teofilus Aditya Suseno Pandeangan (250535626205), Frontend, Backend, dan QA

## Teknologi
- Framework: Laravel 12, PHP 8.2 ke atas (dikembangkan dengan PHP 8.4 via FlyEnv)
- Database: MariaDB 13.1.1, dengan logika bisnis pada Stored Procedure & Trigger
- Frontend: Blade dan Tailwind CSS (tanpa Livewire dan tanpa Alpine.js; interaktivitas modal, filter, stepper, dan tombol aksi via Vanilla JavaScript)
- Lingkungan Pengembangan: Windows dengan FlyEnv

## Struktur Dokumentasi
- `HANDOFF_CRUD_TOKO_FIRO.md`: Panduan serah terima resmi arsitektur teknis, isolasi rute, dan mitigasi merge modul admin
- `DATABASE.md`: ERD, skema tabel, view, stored procedure, trigger, hak akses, dan catatan prosedur
- `database/sql/`: Skrip SQL berurutan 00 sampai 09 (skema inti, tabel baru, view, SP, trigger, dan SP v2)
- `TRACKING.md`: Log pengerjaan berkala dan status tiap tahapan milestone proyek
- `koreksi_copilot.md`: Keputusan teknis final pemilik (K1 sampai K19) dan verifikasi langkah kerja
- `PROJECT_STATUS.md`: Laporan status teknis menyeluruh internal (status branch, file, test suite, dan kesiapan merge)

## Status Fitur: Modul Batas Stok Minimum (Admin)
Modul Batas Stok Minimum (sesuai prototype Figma frame P14) telah diimplementasikan secara mandiri pada branch ini:
- **Kotak Notifikasi Otomatis Telegram**: Banner peringatan informatif berwarna kuning lembut (`bg-amber-50 text-amber-900 border border-amber-200`) terkait pengiriman notifikasi otomatis ke bot Telegram saat stok mencapai atau di bawah batas minimum (format teks bersih tanpa ikon grafis).
- **Pencarian & Filter Cepat**:
  - Kolom pencarian teks bersih dengan placeholder `"Cari nama atau kode barang..."`.
  - Dropdown filter jenis barang (`Semua Jenis`, `ATK`, `Fotocopy`).
- **Tombol Pengaturan Cepat (Mass Action)**:
  - Tombol aksi cepat `"Atur semua ke 3 pcs"` dan `"Atur semua ke 5 pcs"` dengan touch-target minimal 44px (`min-h-[2.75rem]`).
- **Tabel Data Stok & Input Stepper**:
  - Kolom tabel lengkap: `Kode`, `Nama Barang`, `Jenis`, `Stok Sekarang`, `Batas Minimum`, dan `Status`.
  - Input kuantitas batas minimum dilengkapi tombol stepper minus `[-]` dan plus `[+]` berbasis teks murni dengan ukuran interaktif minimal 44px.
  - Lima data contoh realistis sesuai Figma P14:
    - `BRG-008` (Pensil 2B) — 0 pcs — Batas: 3 — Status: **Habis** (`bg-red-100 text-red-700`)
    - `BRG-006` (Isi Staples No.10) — 2 box — Batas: 3 — Status: **Menipis** (`bg-amber-100 text-amber-800`)
    - `BRG-003` (Kertas HVS A4 70gr) — 4 rim — Batas: 5 — Status: **Menipis** (`bg-amber-100 text-amber-800`)
    - `BRG-005` (Stapler HD-10) — 8 pcs — Batas: 5 — Status: **Normal** (`bg-blue-100 text-blue-700`)
    - `BRG-001` (Pulpen Standard AE7) — 120 pcs — Batas: 5 — Status: **Aman** (`bg-emerald-100 text-emerald-800`)
- **Bilah Aksi Perubahan (Bottom Action Bar)**:
  - Panel mengambang di bawah tabel yang memuat indikator perubahan ("2 perubahan batas minimum belum disimpan").
  - Tombol aksi sekunder **Batalkan** dan tombol aksi utama hijau **Simpan Perubahan** (`bg-emerald-600 hover:bg-emerald-700 text-white`).
- **Kepatuhan UI/UX (K12) & Skala Global 80% (K18)**: Bebas tag `<svg>`, bebas ikon grafis dan emoji, ukuran font minimal 16px (`text-base`), touch-target tombol minimal 44px, dan proporsional pada browser zoom 100%.

## Arsitektur Skala Tampilan Global (K12 Adaptif)
Untuk memastikan antarmuka pada zoom browser 100% presisi dengan desain Figma (lebar sidebar ~260px, bukan 325px yang terjadi jika font-size akar tidak disesuaikan):
- Menggunakan partial global terpusat: `resources/views/partials/skala.blade.php`.
- Skala akar halaman ditetapkan sebesar `80%` (`:root { --skala: 80%; } html { font-size: var(--skala); }`).
- Seluruh elemen tetap mematuhi standar touch-target K12: ukuran tombol minimal `2.75rem` (setara 44px pada font basis 16px) dan font minimum `1rem` (`text-base`).
- Kontainer sidebar menerapkan `min-h-screen flex flex-col justify-between` sehingga kartu profil pengguna ("Admin / Pemilik") dan tombol "Keluar" di pojok kiri bawah selalu terkunci rapi tanpa terpotong.

## Isolasi Arsitektur & Decoupling Sesi
Untuk mencegah konflik (*conflict*) merge dengan modul Autentikasi dan 2FA yang dikembangkan rekan tim:
- Rute modul didaftarkan mandiri di `routes/admin_batas_stok.php`.
- Rute dimuat melalui blok `then:` pada `bootstrap/app.php` tanpa memodifikasi `routes/web.php`.
- Controller mandiri ditempatkan di `app/Http/Controllers/Admin/BatasStokController.php`.
- Middleware `auth` dan `role:ADMIN` dilepas sementara di tingkat rute lokal untuk mempermudah verifikasi tampilan independen (dengan pengingat pemulihan sebelum penggabungan ke branch `staging`/`main`).
- Pembacaan profil pengguna pada view menggunakan fallback aman null-safe: `Auth::user()?->nama ?? 'Admin / Pemilik'`.

## Cara Menjalankan (Lingkungan Lokal)
1. Beralih ke branch kerja:
   ```bash
   git checkout Rossi/Minimum-Stock_Admins