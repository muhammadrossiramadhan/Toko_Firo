# Toko Firo

Sistem informasi berbasis web untuk pengelolaan data master barang, stok inventori, riwayat transaksi, laporan penjualan, pembelian barang (restock), serta manajemen akun pengguna dan hak akses pada Toko Firo (usaha ATK dan fotokopi).

Dikerjakan untuk mata kuliah Sistem Informasi / Rekayasa Perangkat Lunak, Universitas Negeri Malang, 2026.

## Anggota Kelompok
- **Muhammad Rossi Ramadhan** (250535625057) — Ketua Kelompok, Database, Modul Data Barang, & Modul Pembelian Barang
- **Muhammad Totti Al Fikri** (250535630175) — Backend Developer
- **Refina Aprilia Dewi** (250535624776) — UI/UX Designer & Analis Sistem
- **Teofilus Aditya Suseno Pandeangan** (250535626205) — Frontend, Backend, & QA Tester

## Teknologi & Lingkungan Pengembangan
- **Framework**: Laravel 12.0
- **Bahasa Pemrograman**: PHP 8.4 (FlyEnv / PHP 8.2+)
- **Database**: MariaDB 13.1.1 (dengan integritas logika pada Stored Procedure, View, & Trigger)
- **Frontend & Styling**: Blade Templates & Tailwind CSS (tanpa Livewire dan tanpa Alpine.js; interaktivitas tab dan filter berbasis Vanilla JavaScript murni)
- **Lingkungan Lokal**: Windows 11 dengan FlyEnv runtime environment

## Struktur Dokumentasi
- `README.md`: Dokumentasi utama proyek, arsitektur, panduan instalasi, dan spesifikasi modul.
- `HANDOFF_CRUD_TOKO_FIRO.md`: Panduan serah terima teknis resmi, isolasi rute mandiri, dan mitigasi merge modul ke branch staging.
- `DATABASE.md`: Dokumentasi skema basis data lengkap (ERD, DDL tabel, view, stored procedure, trigger, hak akses, dan tata cara seed/testing).
- `TRACKING.md`: Log pengerjaan berkala, milestone, dan status historis fitur yang dikembangkan.
- `koreksi_copilot.md`: Keputusan teknis final pemilik sistem (aturan K1 sampai K19) dan alur kerja teknis.
- `PROJECT_STATUS.md`: Laporan status teknis internal proyek (pelacakan status rute, test suite, dan kesiapan merge).

---

## Status Fitur: Modul Pembelian Barang Restock (Branch: `Rossi/Purchase_Admins`)
Modul Pembelian Barang Restock telah diimplementasikan sesuai desain **Figma frame P21 (Pembelian Barang / Desktop / Restock)** dengan arsitektur terisolasi dan pemisahan dependensi (decoupling) yang aman:

1. **Dua Tab Navigasi Interaktif (Vanilla JavaScript)**:
   - Navigasi tab responsif tanpa reload halaman dan tanpa library eksternal: Tab **"Pembelian Baru"** (tab aktif hijau) dan Tab **"Riwayat Pembelian"**.
2. **Kartu "Perlu Segera Restock" (Tab Pembelian Baru)**:
   - Menampilkan daftar barang dengan status stok habis atau menipis berlatar kuning muda (`bg-amber-50` / `border-amber-200`) dengan teks nama tebal.
   - Menampilkan informasi kuantitas dan label status warna merah tegas (`text-red-600 font-semibold`):
     - `Pensil 2B` — `0 pcs (Habis)`
     - `Isi Staples No.10` — `2 box (Menipis)`
     - `Kertas HVS A4` — `4 rim (Menipis)`
   - Tombol aksi primer **"Catat Restock"** berwarna hijau (`bg-emerald-600 hover:bg-emerald-700 text-white`) dengan touch-target interaktif minimal 44px (`min-h-[2.75rem]`).
