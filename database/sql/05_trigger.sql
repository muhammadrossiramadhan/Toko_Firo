-- =============================================================================
-- BASIS DATA: Toko_Firo
-- FILE: 05_trigger.sql
-- DESKRIPSI: Pembuatan trigger keamanan data pada tabel log_aktivitas
-- CATATAN: Trigger pada tabel daftar_transaksi TIDAK boleh diubah/disentuh
-- =============================================================================

USE `Toko_Firo`;

DELIMITER $$

-- -----------------------------------------------------------------------------
-- Trigger: trg_log_aktivitas_cegah_update
-- Mencegah modifikasi data riwayat log aktivitas (audit trail immutable)
-- -----------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `trg_log_aktivitas_cegah_update`$$
CREATE TRIGGER `trg_log_aktivitas_cegah_update` BEFORE UPDATE ON `log_aktivitas`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Log aktivitas tidak boleh diubah atau dihapus!';
END$$

-- -----------------------------------------------------------------------------
-- Trigger: trg_log_aktivitas_cegah_delete
-- Mencegah penghapusan data riwayat log aktivitas (audit trail immutable)
-- -----------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `trg_log_aktivitas_cegah_delete`$$
CREATE TRIGGER `trg_log_aktivitas_cegah_delete` BEFORE DELETE ON `log_aktivitas`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Log aktivitas tidak boleh diubah atau dihapus!';
END$$

DELIMITER ;
