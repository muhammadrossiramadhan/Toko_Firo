# HANDOFF RESMI: MODUL KELOLA DATA BARANG & LAPORAN PENJUALAN (TOKO FIRO)
Tanggal Pembaruan: 19 Oktober 2026  
Penanggung Jawab: Muhammad Rossi Ramadhan (Ketua / Database, Modul Barang, dan Laporan Penjualan)  
Branch Aktif Saat Ini: `Rossi/Sales_Report_Admins`  
Branch Rilis Modul Barang: `Rossi/Manage-Product-Data-Items_Admins`  
Target Basis PR: `staging`  

---

## 1. PANDUAN UNTUK AI / DEVELOPER KOLABORATOR
- **DILARANG MERUSAK ATAU MENGUBAH** arsitektur MVC yang sudah selesai:
  - Modul Kelola Data Barang (`app/Models/ItemBarang.php`, `app/Http/Controllers/Admin/BarangController.php`, `resources/views/admin/barang/`).
  - Modul Laporan Penjualan (`app/Http/Controllers/Admin/LaporanPenjualanController.php`, `resources/views/admin/laporan/`).
  - Partial Skala Tampilan (`resources/views/partials/skala.blade.php`).
- Seluruh rute modul ini **terisolasi secara mandiri** di file terpisah (`routes/admin_barang.php` dan `routes/admin_laporan.php`), dimuat via `bootstrap/app.php`, sehingga **tidak pernah menyentuh `routes/web.php` milik rekan tim (Adit)**.
- Tampilan antarmuka 100% mematuhi aturan K12:
  - Tanpa tag `<svg>`, tanpa ikon grafis, tanpa emoji.
  - Font minimal 16px (`text-base`).
  - Touch-target tombol minimal 44px (dikonversi ke `2.75rem`).
  - Skala tampilan diatur seragam 80% pada tingkat root `:root { --skala: 80%; } html { font-size: var(--skala); }` agar di browser zoom 100% tepat sama persis dengan prototype Figma zoom 100% (lebar sidebar ~260px).

---

## 2. ARSITEKTUR TEKNIS & DATABASE

### A. Modul Kelola Data Barang
- **Model Baca**: `app/Models/ItemBarang.php` (tabel `daftar_item`, PK `kode_item` non-incrementing string, timestamps `dibuat_pada` dan `diubah_pada`, accessor `status_stok`: Habis jika stok 0, Menipis jika stok $\le$ batas_minimum, Aman jika selainnya).
- **Operasi Tulis (Stored Procedure MariaDB)**:
  - Tambah: `sp_create_item` (13 parameter). Tipe JASA otomatis stok 999999.
  - Ubah Data: `sp_update_item_v2` (10 parameter, tanpa kolom stok & harga).
  - Ubah Harga: `sp_update_harga` (4 parameter, mendukung peringatan jika harga jual < harga pokok).
  - Hapus: `sp_delete_item_v2` (dilarang hapus jika ada riwayat transaksi, pesan K13: "Barang tidak bisa dihapus karena sudah pernah dijual.").
- **Audit Trail**: Seluruh operasi tulis otomatis dicatat oleh trigger/stored procedure ke tabel `log_aktivitas` (`ITEM_BARU`, `ITEM_DIUBAH`, `HARGA_DIUBAH`, `ITEM_DIHAPUS`).
- **Paginasi & Filter**: Paginasi 7 item per halaman, filter Jenis unik dinamis dari database, 3 opsi urutan (Terbaru, Nama A-Z, Stok Terkecil), dan pencarian nama/kode.

