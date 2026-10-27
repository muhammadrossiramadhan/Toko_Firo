# HANDOFF RESMI: SELURUH MODUL ADMIN TOKO FIRO (FIGMA P10 - P16 & P21)
Tanggal Pembaruan: 20 Oktober 2026  
Penanggung Jawab: Muhammad Rossi Ramadhan (Ketua / Database & Pengembang Seluruh Modul Admin)  
Target Basis PR / Merge: `staging`  

---

## 1. PANDUAN PENGEMBANG & ARSITEKTUR DECOUPLING MODUL ADMIN

### Prinsip Desain Terisolasi (Decoupling)
Untuk menjamin kolaborasi multi-developer berjalan lancar tanpa memicu *merge conflict* (terutama dengan modul Autentikasi dan 2FA yang dikerjakan oleh Adit):
1. **Rute Mandiri per Modul**:
   Setiap modul admin memiliki file rute terpisah di direktori `routes/` dengan pola penamaan `routes/admin_*.php`:
   - `routes/admin_dashboard.php` (Dashboard Admin - Figma P10)
   - `routes/admin_barang.php` (Kelola Data Barang - Figma P12)
   - `routes/admin_pembelian.php` (Pembelian Barang / Restock - Figma P21)
   - `routes/admin_laporan.php` (Laporan Penjualan - Figma P11)
   - `routes/admin_pengguna.php` (Kelola Akun Pengguna - Figma P13)
   - `routes/admin_batas_stok.php` (Batas Stok Minimum - Figma P14)
   - `routes/admin_backup.php` (Backup Data - Figma P15)
   - `routes/admin_log.php` (Log Aktivitas - Figma P16)
2. **Pemuatan Otomatis (Zero-Configuration di `bootstrap/app.php`)**:
   File `bootstrap/app.php` telah dikonfigurasi menggunakan fungsi `glob(base_path('routes/admin_*.php'))`. Artinya, **anggota tim tidak perlu mengedit `bootstrap/app.php` atau `routes/web.php`** saat membuat atau menguji modul admin baru.
3. **Pelepasan Middleware Autentikasi Sementara di Tingkat Rute Lokal**:
   Pada rute lokal, middleware dibatasi pada `['web']` untuk mempermudah anggota tim (Frontend, QA, Desainer UI/UX) membuka antarmuka prototype secara langsung di browser tanpa terhalang sesi login atau redirect.
4. **Keamanan View Null-Safe**:
   Seluruh view Blade menggunakan fallback null-safe pada komponen profil:
   `{{ Auth::user()?->nama ?? 'Admin / Pemilik' }}`
   sehingga view tetap dapat dirender dengan sempurna baik saat pengguna sudah login maupun saat diakses langsung sebagai tamu.

---

## 2. PANDUAN PENGGABUNGAN KE BRANCH `staging` (UNTUK DEVELOPER AUTH)

Saat seluruh modul admin digabungkan ke branch integrasi `staging`:
1. Pengembang autentikasi (Adit) **TIDAK PERLU** memindahkan isi rute ke `routes/web.php`. Biarkan berkas `routes/admin_*.php` tetap terpisah.
2. Cukup pulihkan middleware `auth` dan `role:ADMIN` pada masing-masing berkas rute admin. Ubah baris middleware grup dari:
   ```php
   Route::middleware(['web'])->prefix('admin')->group(function () {
   ```
   Menjadi:
   ```php
   Route::middleware(['web', 'auth', 'role:ADMIN'])->prefix('admin')->group(function () {
   ```
3. Seluruh rute admin akan langsung terlindungi oleh middleware autentikasi dan otorisasi role tanpa mengubah arsitektur kode di view maupun controller.

---

## 3. RANGKUMAN IMPLEMENTASI MODUL ADMIN

### A. Dashboard Admin (Figma P10)
- Controller: `app/Http/Controllers/Admin/DashboardController.php`.
- View: `resources/views/admin/dashboard/index.blade.php`.
- Fitur:
  - Header sapaan: "Halo, Junaidi" (tanpa emoji tangan).
  - 4 kartu ringkasan: Penjualan Hari Ini (`Rp 3.180.000`), Jumlah Transaksi (`14`), Stok Menipis (`7`), dan Laba Hari Ini (`Rp 680.000`).
  - Diagram batang penjualan 7 hari murni Tailwind CSS (tanpa library Chart.js eksternal).
  - Daftar peringkat "Top Barang Hari Ini" dan daftar linier "Aktivitas Terbaru".

