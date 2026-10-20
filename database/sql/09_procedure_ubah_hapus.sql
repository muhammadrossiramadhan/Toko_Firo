-- =============================================================================
-- Database: Toko_Firo
-- Berkas: 09_procedure_ubah_hapus.sql
-- Deskripsi: Prosedur sp_update_item_v2 dan sp_delete_item_v2 dengan jejak audit.
-- Dijalankan manual oleh pemilik, SETELAH 01 sampai 05,
-- dan hanya di database yang sudah diuji.
-- =============================================================================

USE `Toko_Firo`;

-- =============================================================================
-- Prosedur: sp_update_item_v2
-- Deskripsi: Memperbarui semua kolom daftar_item yang dapat diedit secara
--            langsung: nama_item, jenis, merek, rak, satuan, tipe_item,
--            keterangan, batas_minimum. Mencatat jejak audit lengkap
--            ke log_aktivitas.
-- Kolom yang TIDAK diubah prosedur ini: kode_item, harga_pokok, harga_jual,
--            stok, dibuat_pada, diubah_pada.
-- Format log_aktivitas:
--   tipe_aksi   : 'ITEM_DIUBAH'   (sama dengan sp_update_item di 04_procedure.sql)
--   keterangan  : 'Item diubah: <kode> - <nama_baru>'
--   data_sebelum: JSON 8 kolom yang berubah sebelum UPDATE
--   data_sesudah: JSON 8 kolom yang berubah sesudah UPDATE
-- =============================================================================
DROP PROCEDURE IF EXISTS `sp_update_item_v2`;
DELIMITER $$
CREATE PROCEDURE `sp_update_item_v2`(
    IN `p_kode`         VARCHAR(20),
    IN `p_nama`         VARCHAR(100),
    IN `p_jenis`        VARCHAR(50),
    IN `p_merek`        VARCHAR(50),
    IN `p_rak`          VARCHAR(20),
    IN `p_satuan`       VARCHAR(20),
    IN `p_tipe`         VARCHAR(20),
    IN `p_ket`          TEXT,
    IN `p_batas`        INT,
    IN `p_id_pengguna`  INT
)
BEGIN
    DECLARE v_old_nama      VARCHAR(100);
    DECLARE v_old_jenis     VARCHAR(50);
    DECLARE v_old_merek     VARCHAR(50);
    DECLARE v_old_rak       VARCHAR(20);
    DECLARE v_old_satuan    VARCHAR(20);
    DECLARE v_old_tipe      VARCHAR(20);
    DECLARE v_old_ket       TEXT;
    DECLARE v_old_batas     INT;

    -- Ambil semua kolom yang bisa diubah sebelum pembaruan
    SELECT
        `nama_item`, `jenis`, `merek`, `rak`,
        `satuan`, `tipe_item`, `keterangan`, `batas_minimum`
    INTO
        v_old_nama, v_old_jenis, v_old_merek, v_old_rak,
        v_old_satuan, v_old_tipe, v_old_ket, v_old_batas
    FROM `daftar_item`
    WHERE `kode_item` = p_kode;

    IF v_old_nama IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Item tidak ditemukan!';
    END IF;

    UPDATE `daftar_item`
    SET
        `nama_item`     = p_nama,
        `jenis`         = p_jenis,
        `merek`         = p_merek,
        `rak`           = p_rak,
        `satuan`        = p_satuan,
        `tipe_item`     = p_tipe,
        `keterangan`    = p_ket,
        `batas_minimum` = p_batas,
        `diubah_pada`   = NOW()
    WHERE `kode_item` = p_kode;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`,
        `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Item diubah: ', p_kode, ' - ', p_nama),
        'ITEM_DIUBAH',
        p_id_pengguna,
        JSON_OBJECT(
            'nama_item',     v_old_nama,
            'jenis',         v_old_jenis,
            'merek',         v_old_merek,
            'rak',           v_old_rak,
            'satuan',        v_old_satuan,
            'tipe_item',     v_old_tipe,
            'keterangan',    v_old_ket,
            'batas_minimum', v_old_batas
        ),
        JSON_OBJECT(
            'nama_item',     p_nama,
            'jenis',         p_jenis,
            'merek',         p_merek,
            'rak',           p_rak,
            'satuan',        p_satuan,
            'tipe_item',     p_tipe,
            'keterangan',    p_ket,
            'batas_minimum', p_batas
        )
    );
END$$
DELIMITER ;


-- =============================================================================
-- Prosedur: sp_delete_item_v2
-- Deskripsi: Menghapus item dari daftar_item jika belum memiliki riwayat
--            transaksi. Sama seperti sp_delete_item di 04_procedure.sql.
-- Pesan penolakan (persis sama dengan sp_delete_item, baris 190 di 04):
--   'Item tidak bisa dihapus karena sudah memiliki riwayat transaksi!'
-- Catatan K13: Laravel menerjemahkan pesan database ini ke kalimat yang
--   ditampilkan ke pengguna: "Barang tidak bisa dihapus karena sudah pernah
--   dijual." — penerjemahan dilakukan di controller, bukan di sini.
-- Format log_aktivitas:
--   tipe_aksi   : 'ITEM_DIHAPUS'  (sama dengan sp_delete_item di 04_procedure.sql)
--   keterangan  : 'Hapus item: <kode> - <nama>'
--   data_sebelum: JSON 7 kolom snapshot barang sebelum dihapus
--   data_sesudah: NULL (barang sudah tidak ada)
-- =============================================================================
DROP PROCEDURE IF EXISTS `sp_delete_item_v2`;
DELIMITER $$
CREATE PROCEDURE `sp_delete_item_v2`(
    IN `p_kode`         VARCHAR(20),
    IN `p_id_pengguna`  INT
)
BEGIN
    DECLARE v_nama      VARCHAR(100);
    DECLARE v_jenis     VARCHAR(50);
    DECLARE v_merek     VARCHAR(50);
    DECLARE v_stok      INT;
    DECLARE v_h_pokok   DECIMAL(15,2);
    DECLARE v_h_jual    DECIMAL(15,2);

    -- Tolak jika ada riwayat transaksi.
    -- Pesan persis sama dengan sp_delete_item (04_procedure.sql baris 190).
    -- Laravel menerjemahkan ke K13: "Barang tidak bisa dihapus karena sudah pernah dijual."
    IF EXISTS (SELECT 1 FROM `daftar_transaksi` WHERE `kode_item` = p_kode) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Item tidak bisa dihapus karena sudah memiliki riwayat transaksi!';
    END IF;

    -- Ambil data barang sebelum dihapus untuk dicatat di log
    SELECT `nama_item`, `jenis`, `merek`, `stok`, `harga_pokok`, `harga_jual`
    INTO v_nama, v_jenis, v_merek, v_stok, v_h_pokok, v_h_jual
    FROM `daftar_item`
    WHERE `kode_item` = p_kode;

    IF v_nama IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Item tidak ditemukan!';
    END IF;

    DELETE FROM `daftar_item` WHERE `kode_item` = p_kode;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`,
        `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Hapus item: ', p_kode, ' - ', v_nama),
        'ITEM_DIHAPUS',
        p_id_pengguna,
        JSON_OBJECT(
            'kode_item',   p_kode,
            'nama_item',   v_nama,
            'jenis',       v_jenis,
            'merek',       v_merek,
            'stok',        v_stok,
            'harga_pokok', v_h_pokok,
            'harga_jual',  v_h_jual
        ),
        NULL
    );
END$$
DELIMITER ;
