-- =============================================================================
-- PERINGATAN: Data ini hanya untuk pengembangan! Jangan dijalankan di produksi!
-- =============================================================================
-- BASIS DATA: Toko_Firo
-- FILE: 07_seed_dev.sql
-- DESKRIPSI: Data awal (seeding) untuk lingkungan pengembangan dan pengujian
-- =============================================================================

USE `Toko_Firo`;

-- -----------------------------------------------------------------------------
-- 1. SEED PENGGUNA (Akun Awal)
-- Password hash menggunakan placeholder bcrypt
-- -----------------------------------------------------------------------------
INSERT INTO `pengguna` (`nama`, `username`, `password_hash`, `role`, `status`) VALUES
('Junaidi', 'junaidi', 'GANTI_DENGAN_HASH_BCRYPT', 'ADMIN', 'AKTIF'),
('Rina', 'rina', 'GANTI_DENGAN_HASH_BCRYPT', 'KASIR', 'AKTIF'),
('Andi', 'andi', 'GANTI_DENGAN_HASH_BCRYPT', 'KASIR', 'NONAKTIF');

-- -----------------------------------------------------------------------------
-- 2. SEED MASTER ITEM VIA sp_create_item
-- Parameter: (p_kode, p_nama, p_jenis, p_merek, p_rak, p_satuan, p_h_pokok,
--             p_h_jual, p_stok, p_tipe, p_ket, p_batas, p_id_pengguna)
-- -----------------------------------------------------------------------------
CALL sp_create_item('BRG-001', 'Pulpen Standard AE7 Hitam', 'Alat Tulis', 'Standard', 'A1', 'pcs', 1800.00, 2500.00, 120, NULL, NULL, 5, 1);
CALL sp_create_item('BRG-002', 'Buku Tulis Sidu 38 Lembar', 'Buku', 'Sidu', 'A2', 'pcs', 3000.00, 4000.00, 64, NULL, NULL, 5, 1);
CALL sp_create_item('BRG-003', 'Kertas HVS A4 70gr', 'Kertas', 'PaperOne', 'B1', 'rim', 45000.00, 52000.00, 4, NULL, NULL, 5, 1);
CALL sp_create_item('BRG-004', 'Map Plastik Folio', 'Perlengkapan Kantor', '-', 'B2', 'pcs', 1200.00, 2000.00, 40, NULL, NULL, 5, 1);
CALL sp_create_item('BRG-005', 'Stapler HD-10', 'Perlengkapan Kantor', 'Max', 'B3', 'pcs', 12000.00, 15000.00, 8, NULL, NULL, 5, 1);
CALL sp_create_item('BRG-006', 'Isi Staples No.10', 'Perlengkapan Kantor', 'Max', 'B3', 'box', 2200.00, 3000.00, 2, NULL, NULL, 5, 1);
CALL sp_create_item('BRG-007', 'Penghapus Faber-Castell', 'Alat Tulis', 'Faber-Castell', 'A1', 'pcs', 2500.00, 3500.00, 25, NULL, NULL, 5, 1);
CALL sp_create_item('BRG-008', 'Pensil 2B', 'Alat Tulis', 'Faber-Castell', 'A1', 'pcs', 2000.00, 3000.00, 0, NULL, NULL, 5, 1);
CALL sp_create_item('BRG-009', 'Lakban Bening', 'Perlengkapan Kantor', 'Daimaru', 'B2', 'pcs', 6000.00, 8000.00, 12, NULL, NULL, 5, 1);
CALL sp_create_item('BRG-010', 'Spidol Snowman Hitam', 'Alat Tulis', 'Snowman', 'A3', 'pcs', 5500.00, 7500.00, 18, NULL, NULL, 5, 1);
CALL sp_create_item('BRG-011', 'Amplop Coklat', 'Kertas', '-', 'B1', 'pcs', 600.00, 1000.00, 200, NULL, NULL, 5, 1);
CALL sp_create_item('BRG-012', 'Jasa Fotocopy A4 (per lembar)', 'Jasa Fotocopy', '-', '-', 'lembar', 150.00, 300.00, 999999, 'JASA', NULL, 5, 1);

-- -----------------------------------------------------------------------------
-- 3. SEED TRANSAKSI PEMBELIAN
-- PB-20261003-001 dari CV Sumber Kertas oleh Junaidi (id_pengguna = 1)
-- -----------------------------------------------------------------------------
CALL sp_create_pembelian('PB-20261003-001', 'CV Sumber Kertas', 1);
CALL sp_tambah_item_pembelian('PB-20261003-001', 'BRG-003', 10);
CALL sp_tambah_item_pembelian('PB-20261003-001', 'BRG-006', 5);

-- -----------------------------------------------------------------------------
-- 4. SEED TRANSAKSI PENJUALAN
-- PJ-20261003-001 kepada Umum (NULL) oleh Rina (id_kasir = 2), metode TUNAI
-- -----------------------------------------------------------------------------
CALL sp_create_penjualan('PJ-20261003-001', NULL, 2, 'TUNAI');
CALL sp_tambah_item_penjualan('PJ-20261003-001', 'BRG-001', 3);
CALL sp_tambah_item_penjualan('PJ-20261003-001', 'BRG-002', 2);
CALL sp_tambah_item_penjualan('PJ-20261003-001', 'BRG-012', 40);

-- -----------------------------------------------------------------------------
-- 5. SEED SALDO AWAL HARIAN
-- Saldo awal jangkar tanggal 2026-10-03 sebesar Rp 500.000 oleh Junaidi
-- -----------------------------------------------------------------------------
CALL sp_set_saldo_awal('2026-10-03', 500000.00, 1);

-- -----------------------------------------------------------------------------
-- 6. SEED PENGATURAN NOTIFIKASI
-- -----------------------------------------------------------------------------
INSERT INTO `pengaturan_notifikasi` (`kunci`, `nilai`) VALUES
('notif_stok_minimum', '1'),
('notif_item_baru', '1'),
('notif_backup', '1'),
('notif_otp', '1'),
('laporan_otomatis', '1'),
('laporan_jam', '21:00'),
('backup_otomatis', '0'),
('backup_frekuensi', 'HARIAN'),
('backup_jam', '22:00'),
('telegram_chat_id_pemilik', '')
ON DUPLICATE KEY UPDATE `nilai` = VALUES(`nilai`);
