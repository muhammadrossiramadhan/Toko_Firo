# Dokumen Final: Autentikasi, Role, dan Kelola Data Barang (Toko Firo)

## 0. Konteks
- Laravel 12, PHP 8.4 (FlyEnv), MariaDB. Windows, PowerShell.
- Perintah PHP: & "C:\Program Files\FlyEnv-Data\env\php\php.exe"
- Frontend: Blade + Tailwind, tanpa Livewire dan tanpa Alpine.js. Modal boleh
  memakai JavaScript biasa (vanilla) yang singkat.
- Prototype halaman selesai: route /demo/barang (data dummy). Tampilan nyata
  memakai view yang sama, dengan sidebar sesuai prototype Figma (frame P12).
- Role: ADMIN dan KASIR. Status pengguna: AKTIF atau NONAKTIF.

## 1. Aturan kerja (wajib)
1. Satu sub-langkah per sesi. Setelah selesai: jalankan test, tulis ringkasan
   file yang berubah dan saran commit, lalu BERHENTI.
2. Baca file yang relevan sebelum mengubah apa pun. Jangan menebak isinya.
3. Jangan membaca atau menampilkan .env. Kredensial tidak masuk kode atau laporan.
4. Jangan ubah User.php, file lama di database/sql/, atau migrasi lama.
5. Nama kolom, tabel, dan procedure diambil dari file SQL riil di database/sql/.
6. Jika ada yang tidak jelas: tulis TODO dan berhenti. Jangan mengarang.
7. Jangan commit atau push.
8. Jangan menjalankan file SQL. Pemilik menjalankannya sendiri.
9. Di luar cakupan: halaman Dashboard (admin dan kasir), Laporan Penjualan,
   Pembelian Barang, Kelola Akun Pengguna, Batas Stok Minimum, Backup Data,
   Log Aktivitas, Pengaturan Notifikasi, semua halaman Akses Kasir, tombol
   rincian dan lonceng di kolom Aksi, 2FA, retur, notifikasi.
   Menu-menu itu tetap tampil di sidebar dengan href="#".

## 2. Keputusan pemilik (final)
- K1 Skema: sumber kebenarannya file SQL 01 sampai 05 dan 09, bukan migrasi
  Laravel. Tabel pengguna dibuat oleh 02_tabel_baru.sql. JANGAN membuat migrasi
  Laravel untuk pengguna.
- K2 Model: App\Models\Pengguna (tabel pengguna, primary key id_pengguna,
  kolom password_hash). User.php tidak diubah.
- K3 Login: username dan password, Auth::attempt, hanya status AKTIF.
- K4 Baca daftar barang: Eloquent, paginasi 7.
- K5 Tulis data: stored procedure. Tambah: sp_create_item. Ubah data:
  sp_update_item_v2. Ubah harga: sp_update_harga. Hapus: sp_delete_item_v2.
- K6 Akses: Kelola Data Barang HANYA untuk ADMIN. KASIR mendapat 403.
  Sidebar bawah menampilkan nama pengguna dan perannya sebagai teks
  ("Admin / Pemilik" atau "Kasir"), lalu tombol teks "Keluar".
- K7 Jejak audit: setiap tambah, ubah, harga, dan hapus tercatat di
  log_aktivitas (pelaku, waktu, data sebelum dan sesudah).
- K8 Kode barang: bebas (angka atau huruf), tidak kosong, maksimal 20 karakter,
  tidak boleh kembar. Pesan jika kembar: "Kode barang sudah dipakai."
- K9 Stok awal saat tambah barang: 0 sampai 1000, satu konstanta di satu tempat.
  Tipe Jasa dikecualikan (database memberi stok 999999). Form ubah data TIDAK
  punya kolom stok; stok berubah lewat Pembelian dan Penjualan.
- K10 Harga (murni sesuai Figma): form hanya punya Harga Pokok dan Harga Jual,
  keduanya diketik manual. TIDAK ada kolom persen atau perhitungan otomatis.
  Jika Harga Jual < Harga Pokok, tampilkan teks
  "Peringatan: Harga Jual lebih rendah dari Harga Pokok". Peringatan hanya
  muncul saat kondisi itu terjadi, dan data tetap boleh disimpan.
- K11 Batas Minimum: ADA di form tambah (default 5, catatan kecil
  "Dipakai untuk notifikasi stok menipis"), dikirim ke p_batas, dan bisa diubah
  di form ubah data. Status stok: Habis jika stok = 0. Menipis jika
  stok <= batas_minimum. Aman selainnya.
- K12 Tampilan: tanpa ikon, tanpa emoji, tanpa lonceng, tanpa segitiga
  peringatan. Tombol bertulisan: "Tambah Barang Baru", "Ubah Data", "Ubah Harga",
  "Hapus Data", "Simpan", "Batal". Font minimal 16px, tombol minimal 44px,
  bahasa Indonesia sederhana.
- K13 Pesan error database ditampilkan sebagai kalimat sederhana. Hapus ditolak
  karena ada transaksi: "Barang tidak bisa dihapus karena sudah pernah dijual."
