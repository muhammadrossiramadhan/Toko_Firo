# HANDOFF RESMI: MODUL KELOLA DATA BARANG, LAPORAN PENJUALAN, & PEMBELIAN BARANG (TOKO FIRO)
Tanggal Pembaruan: 20 Oktober 2026  
Penanggung Jawab: Muhammad Rossi Ramadhan (Ketua / Database, Modul Barang, Laporan Penjualan, dan Pembelian Barang)  
Branch Aktif Saat Ini: `Rossi/Sales_Report_Admins`  
Branch Rilis Modul Barang: `Rossi/Manage-Product-Data-Items_Admins`  
Target Basis PR: `staging`  

---

## 1. PANDUAN UNTUK AI / DEVELOPER KOLABORATOR
- **DILARANG MERUSAK ATAU MENGUBAH** arsitektur MVC yang sudah selesai:
  - Modul Kelola Data Barang (`app/Models/ItemBarang.php`, `app/Http/Controllers/Admin/BarangController.php`, `resources/views/admin/barang/`).
  - Modul Laporan Penjualan (`app/Http/Controllers/Admin/LaporanPenjualanController.php`, `resources/views/admin/laporan/`).
  - Modul Pembelian Barang Restock (`app/Http/Controllers/Admin/PembelianController.php`, `resources/views/admin/pembelian/`).
  - Partial Skala Tampilan (`resources/views/partials/skala.blade.php`).
- Seluruh rute modul ini **terisolasi secara mandiri** di file terpisah (`routes/admin_barang.php`, `routes/admin_laporan.php`, `routes/admin_pembelian.php`), dimuat secara otomatis via glob di `bootstrap/app.php`, sehingga **tidak pernah menyentuh `routes/web.php` milik rekan tim (Adit)**.
- Tampilan antarmuka 100% mematuhi aturan K12:
  - Tanpa tag `<svg>`, tanpa ikon grafis, tanpa emoji, tanpa lonceng atau segitiga peringatan.
  - Font minimal 16px (`text-base` / 1rem).
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

### C. Modul Pembelian Barang (Restock - Figma P21)
- **Controller**: `app/Http/Controllers/Admin/PembelianController.php`.
- **Dua Tab Interaktif**: "Pembelian Baru" dan "Riwayat Pembelian" dikendalikan via JavaScript biasa (vanilla JS), tab aktif berwarna hijau.
- **Kartu Perlu Segera Restock**: Menampilkan daftar barang dengan stok menipis atau habis berlatar kuning muda (3 data contoh: Pensil 2B - Habis, Isi Staples No.10 - Menipis, Kertas HVS A4 - Menipis), teks status merah tebal, dan tombol aksi "Beli" hijau (dengan catatan TODO: modal pembelian belum ada di Figma).
- **Tabel Riwayat Pembelian**: Tabel riwayat pengadaan barang (4 baris data transaksi contoh: Waktu, Kode Pembelian, Barang, Jumlah, Total, Status).
- **Rute Terisolasi**: `routes/admin_pembelian.php` (tanpa menyentuh `routes/web.php`).

---

## 3. STRUKTUR FILE & DIREKTORI
```
app/
├── Http/Controllers/Admin/
│   ├── BarangController.php            # Controller CRUD Barang (SP MariaDB)
│   ├── LaporanPenjualanController.php  # Controller Laporan Penjualan (Figma P11)
│   └── PembelianController.php         # Controller Pembelian Barang (Figma P21)
├── Models/
│   ├── ItemBarang.php                  # Model pembaca daftar_item
│   └── Pengguna.php                    # Model auth pengguna MariaDB
bootstrap/
└── app.php                             # Pemuat otomatis routes/admin_*.php via glob
resources/views/
├── admin/
│   ├── barang/
│   │   ├── index.blade.php             # View utama tabel barang
│   │   └── modals.blade.php            # Modal Tambah, Ubah, Harga, Hapus
│   ├── laporan/
│   │   ├── penjualan.blade.php         # View utama laporan penjualan
│   │   └── pagination.blade.php        # Komponen paginasi laporan
│   └── pembelian/
│       └── index.blade.php             # View utama pembelian barang restock
└── partials/
    └── skala.blade.php                 # Standardisasi skala tampilan 80% (Figma 100%)
routes/
├── admin_barang.php                    # Rute mandiri modul data barang
├── admin_laporan.php                   # Rute mandiri modul laporan penjualan
└── admin_pembelian.php                 # Rute mandiri modul pembelian barang
tests/Feature/
├── BarangAdminAccessTest.php           # Uji akses & otorisasi modul barang
├── BarangCrudTest.php                  # Uji operasi tulis SP & audit trail
├── BarangDaftarTest.php                # Uji daftar, filter, pencarian, paginasi
├── LaporanPenjualanAccessTest.php      # Uji akses, filter rentang, dan UI laporan
├── PembelianAdminTest.php              # Uji akses, UI restock, skala, dan tab pembelian
└── TampilanSkalaTest.php               # Uji skala tampilan 80% antar-modul
```

---

## 4. STATUS TEST SUITE
Hasil pengujian menyeluruh via `php artisan test`:
- **Total Test**: 46 tests (144 assertions)
- **Status**: 38 Passed, 8 Skipped, 0 Failed
- *Catatan Skipped*: 8 test yang dilewati adalah pengujian middleware auth (2 di `BarangAdminAccessTest`, 2 di `BarangCrudTest`, 2 di `LaporanPenjualanAccessTest`, dan 2 di `PembelianAdminTest`) karena middleware `auth` dan `role:ADMIN` sengaja dilepas sementara untuk memungkinkan pengujian independen tanpa bentrok dengan modul auth tim.

---

## 5. TODO & LANGKAH KERJA SAAT MERGE KE STAGING
1. **Pulihkan Middleware Autentikasi**:
   - Buka `routes/admin_barang.php`, `routes/admin_laporan.php`, dan `routes/admin_pembelian.php`.
   - Aktifkan kembali middleware `auth` dan `role:ADMIN` pada grup rute sebelum membuka Pull Request ke `staging`.
2. **Koneksi Backend Database Laporan Penjualan & Pembelian**:
   - Definisikan view `v_laporan_penjualan` dan sesuaikan parameter stored procedure `sp_get_laporan_penjualan` pada MariaDB.
   - Sambungkan controller pembelian ke stored procedure pembelian MariaDB (`sp_create_pembelian`, dll) dan buat modal pembelian jika desain Figma sudah tersedia.
3. **Resolusi File Konfigurasi**:
   - File `routes/web.php` tidak diubah, sehingga tidak akan menimbulkan merge conflict.
   - Pemuatan rute admin dilakukan dinamis melalui `glob(base_path('routes/admin_*.php'))` di `bootstrap/app.php` sehingga penambahan rute baru tidak perlu mengedit `bootstrap/app.php` berulang kali.
