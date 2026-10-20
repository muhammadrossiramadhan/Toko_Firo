# Toko Firo

Sistem informasi berbasis web untuk pengelolaan data master barang, stok inventori, riwayat transaksi, laporan penjualan, pembelian restock, serta manajemen akun pengguna dan hak akses pada Toko Firo (usaha ATK dan fotokopi).

Dikerjakan untuk mata kuliah Sistem Informasi / Rekayasa Perangkat Lunak, Universitas Negeri Malang, 2026.

## Anggota Kelompok
- **Muhammad Rossi Ramadhan** (250535625057) — Ketua Kelompok, Database, Modul Data Barang, & Modul Kelola Akun Pengguna
- **Muhammad Totti Al Fikri** (250535630175) — Backend Developer
- **Refina Aprilia Dewi** (250535624776) — UI/UX Designer & Analis Sistem
- **Teofilus Aditya Suseno Pandeangan** (250535626205) — Frontend, Backend, & QA Tester

## Teknologi & Lingkungan Pengembangan
- **Framework**: Laravel 12.0
- **Bahasa Pemrograman**: PHP 8.4 (FlyEnv / PHP 8.2+)
- **Database**: MariaDB 13.1.1 (dengan integritas logika pada Stored Procedure, View, & Trigger)
- **Frontend & Styling**: Blade Templates & Tailwind CSS (tanpa Livewire dan tanpa Alpine.js; interaktivitas modal dan filter berbasis Vanilla JavaScript murni)
- **Lingkungan Lokal**: Windows 11 dengan FlyEnv runtime environment

## Struktur Dokumentasi
- `README.md`: Dokumentasi utama proyek, arsitektur, panduan instalasi, dan spesifikasi modul.
- `HANDOFF_CRUD_TOKO_FIRO.md`: Panduan serah terima teknis resmi, isolasi rute mandiri, dan mitigasi merge modul ke branch staging.
- `DATABASE.md`: Dokumentasi skema basis data lengkap (ERD, DDL tabel, view, stored procedure, trigger, hak akses, dan tata cara seed/testing).
- `TRACKING.md`: Log pengerjaan berkala, milestone, dan status historis fitur yang dikembangkan.
- `koreksi_copilot.md`: Keputusan teknis final pemilik sistem (aturan K1 sampai K19) dan alur kerja L0–L16.
- `PROJECT_STATUS.md`: Laporan status teknis internal proyek (pelacakan status rute, test suite, dan kesiapan merge).

---

## Status Fitur: Modul Kelola Akun Pengguna (Branch: `Rossi/User-Settings`)
Modul Kelola Akun Pengguna telah diimplementasikan sesuai desain **Figma frame P13 (Kelola Akun Pengguna / Desktop / Default)** dengan pemisahan dependensi (decoupling) yang aman:

1. **Daftar & Detail Akun Pengguna**:
   - Menampilkan tabel akun pengguna terdaftar dengan avatar lingkaran inisial huruf dinamis (warna hijau untuk Admin, biru untuk Kasir, dan slate untuk nonaktif).
   - Menampilkan kolom data: Nama Lengkap, Username, Role (Badge "Admin" hijau muda dan "Kasir" biru muda), Status ("• Aktif" / "• Nonaktif"), Waktu Login Terakhir, serta Tombol Aksi.
   - Menggunakan dataset representatif Figma P13:
     - `Junaidi Saleh` (`junaidi` — Role: `ADMIN`, Status: `AKTIF`, Inisial: `J`, Login Terakhir: `03 Okt 2026, 08:15`)
     - `Rina Putri` (`rina` — Role: `KASIR`, Status: `AKTIF`, Inisial: `R`, Login Terakhir: `03 Okt 2026, 07:30`)
     - `Siti Aminah` (`siti` — Role: `KASIR`, Status: `NONAKTIF`, Inisial: `S`, Login Terakhir: `28 Sep 2026, 14:00`)
2. **Pencarian Pengguna & Penambahan Akun**:
   - Input pencarian pengguna dengan *placeholder* `"Cari pengguna..."` (tinggi minimal 44px, font 16px).
   - Tombol aksi primer `+ Tambah Akun` berwarna hijau (`bg-emerald-600`) dengan touch-target interaktif minimal 44px.
3. **Manajemen Status & Hak Akses (Aksi Per Baris)**:
   - Tombol **Ubah**: Untuk pengeditan profil dan role akun.
   - Tombol **Reset Password**: Untuk pengaturan ulang kata sandi pengguna.
   - Tombol **Nonaktifkan / Aktifkan**: Mengubah status keaktifan akun secara kontekstual (merah untuk nonaktifkan akun aktif, hijau untuk aktifkan akun nonaktif).
4. **Footer Informasi**:
   - Menampilkan teks rekapitulasi data: `"Menampilkan 3 pengguna terdaftar"`.

---

## Fitur Modul Terkait Lainnya (Admin)