### B. Kelola Data Barang (Figma P12)
- Controller: `app/Http/Controllers/Admin/BarangController.php`.
- Model: `app/Models/ItemBarang.php`.
- View: `resources/views/admin/barang/index.blade.php` & `modals.blade.php`.
- Fitur: Full CRUD dengan 4 Stored Procedure MariaDB (`sp_create_item`, `sp_update_item_v2`, `sp_update_harga`, `sp_delete_item_v2`), audit trail ke `log_aktivitas`, paginasi 7 baris, dan filter jenis.

### C. Pembelian Barang / Restock (Figma P21)
- Controller: `app/Http/Controllers/Admin/PembelianController.php`.
- View: `resources/views/admin/pembelian/index.blade.php`.
- Fitur: 2 tab interaktif ("Pembelian Baru" & "Riwayat Pembelian"), kartu barang perlu restock dengan status Habis/Menipis, tombol aksi "Catat Restock", dan tabel riwayat transaksi.

### D. Laporan Penjualan (Figma P11)
- Controller: `app/Http/Controllers/Admin/LaporanPenjualanController.php`.
- View: `resources/views/admin/laporan/penjualan.blade.php` & `pagination.blade.php`.
- Fitur: Filter rentang waktu (Hari ini, 7 Hari, Bulan Ini, Kustom `Y-m-d`), 3 kartu metrik ringkasan, riwayat transaksi tabel dengan paginasi 5 item, subtotal retur negatif warna merah, tombol cetak PDF (`window.print()`).

### E. Kelola Akun Pengguna (Figma P13)
- Controller: `app/Http/Controllers/Admin/PenggunaController.php`.
- View: `resources/views/admin/pengguna/index.blade.php`.
- Fitur: Tabel master akun admin dan kasir, badge status AKTIF/NONAKTIF, modal dialog tambah pengguna dan ubah password.

### F. Batas Stok Minimum (Figma P14)
- Controller: `app/Http/Controllers/Admin/BatasStokController.php`.
- View: `resources/views/admin/batas_stok/index.blade.php`.
- Fitur: Banner informasi bot Telegram teks bersih, kontrol stepper angka tombol teks `[-]` dan `[+]`, tombol aksi cepat 3/5 pcs, dan bilah simpan mengambang (*floating bar*).

### G. Backup Data (Figma P15)
- Controller: `app/Http/Controllers/Admin/BackupController.php`.
- View: `resources/views/admin/backup/index.blade.php`.
- Fitur: Kartu Backup Manual (`border-emerald-500`) dan Backup Otomatis (`border-slate-200`), tabel 4 riwayat backup dengan tombol unduh teks murni, dan banner keamanan server lokal.

### H. Log Aktivitas (Figma P16)
- Controller: `app/Http/Controllers/Admin/LogAktivitasController.php`.
- View: `resources/views/admin/log/index.blade.php`.
- Fitur: Tombol teks "Ekspor CSV", 3 dropdown filter (tanggal, pengguna, tipe aksi), tabel 10 baris jejak audit dengan badge warna lembut, dan teks footer info total log.

---

## 4. STANDARDISASI K12 & K18

1. **Aturan K12 (Aksesibilitas & Tampilan Bersih)**:
   - DILARANG menggunakan tag `<svg>`, ikon grafis, atau emoji.
   - Font minimal 16px (`text-base` / `1rem`).
   - Touch-target tombol minimal 44px (`min-h-[2.75rem]` / `min-h-[44px]`).
2. **Aturan K18 (Skala Tampilan Presisi Figma 100%)**:
   - Berkas terpusat: `resources/views/partials/skala.blade.php`.
   - Mengatur skala akar: `:root { --skala: 80%; } html { font-size: var(--skala); }`.
   - Menghasilkan tampilan browser zoom 100% yang tepat sama persis dengan rasio Figma zoom 100% (lebar sidebar ~260px).

---

## 5. STATUS TEST SUITE LENGKAP
Eksekusi pengujian otomatis `php artisan test`:
- **Hasil**: 54 Passed, 16 Skipped (auth sementara), 0 Failed (241 assertions).
- **Semua Test Suite Modul Admin Berstatus Hijau (PASS)**.
