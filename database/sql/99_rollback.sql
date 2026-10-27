-- =============================================================================
-- ROLLBACK - Urutan terbalik dari instalasi
-- PERINGATAN: Ini akan menghapus semua objek baru dan mengembalikan skema!
-- =============================================================================
-- BASIS DATA: Toko_Firo
-- FILE: 99_rollback.sql
-- DESKRIPSI: Skrip pembatalan seluruh migrasi/perubahan arsitektur Toko_Firo
-- =============================================================================

USE `Toko_Firo`;

-- -----------------------------------------------------------------------------
-- 1. DROP TRIGGER PADA log_aktivitas
-- -----------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `trg_log_aktivitas_cegah_update`;
DROP TRIGGER IF EXISTS `trg_log_aktivitas_cegah_delete`;

-- -----------------------------------------------------------------------------
-- 2. DROP STORED PROCEDURE (Seluruh prosedur baru & prosedur yang dimodifikasi)
-- -----------------------------------------------------------------------------
-- Prosedur Autentikasi & Akun
DROP PROCEDURE IF EXISTS `sp_get_pengguna_login`;
DROP PROCEDURE IF EXISTS `sp_buat_otp`;
DROP PROCEDURE IF EXISTS `sp_verifikasi_otp`;
DROP PROCEDURE IF EXISTS `sp_catat_login`;
DROP PROCEDURE IF EXISTS `sp_create_pengguna`;
DROP PROCEDURE IF EXISTS `sp_update_pengguna`;
DROP PROCEDURE IF EXISTS `sp_set_status_pengguna`;
DROP PROCEDURE IF EXISTS `sp_reset_password`;
DROP PROCEDURE IF EXISTS `sp_delete_pengguna`;

-- Prosedur Barang & Stok
DROP PROCEDURE IF EXISTS `sp_cari_item`;
DROP PROCEDURE IF EXISTS `sp_create_item`;
DROP PROCEDURE IF EXISTS `sp_update_item`;
DROP PROCEDURE IF EXISTS `sp_update_harga`;
DROP PROCEDURE IF EXISTS `sp_delete_item`;
DROP PROCEDURE IF EXISTS `sp_set_batas_minimum`;
DROP PROCEDURE IF EXISTS `sp_set_batas_minimum_semua`;
DROP PROCEDURE IF EXISTS `sp_item_kritis_dari_nota`;

-- Prosedur Transaksi & Retur
DROP PROCEDURE IF EXISTS `sp_create_penjualan`;
DROP PROCEDURE IF EXISTS `sp_tambah_item_penjualan`;
DROP PROCEDURE IF EXISTS `sp_retur_penjualan`;
DROP PROCEDURE IF EXISTS `sp_create_pembelian`;
DROP PROCEDURE IF EXISTS `sp_tambah_item_pembelian`;
DROP PROCEDURE IF EXISTS `sp_retur_pembelian`;

-- Prosedur Saldo & Keuangan
DROP PROCEDURE IF EXISTS `sp_set_saldo_awal`;
DROP PROCEDURE IF EXISTS `sp_get_saldo_harian`;

-- Prosedur Laporan & Dashboard
DROP PROCEDURE IF EXISTS `sp_get_laporan_penjualan`;
DROP PROCEDURE IF EXISTS `sp_ringkasan_dashboard`;
DROP PROCEDURE IF EXISTS `sp_barang_terlaris`;

-- Prosedur Notifikasi, Info Barang, Log & Pengaturan
DROP PROCEDURE IF EXISTS `sp_get_info_barang`;
DROP PROCEDURE IF EXISTS `sp_tandai_dibaca`;
DROP PROCEDURE IF EXISTS `sp_get_log`;
DROP PROCEDURE IF EXISTS `sp_catat_backup`;
DROP PROCEDURE IF EXISTS `sp_catat_notifikasi`;
DROP PROCEDURE IF EXISTS `sp_update_status_notifikasi`;
DROP PROCEDURE IF EXISTS `sp_get_log_notifikasi`;
DROP PROCEDURE IF EXISTS `sp_get_setting`;
DROP PROCEDURE IF EXISTS `sp_set_setting`;