- K14 Hapus dipakai untuk barang yang salah input. Barang yang sudah punya
  riwayat transaksi tidak bisa dihapus (ditolak database).
- K15 Catatan riwayat harus bisa dibaca manusia: jenis aksi, kode dan nama
  barang, pelaku, waktu. Dashboard admin dan kasir nanti dibangun darinya.
- K16 Form Tambah Barang Baru (urutan sesuai Figma, tiap input berlabel):
  Kode Item (bantuan: "Unik, maks 20 karakter"), Nama Item (wajib, ditandai *),
  Jenis, Merek, Rak, Satuan, Tipe Item (Barang atau Jasa), Stok Awal,
  Harga Pokok, Harga Jual, Batas Minimum, Keterangan (opsional).
  Tombol: Simpan dan Batal. Jenis dan Satuan: jika di DDL berupa ENUM, pakai
  pilihan sesuai ENUM; jika teks bebas, pakai input dengan daftar saran
  (datalist) dari nilai yang sudah ada. Form Ubah Data memakai kolom yang sama
  kecuali Kode Item (hanya tampil), Stok, Harga Pokok, dan Harga Jual.
  Form Ubah Harga berisi Harga Pokok, Harga Jual,
  dan peringatan K10. Frame Modal Ubah dan Modal Hapus di Figma belum tersedia;
  jika ada ketidakjelasan tulis TODO.

## 3. Langkah kerja
L0: Pemilik menguji 01 sampai 05 di database uji, lalu menerapkannya ke
    toko_firo, lalu 09. (Dikerjakan pemilik, bukan Antigravity.)
L1: Model Pengguna dan factory sesuai DDL pengguna. Override getAuthPassword
    ke password_hash. Commit: feat(auth): model Pengguna.
L2: config/auth.php providers.users.model = App\Models\Pengguna.
    Commit: chore(auth): gunakan model Pengguna.
L3: Login GET dan POST /login, logout. Update login_terakhir. Pesan NONAKTIF
    dan pesan gagal yang umum. Commit: feat(auth): halaman login.
L4: RoleMiddleware (parameter variadic), alias role di bootstrap/app.php.
    Tamu ke login, role tidak cocok abort 403. Commit: feat(auth): middleware role.
L5: Test auth: tamu, KASIR, ADMIN, NONAKTIF. Commit: test(auth): role dan status.
L6: Route nyata /admin/barang (nama admin.barang) memakai view prototype,
    middleware auth + role:ADMIN, sidebar sesuai K6. /demo/barang dibiarkan.
    Commit: feat(barang): route kelola data barang.
L7: Tabel dari Eloquent (DaftarItem), paginasi 7. Commit: feat(barang): daftar barang.
L8: Pencarian kode/nama, filter Jenis, urutan (Terbaru, Nama A-Z, Stok Terkecil).
    Commit: feat(barang): pencarian dan filter.
L9: Status stok dari batas_minimum. Commit: feat(barang): status stok.
L10: Pilih baris, tombol Ubah Data, Ubah Harga, Hapus Data aktif sesuai pilihan.
     Commit: feat(barang): pilih baris.
L11: Tambah barang (modal sesuai K16, sp_create_item, validasi K8 K9 K10 K11).
     Commit: feat(barang): tambah barang.
L12: Ubah data (modal, sp_update_item_v2).
     Commit: feat(barang): ubah data.
L13: Ubah harga (modal, sp_update_harga, peringatan K10).
     Commit: feat(barang): ubah harga.
L14: Hapus (modal konfirmasi, sp_delete_item_v2, pesan K13).
     Commit: feat(barang): hapus barang.
L15: Test CRUD dan role, termasuk bahwa log_aktivitas terisi untuk tiap aksi
     (K15) dan KASIR mendapat 403. Memakai database uji, bukan toko_firo.
     Commit: test(barang): CRUD, role, dan jejak audit.
L16: Tinjauan akhir. Test lulus atau gagal, cek tidak ada ikon, cek tidak ada
     kredensial. Daftar TODO. Tanpa commit.

Untuk setiap langkah: kerjakan HANYA satu langkah, jalankan php artisan test,
tulis ringkasan dan saran commit, lalu BERHENTI dan tunggu saya.

## 4. Status Akhir Implementasi
Seluruh tahapan L0 hingga L16 telah selesai diimplementasikan 100%:
- CRUD Berbasis Stored Procedure MariaDB (`sp_create_item`, `sp_update_item_v2`, `sp_update_harga`, `sp_delete_item_v2`).
- Audit trail tercatat lengkap di `log_aktivitas`.
- Tampilan antarmuka memenuhi K12 (tanpa SVG/ikon, font >= 16px, tombol touch-target >= 44px).
- Modal box didesain ulang dengan sticky header & footer serta scrollable body untuk pengalaman optimal pada browser zoom 100%.
- Rute terisolasi independen di `routes/admin_barang.php` untuk mencegah bentrok saat merge ke branch `staging`.
- Dokumen serah terima telah dibuat di `HANDOFF_CRUD_TOKO_FIRO.md`.