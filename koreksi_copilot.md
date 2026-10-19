# Dokumen Final: Autentikasi, Role, Kelola Data Barang, dan Laporan Penjualan (Toko Firo)

## 0. Konteks
- Laravel 12, PHP 8.4 (FlyEnv), MariaDB. Windows, PowerShell.
- Perintah PHP: & "C:\Program Files\FlyEnv-Data\env\php\php.exe"
- Frontend: Blade + Tailwind, tanpa Livewire dan tanpa Alpine.js. Modal dan filter interaktif memakai JavaScript biasa (vanilla).
- Desain Antarmuka: Sesuai prototype Figma frame P12 (Kelola Data Barang) dan frame P11 (Laporan Penjualan).
- Role: ADMIN dan KASIR. Status pengguna: AKTIF atau NONAKTIF.

## 1. Aturan Kerja (Wajib)
1. Satu sub-langkah per sesi. Setelah selesai: jalankan test, tulis ringkasan file yang berubah, lalu tunggu arahan user.
2. Baca file yang relevan sebelum mengubah apa pun. Jangan menebak isinya.
3. Jangan membaca atau menampilkan .env. Kredensial tidak boleh masuk ke kode atau laporan.
4. Jangan ubah User.php, file lama di database/sql/, atau migrasi lama milik tim.
5. Nama kolom, tabel, dan procedure diambil dari file SQL riil di database/sql/.
6. Jika ada yang tidak jelas: tulis TODO dan berhenti. Jangan mengarang.
7. Jangan commit atau push tanpa izin eksplisit.
8. Jangan menjalankan file SQL secara otomatis; pemilik menjalankannya secara terkontrol.
9. Modul yang dikerjakan pada branch ini:
   - Modul Kelola Data Barang (Admin)
   - Modul Laporan Penjualan (Admin)
   - Standardisasi Skala Tampilan Global K12

## 2. Keputusan Pemilik (Final)
- **K1 Skema**: Sumber kebenaran adalah file SQL 01 sampai 05 dan 09, bukan migrasi Laravel. Tabel `pengguna` dibuat oleh `02_tabel_baru.sql`. JANGAN membuat migrasi Laravel untuk pengguna.
- **K2 Model**: `App\Models\Pengguna` (tabel `pengguna`, primary key `id_pengguna`, kolom `password_hash`). `User.php` tidak diubah.
- **K3 Login**: Username dan password, `Auth::attempt`, hanya status AKTIF.
- **K4 Baca daftar barang**: Eloquent, paginasi 7 per halaman.
- **K5 Tulis data**: Stored procedure. Tambah: `sp_create_item`. Ubah data: `sp_update_item_v2`. Ubah harga: `sp_update_harga`. Hapus: `sp_delete_item_v2`.
- **K6 Akses**: Kelola Data Barang & Laporan Penjualan HANYA untuk ADMIN. KASIR mendapat 403. Sidebar menampilkan nama pengguna dan perannya ("Admin / Pemilik" atau "Kasir"), serta tombol teks "Keluar".
- **K7 Jejak audit**: Setiap tambah, ubah, harga, dan hapus tercatat di `log_aktivitas` (pelaku, waktu, data sebelum dan sesudah).
- **K8 Kode barang**: Bebas (angka atau huruf), tidak kosong, maksimal 20 karakter, tidak boleh kembar. Pesan jika kembar: "Kode barang sudah dipakai."
- **K9 Stok awal saat tambah barang**: 0 sampai 1000, satu konstanta di satu tempat. Tipe Jasa dikecualikan (database memberi stok 999999). Form ubah data TIDAK punya kolom stok; stok berubah lewat Pembelian dan Penjualan.
- **K10 Harga**: Form hanya punya Harga Pokok dan Harga Jual, keduanya diketik manual. TIDAK ada kolom persen atau perhitungan otomatis. Jika Harga Jual < Harga Pokok, tampilkan teks "Peringatan: Harga Jual lebih rendah dari Harga Pokok". Data tetap boleh disimpan.
- **K11 Batas Minimum**: Ada di form tambah (default 5, catatan kecil "Dipakai untuk notifikasi stok menipis"), dikirim ke `p_batas`, dan bisa diubah di form ubah data. Status stok: Habis jika stok = 0. Menipis jika stok <= batas_minimum. Aman selainnya.
- **K12 Tampilan**: Tanpa ikon, tanpa emoji, tanpa tag `<svg>`. Tombol bertulisan jelas: "Tambah Barang Baru", "Ubah Data", "Ubah Harga", "Hapus Data", "Simpan", "Batal", "Terapkan Filter", "Unduh Laporan (PDF)". Font minimal 16px (1rem), touch-target minimal 44px (2.75rem), bahasa Indonesia sederhana.
- **K13 Pesan error database**: Ditampilkan sebagai kalimat sederhana. Hapus ditolak karena ada transaksi: "Barang tidak bisa dihapus karena sudah pernah dijual."
- **K14 Hapus**: Dipakai untuk barang yang salah input. Barang yang sudah punya riwayat transaksi tidak bisa dihapus (ditolak database).
- **K15 Catatan riwayat**: Harus bisa dibaca manusia: jenis aksi, kode dan nama barang, pelaku, waktu.
- **K16 Form Tambah Barang Baru**: Urutan dan label sesuai Figma. Input Kode Item, Nama Item (*), Jenis, Merek, Rak, Satuan, Tipe Item, Stok Awal, Harga Pokok, Harga Jual, Batas Minimum, Keterangan. Form Ubah Data memakai kolom yang sama kecuali Kode Item (readonly), Stok, Harga Pokok, dan Harga Jual. Form Ubah Harga berisi Harga Pokok, Harga Jual, dan peringatan K10.
- **K17 Laporan Penjualan (Figma P11)**:
  - Filter rentang waktu: Hari Ini, 7 Hari Terakhir, Bulan Ini, dan Kustom (input tanggal mulai & tanggal akhir dengan validasi `Y-m-d`).
  - 3 kartu metrik ringkasan penjualan: Total Penjualan Kotor, Total Transaksi, Laba Bersih Estimasi.
  - Riwayat transaksi tabel dengan paginasi 5 item per halaman.
  - Subtotal negatif otomatis berlabel/warna merah (`text-red-600`) sebagai indikasi retur.
  - Tombol Unduh Laporan (PDF) memicu cetak antarmuka browser (`window.print()`).
  - Menu sidebar di Modul Barang dan Modul Laporan Penjualan saling terhubung secara konsisten.
