# Toko Firo

Sistem informasi berbasis web untuk pengelolaan data barang, stok, transaksi
penjualan, dan laporan pada Toko Firo (usaha ATK dan fotokopi).

Dikerjakan untuk mata kuliah Sistem Informasi / Rekayasa Perangkat Lunak
Universitas Negeri Malang, 2026.

## Anggota Kelompok
- Muhammad Rossi Ramadhan (250535625057), Ketua, Database dan modul data barang
- Muhammad Totti Al Fikri (250535630175), Backend
- Refina Aprilia Dewi (250535624776), UI/UX dan analisis
- Teofilus Aditya Suseno Pandeangan (250535626205), Frontend, backend, dan QA

## Teknologi
- Laravel 12, PHP 8.2 ke atas (dikembangkan dengan PHP 8.4)
- MariaDB, dengan logika bisnis di stored procedure
- Blade dan Tailwind CSS
- Lingkungan pengembangan: Windows dengan FlyEnv

## Struktur Dokumentasi
- DATABASE.md: ERD, tabel, view, stored procedure, trigger, hak akses
- database/sql/: skrip SQL berurutan 00 sampai 08
- TRACKING.md: log pengerjaan dan status tiap langkah

## Cara Menjalankan (lingkungan lokal)
1. Clone repository dan pindah ke branch kerja.
2. composer install
3. Salin .env.example menjadi .env, lalu isi kredensial database lokal di .env.
   File .env tidak boleh di-commit.
4. php artisan key:generate
5. Buat database toko_firo dan toko_firo_test di MariaDB.
6. php artisan migrate
7. php artisan serve, lalu buka http://127.0.0.1:8000

## Menjalankan Test
php artisan test
Test memakai database toko_firo_test. Jangan jalankan test di database dev.

## Keamanan
- Kredensial tidak boleh masuk ke repository. Gunakan .env.
- Halaman demo (/demo/barang) belum memakai middleware auth. Wajib diberi
  auth dan role:ADMIN sebelum merge ke branch utama.

## Branch
- main: versi stabil
- Rossi-Kelola-Data-Barang-Item: pengerjaan modul Kelola Data Barang