### B. Modul Laporan Penjualan (Figma P11)
- **Controller**: `app/Http/Controllers/Admin/LaporanPenjualanController.php`.
- **Fitur Filter Waktu**: Pilihan cepat (Hari Ini, 7 Hari Terakhir, Bulan Ini) dan rentang tanggal kustom dengan validasi format `Y-m-d`.
- **Paginasi**: 5 transaksi per halaman menggunakan `LengthAwarePaginator`.
- **Metrik & Ringkasan**: 3 kartu metrik ringkasan (Total Penjualan Kotor, Total Transaksi, Laba Bersih Estimasi).
- **Indikator Visual**: Subtotal negatif (retur) otomatis berwana merah (`text-red-600`).
- **Cetak Laporan**: Tombol Unduh Laporan (PDF) memanfaatkan CSS print bawaan browser (`window.print()`).
- **Data Sumber**: Saat ini menggunakan 12 dataset transaksi realistis (`dataContoh()`), siap disambungkan ke query database riil pada tahap backend laporan.

---

## 3. STRUKTUR FILE & DIREKTORI
```
app/
├── Http/Controllers/Admin/
│   ├── BarangController.php            # Controller CRUD Barang (SP MariaDB)
│   └── LaporanPenjualanController.php  # Controller Laporan Penjualan (Figma P11)
├── Models/
│   ├── ItemBarang.php                  # Model pembaca daftar_item
│   └── Pengguna.php                    # Model auth pengguna MariaDB
bootstrap/
└── app.php                             # Pemuat routes/admin_barang.php & routes/admin_laporan.php
resources/views/
├── admin/
│   ├── barang/
│   │   ├── index.blade.php             # View utama tabel barang
│   │   └── modals.blade.php            # Modal Tambah, Ubah, Harga, Hapus
│   └── laporan/
│       ├── penjualan.blade.php         # View utama laporan penjualan
│       └── pagination.blade.php        # Komponen paginasi laporan
└── partials/
    └── skala.blade.php                 # Standardisasi skala tampilan 80% (Figma 100%)
routes/
├── admin_barang.php                    # Rute mandiri modul data barang
└── admin_laporan.php                   # Rute mandiri modul laporan penjualan
tests/Feature/
├── BarangAdminAccessTest.php           # Uji akses & otorisasi modul barang
├── BarangCrudTest.php                  # Uji operasi tulis SP & audit trail
├── BarangDaftarTest.php                # Uji daftar, filter, pencarian, paginasi
├── LaporanPenjualanAccessTest.php      # Uji akses, filter rentang, dan UI laporan
└── TampilanSkalaTest.php               # Uji skala tampilan 80% antar-modul
```

---

## 4. STATUS TEST SUITE
Hasil pengujian menyeluruh via `php artisan test`:
- **Total Test**: 40 tests (130 assertions)
- **Status**: 34 Passed, 6 Skipped, 0 Failed
- *Catatan Skipped*: 6 test yang dilewati adalah pengujian middleware auth (2 di `BarangAdminAccessTest`, 2 di `BarangCrudTest`, dan 2 di `LaporanPenjualanAccessTest`) karena middleware `auth` dan `role:ADMIN` sengaja dilepas sementara untuk memungkinkan pengujian independen tanpa bentrok dengan modul auth tim.

---

## 5. TODO & LANGKAH KERJA SAAT MERGE KE STAGING
1. **Pulihkan Middleware Autentikasi**:
   - Buka `routes/admin_barang.php` dan `routes/admin_laporan.php`.
   - Aktifkan kembali middleware `auth` dan `role:ADMIN` pada grup rute sebelum membuka Pull Request ke `staging`.
2. **Koneksi Backend Database Laporan Penjualan**:
   - Definisikan view `v_laporan_penjualan` dan sesuaikan parameter stored procedure `sp_get_laporan_penjualan` pada MariaDB.
   - Ganti pemanggilan `dataContoh()` pada `LaporanPenjualanController` dengan eksekusi query/stored procedure database riil.
3. **Resolusi File Konfigurasi**:
   - File `routes/web.php` tidak diubah, sehingga tidak akan menimbulkan merge conflict.
   - Jika terdapat perbedaan pada `config/auth.php` atau `bootstrap/app.php`, pastikan pendaftaran provider pengguna dan grup rute mandiri tetap dipertahankan.