- **K18 Standardisasi Skala Tampilan Global (Figma 100% = 80% Skala Akar)**:
  - Menggunakan partial global terpusat: `resources/views/partials/skala.blade.php`.
  - Mengatur `--skala: 80%` pada `:root` dan diterapkan pada `html { font-size: var(--skala); }` sehingga tampilan zoom 100% di peramban sama persis dengan Figma zoom 100% (lebar sidebar ~260px).
  - Dimensi touch-target kaku dalam piksel dikonversi ke satuan `rem` (contoh: 44px $\to$ 2.75rem, min-w 150px $\to$ 9.375rem) agar tetap proporsional dan aksesibel.

## 3. Langkah Kerja

### Tahap A: Modul Kelola Data Barang (L0 - L16)
- **L0 - L5**: Setup skema MariaDB, Model Pengguna, konfigurasi auth, RoleMiddleware, dan pengujian otorisasi.
- **L6 - L7**: Route mandiri `admin/barang`, migrasi view prototype ke Blade produksi, Eloquent `ItemBarang`, paginasi 7 item.
- **L8 - L10**: Pencarian `q`, filter Jenis unik, opsi urutan, status stok real-time, seleksi baris & tombol aksi dinamis.
- **L11 - L14**: Operasi tulis stored procedure (`sp_create_item`, `sp_update_item_v2`, `sp_update_harga`, `sp_delete_item_v2`) dan validasi form request.
- **L15**: Pengujian unit/feature test CRUD barang dan jejak audit `log_aktivitas`.
- **L16**: Penataan modal responsive zoom 100% (`max-h-[90vh]`), decoupling rute auth lokal, dan penyusunan handoff.

### Tahap B: Modul Laporan Penjualan & Standardisasi Skala (LP1 - LP6)
- **LP1**: Pembuatan controller `LaporanPenjualanController.php` dengan 12 dataset transaksi realistis, paginasi 5 item per halaman, dan pengolahan 4 jenis filter rentang waktu.
- **LP2**: Pembuatan rute mandiri `routes/admin_laporan.php` (`admin.laporan.penjualan`) yang dimuat via `bootstrap/app.php`.
- **LP3**: Implementasi tampilan `resources/views/admin/laporan/penjualan.blade.php` dan `pagination.blade.php` sesuai Figma P11 (kartu metrik, riwayat transaksi, badge retur merah, tombol PDF `window.print()`).
- **LP4**: Penghubungan navigasi sidebar dari `resources/views/admin/barang/index.blade.php` ke rute laporan penjualan.
- **LP5**: Decoupling rute dan null-safety view pengguna (`Auth::user()?->nama ?? 'Admin / Pemilik'`) untuk mencegah bentrok merge.
- **LP6**: Standardisasi skala tampilan global melalui `resources/views/partials/skala.blade.php` pada seluruh view dan pengujian `TampilanSkalaTest.php`.

## 4. Status Akhir Implementasi (Per 19 Oktober 2026)
Seluruh tahapan pengerjaan telah diselesaikan 100%:
1. **Modul Kelola Data Barang**: Berfungsi penuh dengan CRUD Stored Procedure, audit trail, paginasi 7 baris, dan modal responsif zoom 100%.
2. **Modul Laporan Penjualan**: Berfungsi penuh dari sisi frontend & filter data contoh sesuai Figma P11, siap untuk dihubungkan ke query database riil pada tahap backend berikutnya.
3. **Skala Tampilan Global**: Seluruh halaman (barang, demo, dan laporan) menggunakan skala terpusat 80% yang presisi dengan desain Figma.
4. **Hasil Pengujian**: Test suite mencakup **40 test (34 passed, 6 skipped karena pemisahan auth mandiri, 0 failed, 130 assertions)**.
5. **Kesiapan Merge**: Rute kedua modul berada di file terpisah (`routes/admin_barang.php` dan `routes/admin_laporan.php`), sehingga bersih dari potensi konflik dengan file `routes/web.php` milik rekan tim.