3. **Tabel Riwayat Pembelian (Tab Riwayat Pembelian)**:
   - Menampilkan catatan pengadaan barang masuk: `Waktu`, `Kode Pembelian`, `Barang`, `Jumlah`, `Total` (format Rupiah tebal), dan `Status` (badge hijau selesai).
   - Memuat 4 dataset representatif transaksi pembelian:
     - `PB-20261018-001`: Kertas HVS A4 (10 rim) — Rp 550.000 (Selesai)
     - `PB-20261017-001`: Pulpen Faster C600 (5 pak) — Rp 175.000 (Selesai)
     - `PB-20261015-001`: Buku Tulis Sinar Dunia 38 (20 pak) — Rp 760.000 (Selesai)
     - `PB-20261012-001`: Tinta Printer Epson 003 Black (3 botol) — Rp 285.000 (Selesai)

---

## Fitur Modul Terkait Lainnya (Admin)

### 1. Modul Kelola Data Barang & Riwayat Transaksi (Figma P12)
- Pembacaan data riil tabel `daftar_item` via Eloquent model `ItemBarang` dengan paginasi 7 baris.
- Perhitungan status stok real-time: `Habis` (stok = 0), `Menipis` (stok ≤ batas_minimum), dan `Aman`.
- Pencarian multi-kolom (`q`), filter dropdown Jenis dinamis, serta 3 opsi urutan (Terbaru, Nama A-Z, Stok Terkecil).
- Operasi tulis tersimpan penuh via Stored Procedure MariaDB: `sp_create_item`, `sp_update_item_v2`, `sp_update_harga`, dan `sp_delete_item_v2`.
- Modal interaktif (Tambah, Ubah Data, Ubah Harga, Hapus) yang responsif pada zoom browser 100% (`max-h-[90vh]` dengan header/footer *sticky*).
- Halaman **Riwayat Transaksi** (`admin.barang.riwayat`) menampilkan 8 riwayat transaksi kasir, rincian items, total nominal, metode (Tunai/QRIS), dan status selesai.

### 2. Modul Laporan Penjualan (Figma P11)
- 3 kartu ringkasan metrik: Total Penjualan Kotor, Jumlah Transaksi Nota, dan Laba Bersih Estimasi.
- Filter rentang waktu terpadu (Hari Ini, 7 Hari Terakhir, Bulan Ini, dan Rentang Kustom dengan validasi tanggal `Y-m-d`).
- Tabel transaksi berpaginasi 5 item, indikator subtotal retur negatif berwarna merah, dan fungsi cetak laporan PDF via `window.print()`.

### 3. Modul Kelola Akun Pengguna (Figma P13)
- Pengelolaan akun pengguna sistem (Admin & Kasir) dengan avatar lingkaran inisial huruf dinamis.
- Pengaturan hak akses, reset kata sandi, serta aktivasi dan penonaktifan akun.

---

## Arsitektur Skala Tampilan Global (K12 & K18)
Untuk memastikan antarmuka pada zoom peramban 100% presisi dan identik dengan desain Figma (lebar sidebar ~260px dan rasio elemen proporsional tanpa perlu memperkecil zoom browser):
- Menggunakan parsial terpusat: `resources/views/partials/skala.blade.php`.
- Skala akar halaman ditetapkan sebesar `80%`:
  ```css
  :root { --skala: 80%; }
  html { font-size: var(--skala); }

- Seluruh elemen mematuhi standar aksesibilitas K12:
  - **Bebas SVG & Grafis**: Dilarang menggunakan tag `<svg>`, ikon grafis eksternal, emoji, lonceng, atau segitiga peringatan (seluruh aksi menggunakan tombol teks yang tegas).
  - **Ukuran Font**: Tetap minimal `1rem` / 16px (`text-base`).
  - **Touch-Target**: Tombol interaktif dan input form wajib minimal `2.75rem` / 44px (`min-h-[2.75rem]`).

---

## Isolasi Arsitektur & Decoupling Rute
Untuk mencegah konflik (*merge conflict*) dengan modul Autentikasi/2FA milik rekan tim pada branch `staging`:
- Rute modul didaftarkan secara independen:
  - `routes/admin_pembelian.php` $\to$ Prefix `/admin/pembelian` (nama rute `admin.pembelian`)
  - `routes/admin_barang.php` $\to$ Prefix `/admin/barang`
  - `routes/admin_laporan.php` $\to$ Prefix `/admin/laporan-penjualan`
  - `routes/admin_pengguna.php` $\to$ Prefix `/admin/pengguna`
- Pemuatan rute admin dilakukan secara dinamis melalui `glob(base_path('routes/admin_*.php'))` di `bootstrap/app.php` tanpa menyentuh file `routes/web.php` milik rekan tim.
- Middleware `auth` dan `role:ADMIN` dilepas sementara pada grup rute lokal (diberi penanda `TODO WAJIB` sebelum rilis/merge ke `staging`).
- Pembacaan profil pengguna pada view dan controller menerapkan null-safety:
  ```php
  Auth::user()?->nama ?? 'Admin / Pemilik'
  ```

---

## Panduan Menjalankan Sistem (Lokal)

1. **Clone repositori dan beralih ke branch kerja:**
   ```bash
   git checkout Rossi/Purchase_Admins
   ```

2. **Instal dependensi PHP:**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`):**
   - Salin file konfigurasi:
     ```bash
     Copy-Item .env.example .env
     ```
   - Generate Application Key:
     ```bash
     php artisan key:generate
     ```
   - Sesuaikan konfigurasi database MariaDB pada `.env` (`DB_DATABASE=toko_firo`).