-- -----------------------------------------------------------------------------
-- 3. DROP VIEW (View baru dan view yang dimodifikasi)
-- -----------------------------------------------------------------------------
DROP VIEW IF EXISTS `v_katalog_publik`;
DROP VIEW IF EXISTS `v_info_barang`;
DROP VIEW IF EXISTS `v_barang_terlaris`;
DROP VIEW IF EXISTS `v_stok_menipis`;
DROP VIEW IF EXISTS `v_stok_item`;

-- -----------------------------------------------------------------------------
-- 4. DROP TABEL BARU (Urutan terbalik berdasarkan ketergantungan relasi FK)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `log_notifikasi`;
DROP TABLE IF EXISTS `pengaturan_notifikasi`;
DROP TABLE IF EXISTS `riwayat_backup`;
DROP TABLE IF EXISTS `notifikasi_dibaca`;
DROP TABLE IF EXISTS `saldo_harian`;
DROP TABLE IF EXISTS `otp_2fa`;

-- -----------------------------------------------------------------------------
-- 5. HAPUS KENDALA FOREIGN KEY KE TABEL pengguna
-- -----------------------------------------------------------------------------
ALTER TABLE `daftar_penjualan` DROP FOREIGN KEY IF EXISTS `fk_penjualan_kasir`;
ALTER TABLE `daftar_penjualan` DROP FOREIGN KEY IF EXISTS `fk_daftar_penjualan_id_kasir`;
ALTER TABLE `daftar_pembelian` DROP FOREIGN KEY IF EXISTS `fk_pembelian_pengguna`;
ALTER TABLE `daftar_pembelian` DROP FOREIGN KEY IF EXISTS `fk_daftar_pembelian_id_pengguna`;
ALTER TABLE `log_aktivitas` DROP FOREIGN KEY IF EXISTS `fk_log_pengguna`;
ALTER TABLE `log_aktivitas` DROP FOREIGN KEY IF EXISTS `fk_log_aktivitas_id_pengguna`;

-- -----------------------------------------------------------------------------
-- 6. DROP TABEL pengguna
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `pengguna`;

-- -----------------------------------------------------------------------------
-- 7. HAPUS KOLOM DAN INDEKS TAMBAHAN PADA TABEL INTI
-- -----------------------------------------------------------------------------
-- Kolom tambahan pada daftar_item
ALTER TABLE `daftar_item` DROP COLUMN IF EXISTS `batas_minimum`;
ALTER TABLE `daftar_item` DROP COLUMN IF EXISTS `dibuat_pada`;
ALTER TABLE `daftar_item` DROP COLUMN IF EXISTS `diubah_pada`;

-- Kolom tambahan pada daftar_penjualan
ALTER TABLE `daftar_penjualan` DROP COLUMN IF EXISTS `id_kasir`;
ALTER TABLE `daftar_penjualan` DROP COLUMN IF EXISTS `metode_bayar`;

-- Kolom tambahan pada daftar_pembelian
ALTER TABLE `daftar_pembelian` DROP COLUMN IF EXISTS `id_pengguna`;

-- Kolom & indeks tambahan pada log_aktivitas
ALTER TABLE `log_aktivitas` DROP COLUMN IF EXISTS `id_pengguna`;
ALTER TABLE `log_aktivitas` DROP COLUMN IF EXISTS `data_sebelum`;
ALTER TABLE `log_aktivitas` DROP COLUMN IF EXISTS `data_sesudah`;
ALTER TABLE `log_aktivitas` DROP INDEX IF EXISTS `idx_log_aktivitas_waktu`;
ALTER TABLE `log_aktivitas` DROP INDEX IF EXISTS `idx_log_aktivitas_tipe_aksi`;

-- -----------------------------------------------------------------------------
-- 8. DROP PENGGUNA DATABASE
-- -----------------------------------------------------------------------------
DROP USER IF EXISTS 'firo_app'@'localhost';
DROP USER IF EXISTS 'firo_katalog'@'localhost';
DROP USER IF EXISTS 'firo_backup'@'localhost';

-- =============================================================================
-- CATATAN PEMULIHAN:
-- Untuk memulihkan view asli (v_stok_item, v_stok_menipis) dan 12 stored procedure
-- awal ke kondisi awal proyek, disarankan menjalankan dump database awal: Toko_Firo.sql
-- =============================================================================
