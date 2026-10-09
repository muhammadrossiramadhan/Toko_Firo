-- =============================================================================
-- BASIS DATA: Toko_Firo
-- FILE: 06_hak_akses.sql
-- DESKRIPSI: Konfigurasi pengguna database dan pemberian hak akses (GRANT)
-- =============================================================================

USE `Toko_Firo`;

-- =============================================================================
-- 1. PENGGUNA: firo_app
-- Digunakan oleh backend aplikasi (Kasir & Admin)
-- =============================================================================
CREATE USER IF NOT EXISTS 'firo_app'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD';

-- Hak eksekusi stored procedure di Toko_Firo
GRANT EXECUTE ON `Toko_Firo`.* TO 'firo_app'@'localhost';

-- Hak SELECT pada seluruh tabel dan view di Toko_Firo
GRANT SELECT ON `Toko_Firo`.* TO 'firo_app'@'localhost';

-- Hak INSERT dan UPDATE pada tabel operasional dan pendukung
GRANT INSERT, UPDATE ON `Toko_Firo`.`daftar_item` TO 'firo_app'@'localhost';
GRANT INSERT, UPDATE ON `Toko_Firo`.`daftar_pembelian` TO 'firo_app'@'localhost';
GRANT INSERT, UPDATE ON `Toko_Firo`.`daftar_penjualan` TO 'firo_app'@'localhost';
GRANT INSERT, UPDATE ON `Toko_Firo`.`daftar_transaksi` TO 'firo_app'@'localhost';
GRANT INSERT, UPDATE ON `Toko_Firo`.`log_aktivitas` TO 'firo_app'@'localhost';
GRANT INSERT, UPDATE ON `Toko_Firo`.`pengguna` TO 'firo_app'@'localhost';
GRANT INSERT, UPDATE ON `Toko_Firo`.`otp_2fa` TO 'firo_app'@'localhost';
GRANT INSERT, UPDATE ON `Toko_Firo`.`notifikasi_dibaca` TO 'firo_app'@'localhost';
GRANT INSERT, UPDATE ON `Toko_Firo`.`saldo_harian` TO 'firo_app'@'localhost';
GRANT INSERT, UPDATE ON `Toko_Firo`.`riwayat_backup` TO 'firo_app'@'localhost';
GRANT INSERT, UPDATE ON `Toko_Firo`.`pengaturan_notifikasi` TO 'firo_app'@'localhost';
GRANT INSERT, UPDATE ON `Toko_Firo`.`log_notifikasi` TO 'firo_app'@'localhost';

-- Hak DELETE HANYA pada tabel yang diizinkan (dibutuhkan prosedur hapus)
-- DILARANG DELETE pada: daftar_transaksi, daftar_penjualan, daftar_pembelian, log_aktivitas
GRANT DELETE ON `Toko_Firo`.`daftar_item` TO 'firo_app'@'localhost';
GRANT DELETE ON `Toko_Firo`.`pengguna` TO 'firo_app'@'localhost';
GRANT DELETE ON `Toko_Firo`.`otp_2fa` TO 'firo_app'@'localhost';
GRANT DELETE ON `Toko_Firo`.`notifikasi_dibaca` TO 'firo_app'@'localhost';


-- =============================================================================
-- 2. PENGGUNA: firo_katalog
-- Digunakan oleh halaman landing / katalog publik (read-only terbatas)
-- =============================================================================
CREATE USER IF NOT EXISTS 'firo_katalog'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD';

-- HANYA memiliki hak SELECT pada view v_katalog_publik
GRANT SELECT ON `Toko_Firo`.`v_katalog_publik` TO 'firo_katalog'@'localhost';


-- =============================================================================
-- 3. PENGGUNA: firo_backup
-- Digunakan untuk otomasi pencadangan database (mysqldump)
-- =============================================================================
CREATE USER IF NOT EXISTS 'firo_backup'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD';

-- Hak minimum untuk backup tabel, view, trigger, dan lock tables
GRANT SELECT, SHOW VIEW, TRIGGER, LOCK TABLES ON `Toko_Firo`.* TO 'firo_backup'@'localhost';

-- Hak membaca definisi stored procedure/function di information_schema (MySQL 8.0+/9.x)
GRANT SHOW_ROUTINE ON *.* TO 'firo_backup'@'localhost';

-- Terapkan perubahan hak akses
FLUSH PRIVILEGES;