4. **Eksekusi Skrip Database MariaDB:**
   - Jalankan skrip SQL di folder `database/sql/` berurutan dari `00_preflight.sql` sampai `09_procedure_ubah_hapus.sql` pada database `toko_firo` dan `toko_firo_test`.

5. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Akses modul melalui peramban web:
   - **Pembelian Barang (Restock)**: `http://127.0.0.1:8000/admin/pembelian`
   - **Kelola Data Barang**: `http://127.0.0.1:8000/admin/barang`
   - **Riwayat Transaksi**: `http://127.0.0.1:8000/admin/barang/riwayat`
   - **Laporan Penjualan**: `http://127.0.0.1:8000/admin/laporan-penjualan`
   - **Kelola Akun Pengguna**: `http://127.0.0.1:8000/admin/pengguna`

---

## Pengujian Otomatis (Testing)
Pengujian modul dijalankan menggunakan PHPUnit / Pest dengan trait `DatabaseTransactions` untuk menjaga integritas data:

```bash
# Jalankan seluruh test suite
php artisan test

# Pengujian khusus modul Pembelian Barang Restock
php artisan test --filter=PembelianAdminTest

# Pengujian modul terkait lainnya
php artisan test --filter=BarangDaftarTest
php artisan test --filter=BarangRiwayatTest
php artisan test --filter=LaporanPenjualanAccessTest
```

---

## Aturan Keamanan & Integritas Data
- Kredensial sensitif database dan API dilarang keras di-commit ke repositori (terlindungi di `.gitignore` melalui `.env`).
- Seluruh formulir pengiriman data dilindungi token proteksi `@csrf`.
- Form modal menggunakan validasi FormRequest ketat (`StoreBarangRequest`, `UpdateBarangRequest`, dll).
- Penghapusan data barang salah input dilindungi validasi ganda di level database (Stored Procedure `sp_delete_item_v2`) untuk mencegah terhapusnya barang yang memiliki riwayat transaksi penjualan.
- Setiap aktivitas penambahan, pengubahan, perubahan harga, dan penghapusan data otomatis tercatat ke dalam tabel audit trail `log_aktivitas`.

---

## Strategi Branching Git
- **`main`**: Versi rilis produksi yang stabil dan siap guna.
- **`staging`**: Basis cabang integrasi bersama seluruh tim sebelum rilis ke `main`.
- **`Rossi/Purchase_Admins`**: Cabang kerja resmi untuk modul Pembelian Barang Restock (Figma P21).
- **`Rossi/Manage-Product-Data-Items_Admins`**: Cabang kerja resmi untuk modul Kelola Data Barang dan Riwayat Transaksi.
- **`Rossi/User-Settings`**: Cabang kerja resmi untuk modul Kelola Akun Pengguna.
- **`Rossi/Sales_Report_Admins`**: Cabang kerja resmi untuk modul Laporan Penjualan.
```