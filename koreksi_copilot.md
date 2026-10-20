# Dokumen Final: Autentikasi, Role, dan Seluruh Modul Admin (Toko Firo)

## 0. Konteks
- Laravel 12, PHP 8.4 (FlyEnv), MariaDB 13.1.1. Windows, PowerShell.
- Perintah PHP: & "C:\Program Files\FlyEnv-Data\env\php\php.exe"
- Frontend: Blade + Tailwind CSS, tanpa Livewire dan tanpa Alpine.js. Interaktivitas dinamis memakai JavaScript biasa (vanilla JS).
- Desain Antarmuka: Sesuai prototype Figma frame P10 (Dashboard), P11 (Laporan Penjualan), P12 (Kelola Barang), P13 (Kelola Akun), P14 (Batas Stok), P15 (Backup Data), P16 (Log Aktivitas), dan P21 (Pembelian Restock).
- Role: ADMIN dan KASIR. Status pengguna: AKTIF atau NONAKTIF.

---

## 1. Aturan Kerja (Wajib)
1. Satu sub-langkah per sesi. Setelah selesai: jalankan test, tulis ringkasan file yang berubah, lalu tunggu arahan user.
2. Baca file yang relevan sebelum mengubah apa pun. Jangan menebak isinya.
3. Jangan membaca atau menampilkan .env. Kredensial tidak boleh masuk ke kode atau laporan.
4. Jangan ubah User.php, file lama di database/sql/, atau migrasi lama milik tim.
5. Nama kolom, tabel, dan procedure diambil dari file SQL riil di database/sql/.
6. Jika ada yang tidak jelas: tulis TODO dan berhenti. Jangan mengarang.
7. Jangan commit atau push tanpa izin eksplisit.
8. Jangan menjalankan file SQL secara otomatis; pemilik menjalankannya secara terkontrol.
9. Modul yang dikerjakan pada branch admin:
   - Modul Dashboard Admin (Figma P10)
   - Modul Laporan Penjualan (Figma P11)
   - Modul Kelola Data Barang (Figma P12)
   - Modul Kelola Akun Pengguna (Figma P13)
   - Modul Batas Stok Minimum (Figma P14)
   - Modul Backup Data (Figma P15)
   - Modul Log Aktivitas (Figma P16)
   - Modul Pembelian Barang / Restock (Figma P21)
   - Standardisasi Skala Tampilan Global K12 & K18

---

## 2. Keputusan Pemilik (Final K1 s.d. K19)
- **K1 Skema**: Sumber kebenaran adalah file SQL 01 sampai 05 dan 09, bukan migrasi Laravel. Tabel `pengguna` dibuat oleh `02_tabel_baru.sql`. JANGAN membuat migrasi Laravel untuk pengguna.
- **K2 Model**: `App\Models\Pengguna` (tabel `pengguna`, primary key `id_pengguna`, kolom `password_hash`). `User.php` tidak diubah.
- **K3 Login**: Username dan password, `Auth::attempt`, hanya status AKTIF.
- **K4 Baca daftar barang**: Eloquent, paginasi 7 per halaman.
- **K5 Tulis data**: Stored procedure. Tambah: `sp_create_item`. Ubah data: `sp_update_item_v2`. Ubah harga: `sp_update_harga`. Hapus: `sp_delete_item_v2`.
- **K6 Akses**: Halaman Admin HANYA untuk role ADMIN. Role KASIR mendapat 403 Forbidden. Sidebar menampilkan nama pengguna dan perannya ("Admin / Pemilik"), serta tombol teks "Keluar".
- **K7 Jejak audit**: Setiap tambah, ubah, harga, dan hapus tercatat di `log_aktivitas` (pelaku, waktu, data sebelum dan sesudah).
- **K8 Kode barang**: Bebas (angka atau huruf), tidak kosong, maksimal 20 karakter, tidak boleh kembar. Pesan jika kembar: "Kode barang sudah dipakai."
- **K9 Stok awal saat tambah barang**: 0 sampai 1000, satu konstanta di satu tempat. Tipe Jasa dikecualikan (database memberi stok 999999). Form ubah data TIDAK punya kolom stok; stok berubah lewat Pembelian dan Penjualan.
- **K10 Harga**: Form hanya punya Harga Pokok dan Harga Jual, keduanya diketik manual. TIDAK ada kolom persen atau perhitungan otomatis. Jika Harga Jual < Harga Pokok, tampilkan teks "Peringatan: Harga Jual lebih rendah dari Harga Pokok". Data tetap boleh disimpan.
- **K11 Batas Minimum**: Ada di form tambah (default 5, catatan kecil "Dipakai untuk notifikasi stok menipis"), dikirim ke `p_batas`, dan bisa diubah di form ubah data. Status stok: Habis jika stok = 0. Menipis jika stok <= batas_minimum. Aman selainnya.
- **K12 Tampilan (Aksesibilitas & Anti-SVG/Emoji)**: 
  - Penegasan ketat: **DILARANG** menggunakan tag `<svg>`, ikon grafis visual, lonceng notifikasi, dan karakter emoji apa pun (seperti 👋, 🔔, 💾, ⏰, 📥, ⚠️, 💡).
  - Seluruh ikon diganti dengan tipografi bersih, badge status berwarna lembut (*soft colors*), indikator teks tebal, dan tombol teks jelas (misal: "Catat Restock", "Ekspor CSV", "Unduh", "Atur Jadwal Backup").
  - Ukuran font minimal 16px (`text-base` / `1rem`).
  - Target sentuh (*touch-target*) interaktif tombol minimal 44px (`min-h-[2.75rem]` / `min-w-[2.75rem]`).
  - Bahasa Indonesia sederhana dan konsisten.