### 1. Modul Kelola Data Barang & Riwayat Transaksi (Figma P12)
- Pembacaan data riil tabel `daftar_item` via Eloquent model `ItemBarang` dengan paginasi 7 baris.
- Perhitungan status stok real-time: `Habis` (stok = 0), `Menipis` (stok ≤ batas_minimum), dan `Aman`.
- Pencarian multi-kolom (`q`), filter dropdown Jenis dinamis, serta 3 opsi urutan (Terbaru, Nama A-Z, Stok Terkecil).
- Operasi tulis tersimpan penuh via Stored Procedure MariaDB: `sp_create_item`, `sp_update_item_v2`, `sp_update_harga`, dan `sp_delete_item_v2`.
- Modal interaktif (Tambah, Ubah Data, Ubah Harga, Hapus) yang responsif pada zoom browser 100% (`max-h-[90vh]` dengan header/footer *sticky*).
- Halaman **Riwayat Transaksi** (`admin.barang.riwayat`) menampilkan 8 riwayat transaksi kasir, rincian items, total nominal, metode (Tunai/QRIS), dan status selesai.

### 2. Modul Pembelian Barang Restock (Figma P21)
- Dua tab interaktif via Vanilla JavaScript: Tab "Pembelian Baru" dan Tab "Riwayat Pembelian".
- Kartu "Perlu Segera Restock" yang memuat item dengan stok kritis (Pensil 2B, Isi Staples No.10, Kertas HVS A4) dan tombol aksi cepat "Catat Restock".
- Tabel riwayat transaksi pembelian barang dengan penomoran nota pengadaan dan badge status selesai.

### 3. Modul Laporan Penjualan (Figma P11)
- 3 kartu ringkasan metrik: Total Penjualan Kotor, Jumlah Transaksi Nota, dan Laba Bersih Estimasi.
- Filter rentang waktu terpadu (Hari Ini, 7 Hari Terakhir, Bulan Ini, dan Rentang Kustom dengan validasi tanggal `Y-m-d`).
- Tabel transaksi berpaginasi 5 item, indikator subtotal retur negatif berwarna merah, dan fungsi cetak laporan PDF via `window.print()`.

---

## Arsitektur Skala Tampilan Global (K12 & K18)
Untuk memastikan antarmuka pada zoom peramban 100% presisi dan identik dengan desain Figma (lebar sidebar ~260px dan rasio elemen proporsional tanpa perlu memperkecil zoom browser):
- Menggunakan parsial terpusat: `resources/views/partials/skala.blade.php`.
- Skala akar halaman ditetapkan sebesar `80%`:
  ```css
  :root { --skala: 80%; }
  html { font-size: var(--skala); }

- Seluruh elemen mematuhi standar aksesibilitas K12:
  - **Bebas SVG & Grafis**: Dilarang menggunakan tag `<svg>`, ikon grafis eksternal, atau emoji (seluruh aksi menggunakan tombol teks yang tegas).
  - **Ukuran Font**: Tetap minimal `1rem` / 16px (`text-base`).
  - **Touch-Target**: Tombol interaktif dan input form wajib minimal `2.75rem` / 44px (`min-h-[2.75rem]`).

---

## Isolasi Arsitektur & Decoupling Rute
Untuk mencegah konflik (*merge conflict*) dengan modul Autentikasi/2FA milik rekan tim pada branch `staging`:
- Rute modul didaftarkan secara independen:
  - `routes/admin_barang.php` $\to$ Prefix `/admin/barang`
  - `routes/admin_pengguna.php` $\to$ Prefix `/admin/pengguna`
  - `routes/admin_pembelian.php` $\to$ Prefix `/admin/pembelian`
  - `routes/admin_laporan.php` $\to$ Prefix `/admin/laporan-penjualan`
- Seluruh file rute dimuat via `bootstrap/app.php` tanpa menyentuh file `routes/web.php` milik rekan tim.
- Middleware `auth` dan `role:ADMIN` dilepas sementara pada grup rute lokal (diberi penanda `TODO WAJIB` sebelum rilis/merge ke `staging`).
- Pembacaan profil pengguna pada view dan controller menerapkan null-safety:
  ```php
  Auth::user()?->nama ?? 'Admin / Pemilik'
  ```

---

## Panduan Menjalankan Sistem (Lokal)

1. **Clone repositori dan beralih ke branch kerja:**
   ```bash
   git checkout Rossi/User-Settings
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
   - **Kelola Akun Pengguna**: `http://127.0.0.1:8000/admin/pengguna`
   - **Kelola Data Barang**: `http://127.0.0.1:8000/admin/barang`
   - **Riwayat Transaksi**: `http://127.0.0.1:8000/admin/barang/riwayat`
   - **Pembelian Barang (Restock)**: `http://127.0.0.1:8000/admin/pembelian`
   - **Laporan Penjualan**: `http://127.0.0.1:8000/admin/laporan-penjualan`

---

## Pengujian Otomatis (Testing)
Pengujian modul dijalankan menggunakan PHPUnit / Pest dengan trait `DatabaseTransactions` untuk menjaga integritas data:

```bash
# Jalankan seluruh test suite
php artisan test

# Pengujian khusus modul Kelola Akun Pengguna
php artisan test --filter=PenggunaAdminTest

# Pengujian modul terkait lainnya
php artisan test --filter=BarangDaftarTest
php artisan test --filter=BarangRiwayatTest
php artisan test --filter=PembelianAdminTest
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
- **`Rossi/User-Settings`**: Cabang kerja resmi untuk modul Kelola Akun Pengguna dan penyelarasan pengaturan sistem.
- **`Rossi/Manage-Product-Data-Items_Admins`**: Cabang kerja resmi untuk modul Kelola Data Barang dan Riwayat Transaksi.
```
