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

## Progress Implementasi Modul Antarmuka (Figma)
| Frame | Modul | Branch | Status |
|---|---|---|---|
| P05 | Kasir - Dashboard | Refina-Toti/Dashboard_Kasir | Selesai (Frontend Prototype K12/K18) |
| P06 | Kasir - Transaksi Penjualan | Refina-Toti/Sales-Transaction_Kasir | Selesai (Frontend Prototype K12/K18) |
| P07 | Kasir - Lihat Stok Barang | Refina-Toti/Stock-Data_Kasir | Selesai (Frontend Prototype K12/K18) |
| P08 | Kasir - Saldo Harian | Refina-Toti/Daily-Balance_Kasir | Selesai (Frontend Prototype K12/K18) |

## Status Fitur: Modul Kelola Data Barang (Admin)
Modul Kelola Data Barang telah selesai 100% dan terisolasi secara mandiri:
- **Tabel & Paginasi**: Pembacaan data riil dari tabel `daftar_item` via Eloquent model `ItemBarang` dengan paginasi 7 item per halaman.
- **Status Stok Otomatis**: Menghitung status stok secara real-time: `Habis` (stok = 0), `Menipis` (stok ≤ batas_minimum), dan `Aman` (stok > batas_minimum).
- **Pencarian, Filter, & Urutan**: Fitur pencarian kode/nama barang (`q`), dropdown filter Jenis unik dari database, dan 3 opsi pengurutan (Terbaru, Nama A-Z, Stok Terkecil) dengan persistensi query string pada paginasi.
- **Pilih Baris & Aksi Dinamis**: Seleksi baris tabel untuk mengaktifkan tombol Ubah Data, Ubah Harga, dan Hapus Data.
- **Operasi Tulis (Stored Procedure)**:
  - **Tambah Barang**: `sp_create_item` (12 input form sesuai Figma, tipe Jasa otomatis stok 999999).
  - **Ubah Data**: `sp_update_item_v2` (tanpa kolom kode, stok, dan harga).
  - **Ubah Harga**: `sp_update_harga` (mendukung teks peringatan jika harga jual < harga pokok sesuai aturan K10).
  - **Hapus Data**: `sp_delete_item_v2` (proteksi integritas transaksi dengan pesan error K13: "Barang tidak bisa dihapus karena sudah pernah dijual.").
- **Jejak Audit (Audit Trail)**: Seluruh operasi tulis otomatis dicatat ke tabel `log_aktivitas` (`ITEM_BARU`, `ITEM_DIUBAH`, `HARGA_DIUBAH`, `ITEM_DIHAPUS`).
- **Kepatuhan UI/UX (K12) & Zoom 100%**: Bebas tag `<svg>`, bebas ikon/grafis/emoji, font minimal 16px, tinggi tombol minimal 44px, serta dialog modal responsif (`max-h-[90vh]` dengan header/footer sticky dan body form scrollable sehingga proporsional pada browser zoom 100%).

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