- **K13 Pesan error database**: Ditampilkan sebagai kalimat sederhana. Hapus ditolak karena ada transaksi: "Barang tidak bisa dihapus karena sudah pernah dijual."
- **K14 Hapus**: Dipakai untuk barang yang salah input. Barang yang sudah punya riwayat transaksi tidak bisa dihapus (ditolak database).
- **K15 Catatan riwayat**: Harus bisa dibaca manusia: jenis aksi, kode dan nama barang, pelaku, waktu.
- **K16 Form Tambah Barang Baru**: Urutan dan label sesuai Figma. Input Kode Item, Nama Item (*), Jenis, Merek, Rak, Satuan, Tipe Item, Stok Awal, Harga Pokok, Harga Jual, Batas Minimum, Keterangan.
- **K17 Spesifikasi Modul Laporan Penjualan (Figma P11)**:
  - Filter rentang waktu: Hari Ini, 7 Hari Terakhir, Bulan Ini, dan Kustom (input tanggal mulai & tanggal akhir dengan validasi `Y-m-d`).
  - 3 kartu metrik ringkasan: Total Penjualan Kotor, Total Transaksi, Laba Bersih Estimasi.
  - Riwayat transaksi tabel dengan paginasi 5 item per halaman. Subtotal negatif (retur) otomatis berlabel merah (`text-red-600`).
  - Tombol Unduh Laporan (PDF) memicu cetak antarmuka browser (`window.print()`).
- **K18 Standardisasi Skala Tampilan Global (Figma 100% = 80% Skala Akar)**:
  - Menggunakan partial global terpusat: `resources/views/partials/skala.blade.php`.
  - Mengatur `--skala: 80%` pada `:root` dan diterapkan pada `html { font-size: var(--skala); }` sehingga tampilan zoom 100% di peramban sama persis dengan Figma zoom 100% (lebar sidebar ~260px, proporsi kartu dan modal pas di layar).
  - Seluruh tombol dan form tetap memenuhi K12 dengan satuan `rem` (contoh: 44px $\to$ `2.75rem`).
- **K19 Isolasi Rute Mandiri per Modul (Zero-Conflict Architecture)**:
  - Setiap modul admin memiliki berkas rute tersendiri di direktori `routes/` dengan pola `routes/admin_*.php` (misal: `routes/admin_dashboard.php`, `routes/admin_batas_stok.php`, `routes/admin_backup.php`, dll).
  - Pemuatan dilakukan dinamis di `bootstrap/app.php` via fungsi `glob(base_path('routes/admin_*.php'))`.
  - Berkas rute utama `routes/web.php` milik rekan tim (Adit) sama sekali tidak disentuh untuk menjamin **nol konflik** (*zero conflict*) saat penggabungan branch ke `staging` atau `main`.
  - Middleware autentikasi dilepas sementara di tingkat rute lokal (`middleware(['web'])`) untuk kemudahan pengujian independen prototype.

---

## 3. Status Akhir Implementasi Seluruh Modul Admin (Per 20 Oktober 2026)

Seluruh 7 modul admin telah 100% tuntas diimplementasikan dan teruji:
1. **Dashboard Admin (Figma P10)**: Sapaan halo tanpa emoji tangan, 4 kartu ringkasan, diagram batang penjualan 7 hari murni Tailwind CSS (tanpa Chart.js), top barang, dan aktivitas terbaru.
2. **Laporan Penjualan (Figma P11)**: 3 kartu ringkasan penjualan, riwayat tabel transaksi dengan badge retur merah, paginasi 5 item, dan tombol cetak PDF.
3. **Kelola Data Barang (Figma P12)**: Full CRUD Stored Procedure MariaDB, audit trail, paginasi 7 baris, dan 4 modal dialog responsif zoom 100%.
4. **Kelola Akun Pengguna (Figma P13)**: Manajemen akun admin dan kasir, status AKTIF/NONAKTIF, dan modal dialog ubah password.
5. **Batas Stok Minimum (Figma P14)**: Banner bot Telegram teks bersih, stepper tombol teks `[-]` dan `[+]`, tombol aksi cepat 3/5 pcs, dan bilah simpan mengambang (*floating bar*).
6. **Backup Data (Figma P15)**: Kartu Backup Manual dan Otomatis, tabel riwayat pencadangan, tombol unduh teks murni, dan peringatan server lokal.
7. **Log Aktivitas (Figma P16)**: Tombol teks "Ekspor CSV", 3 dropdown filter, tabel riwayat jejak audit 10 baris dengan badge warna lembut, dan footer info jumlah log.
8. **Pembelian Barang / Restock (Figma P21)**: Dua tab interaktif ("Pembelian Baru" & "Riwayat"), kartu perlu segera restock, dan tombol aksi "Catat Restock".

## 4. Status Akhir Implementasi
Seluruh tahapan L0 hingga L16 telah selesai diimplementasikan 100%:
- CRUD Berbasis Stored Procedure MariaDB (`sp_create_item`, `sp_update_item_v2`, `sp_update_harga`, `sp_delete_item_v2`).
- Audit trail tercatat lengkap di `log_aktivitas`.
- Tampilan antarmuka memenuhi K12 (tanpa SVG/ikon, font >= 16px, tombol touch-target >= 44px).
- Modal box didesain ulang dengan sticky header & footer serta scrollable body untuk pengalaman optimal pada browser zoom 100%.
- Rute terisolasi independen di `routes/admin_barang.php` untuk mencegah bentrok saat merge ke branch `staging`.
- Dokumen serah terima telah dibuat di `HANDOFF_CRUD_TOKO_FIRO.md`.
