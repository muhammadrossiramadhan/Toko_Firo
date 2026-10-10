# Dokumen Final: Autentikasi, Role, dan Kelola Data Barang (Toko Firo)

## 0. Konteks
- Laravel 12, PHP 8.4 (sudah terpasang di FlyEnv), MariaDB.
- Lingkungan: Windows, PowerShell. Perintah PHP pakai:
  "C:\Program Files\FlyEnv-Data\env\php\php.exe"
- Frontend: Blade + controller, Tailwind. Alpine.js hanya jika sudah ada di package.json.
- Branch: Rossi-Kelola-Data-Barang-Item.
- Role di tabel pengguna: ADMIN atau KASIR. Status: AKTIF atau NONAKTIF.

## 1. Aturan kerja (wajib)
1. Satu sub-langkah per sesi. Setelah selesai: jalankan test, tulis ringkasan
   file yang berubah, tulis saran commit, lalu BERHENTI.
2. Baca file yang relevan sebelum mengubah apa pun. Jangan menebak isinya.
3. Jangan membaca atau menampilkan .env. Kredensial tidak boleh masuk kode atau laporan.
4. Jangan ubah User.php, file di database/sql/, atau migrasi yang sudah ada.
5. Nama kolom, tabel, dan procedure diambil dari DDL riil di
   database/sql/02_tabel_baru.sql dan database/sql/04_procedure.sql.
   Jika berbeda dengan DATABASE.md, ikuti DDL riil.
6. Jika ada yang tidak jelas: tulis TODO dan berhenti. Jangan mengarang.
7. Jangan commit atau push. Itu tugas pemilik.
8. Di luar cakupan: 2FA, backup, retur, laporan otomatis, notifikasi WhatsApp/Telegram.

## 2. Keputusan (sudah final)
- D1 Frontend: Blade + controller, tanpa Livewire.
- D2 Tabel pengguna: migrasi Laravel baru dari DDL 02_tabel_baru.sql.
- D3 Model: App\Models\Pengguna. User.php tidak diubah.
- D4 Login: Auth::attempt dengan Eloquent.
- D5 Baca daftar barang: Eloquent, paginasi 7.
- D6 Tulis data barang: stored procedure sp_create_item, sp_update_item,
  sp_update_harga, sp_delete_item.
- D7 Status stok: Habis jika stok 0. Menipis jika stok <= batas minimum.
  Aman lainnya. Jika sumber batas minimum tidak jelas di DDL, tulis asumsi.
- D8 Tampilan: tanpa ikon. Tombol bertulisan: "Tambah Barang Baru", "Ubah Data",
  "Hapus Data", "Simpan", "Batal". Font minimal 16px, tombol minimal 44px.
- D9 Akses Kelola Data Barang: hanya ADMIN.

## 3. Langkah kerja
Langkah 0: Inventaris (hanya baca). Baca composer.json, package.json, config/auth.php,
database/sql/02_tabel_baru.sql (CREATE TABLE pengguna), database/sql/04_procedure.sql
(signature procedure), migrasi daftar_item. Laporkan perbedaan dengan DATABASE.md.
Lalu BERHENTI.

Langkah 1: Migrasi pengguna sesuai DDL riil (make:migration create_pengguna_table).
Test: php artisan test. Commit: feat(db): migrasi tabel pengguna sesuai DDL.

Langkah 2: Model Pengguna dan factory. Commit: feat(auth): model Pengguna dan factory.

Langkah 3: config/auth.php providers.users.model = App\Models\Pengguna.
Commit: chore(auth): gunakan model Pengguna.

Langkah 4: Login (GET dan POST /login). Auth::attempt dengan status AKTIF.
Update kolom login terakhir sesuai DDL. Pesan NONAKTIF dan pesan gagal yang umum.
Commit: feat(auth): halaman login berbasis username.

Langkah 5: RoleMiddleware (parameter variadic). Tamu redirect ke login,
role salah abort 403. Daftarkan alias role di bootstrap/app.php.
Commit: feat(auth): middleware role.

Langkah 6: Feature test auth (tamu, KASIR 403, ADMIN 200, NONAKTIF gagal).
Commit: test(auth): akses berdasarkan role dan status.

Langkah 7: Route GET /admin/barang (name admin.barang, middleware auth + role:ADMIN),
halaman kosong. Commit: feat(barang): route dan halaman kelola data barang.

Langkah 8: Tabel daftar barang (Kode, Nama Item, Jenis, Merek, Rak, Satuan,
Harga Pokok, Harga Jual, Stok, Status), paginasi 7, bisa digeser horizontal.
Commit: feat(barang): tampilkan daftar barang.

Langkah 9: Pencarian (kode/nama), filter Jenis, urutan Terbaru/Nama A-Z/Stok Terkecil.
Commit: feat(barang): pencarian dan filter.

Langkah 10: Kolom status sebagai teks berwarna (Aman, Menipis, Habis). Tanpa ikon.
Commit: feat(barang): kolom status stok.

Langkah 11: Klik baris untuk memilih. Tombol Ubah Data dan Hapus Data nonaktif
sampai ada yang dipilih. Jika ditekan tanpa pilihan: "Pilih satu barang dulu".
Commit: feat(barang): pilih baris.

Langkah 12: Tambah barang via sp_create_item. Validasi kode unik, nama wajib,
harga dan stok tidak negatif. id_pengguna dari user login.
Commit: feat(barang): tambah barang via sp_create_item.

Langkah 13: Ubah data (sp_update_item) dan harga (sp_update_harga) dalam satu
DB::transaction. Kode tidak bisa diubah. Commit: feat(barang): ubah data dan harga
dalam satu transaksi.

Langkah 14: Hapus dengan modal konfirmasi ("Yakin hapus barang ini? Tindakan ini
tidak bisa dibatalkan."). Panggil sp_delete_item. Jika ditolak karena ada transaksi:
"Barang tidak bisa dihapus karena sudah pernah dijual."
Commit: feat(barang): hapus barang dengan konfirmasi.

Langkah 15: Feature test CRUD dan akses role. Commit: test(barang): CRUD dan akses role.

Langkah 16: Tinjauan akhir. Test lulus/gagal, cek tidak ada ikon, cek tidak ada
kredensial di kode. Daftar TODO. Tanpa commit.

Untuk setiap langkah 1 sampai 16: kerjakan HANYA satu langkah, jalankan
php artisan test, tulis ringkasan dan saran commit, lalu BERHENTI dan tunggu saya.