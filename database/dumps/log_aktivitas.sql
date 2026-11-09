-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 08, 2026 at 11:20 PM
-- Server version: 13.1.1-MariaDB-log
-- PHP Version: 8.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko_firo`
--

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id_log` int(10) UNSIGNED NOT NULL,
  `waktu` datetime NOT NULL DEFAULT current_timestamp(),
  `keterangan` text DEFAULT NULL,
  `tipe_aksi` varchar(50) DEFAULT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `data_sebelum` text DEFAULT NULL,
  `data_sesudah` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id_log`, `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`) VALUES
(1, '2026-10-17 15:14:15', 'Item baru: TST_TINKER_01 - Nama Tinker', 'ITEM_BARU', 1, NULL, NULL),
(2, '2026-10-17 15:24:18', 'Item baru: TST_DEV_01 - Test Dev', 'ITEM_BARU', 1, NULL, NULL),
(3, '2026-10-18 14:58:46', 'Item baru: dsfsa - tip-x', 'ITEM_BARU', 1, NULL, NULL),
(4, '2026-10-18 14:59:13', 'Ubah harga: dsfsa - tip-x', 'HARGA_DIUBAH', 1, '{\"harga_pokok\": \"5000.00\", \"harga_jual\": \"1998.00\"}', '{\"harga_pokok\": \"5000.00\", \"harga_jual\": \"5400.00\"}'),
(5, '2026-10-18 14:59:42', 'Hapus item: dsfsa - tip-x', 'ITEM_DIHAPUS', 1, '{\"kode_item\": \"dsfsa\", \"nama_item\": \"tip-x\", \"jenis\": \"ATK\", \"merek\": \"SIDU\", \"stok\": \"6\", \"harga_pokok\": \"5000.00\", \"harga_jual\": \"5400.00\"}', NULL),
(6, '2026-10-20 08:40:12', 'Item baru: BRG-001 - Pulpen Standard AE7 Hitam', 'ITEM_BARU', 1, NULL, NULL),
(7, '2026-10-20 08:40:12', 'Item baru: BRG-002 - Buku Tulis Sidu 38 Lembar', 'ITEM_BARU', 1, NULL, NULL),
(8, '2026-10-20 08:40:12', 'Item baru: BRG-003 - Kertas HVS A4 70gr', 'ITEM_BARU', 1, NULL, NULL),
(9, '2026-10-20 08:40:12', 'Item baru: BRG-004 - Map Plastik Folio', 'ITEM_BARU', 1, NULL, NULL),
(10, '2026-10-20 08:40:12', 'Item baru: BRG-005 - Stapler HD-10', 'ITEM_BARU', 1, NULL, NULL),
(11, '2026-10-20 08:40:12', 'Item baru: BRG-006 - Isi Staples No.10', 'ITEM_BARU', 1, NULL, NULL),
(12, '2026-10-20 08:40:12', 'Item baru: BRG-007 - Penghapus Faber-Castell', 'ITEM_BARU', 1, NULL, NULL),
(13, '2026-10-20 08:40:12', 'Item baru: BRG-008 - Pensil 2B', 'ITEM_BARU', 1, NULL, NULL),
(14, '2026-10-20 08:40:12', 'Item baru: BRG-009 - Lakban Bening', 'ITEM_BARU', 1, NULL, NULL),
(15, '2026-10-20 08:40:12', 'Item baru: BRG-010 - Spidol Snowman Hitam', 'ITEM_BARU', 1, NULL, NULL),
(16, '2026-10-20 08:40:12', 'Item baru: BRG-011 - Amplop Coklat', 'ITEM_BARU', 1, NULL, NULL),
(17, '2026-10-20 08:40:12', 'Item baru: BRG-012 - Jasa Fotocopy A4 (per lembar)', 'ITEM_BARU', 1, NULL, NULL),
(18, '2026-10-20 08:40:12', 'Atur saldo awal tanggal 2026-10-03 sebesar 500000.00', 'SALDO_DIATUR', 1, NULL, '{\"tanggal\": \"2026-10-03\", \"saldo_awal\": \"500000.00\"}'),
(21, '2026-10-27 07:39:22', 'Transaksi penjualan kasir PJ-20261027-0001 berhasil diproses (TUNAI). Total: Rp 13.000', 'PENJUALAN', 3, NULL, NULL),
(22, '2026-10-27 07:41:44', 'Transaksi penjualan kasir PJ-20261027-0002 berhasil diproses (TUNAI). Total: Rp 16.000', 'PENJUALAN', 3, NULL, NULL),
(23, '2026-10-27 16:06:19', 'Backup database: backup_toko_firo_2026-10-27_160618.sql (BERHASIL)', 'BACKUP', 1, NULL, '{\"jenis\": \"MANUAL\", \"ukuran_kb\": \"108\", \"status\": \"BERHASIL\", \"nama_file\": \"backup_toko_firo_2026-10-27_160618.sql\"}'),
(24, '2026-10-28 03:47:24', 'Pembaruan profil pengguna: admin', 'UBAH_PROFIL', 1, NULL, NULL),
(25, '2026-10-28 04:09:26', 'Update pengguna: admin', 'PENGGUNA_DIUBAH', 1, '{\"nama\": \"Rossi\", \"username\": \"admin\", \"role\": \"ADMIN\"}', '{\"nama\": \"Rossi\", \"username\": \"admin\", \"role\": \"ADMIN\"}'),
(26, '2026-11-03 18:11:55', 'Mengubah batas minimum item ACT-BRG-BATAS menjadi 18', 'STOK', NULL, NULL, NULL),
(27, '2026-11-03 18:15:32', 'Mengubah batas minimum item TEST-SP menjadi 18', 'STOK', NULL, NULL, NULL),
(28, '2026-11-05 16:19:56', 'Backup database: backup_toko_firo_2026-11-05_161956.sql (BERHASIL)', 'BACKUP', 1, NULL, '{\"jenis\": \"MANUAL\", \"ukuran_kb\": \"13\", \"status\": \"BERHASIL\", \"nama_file\": \"backup_toko_firo_2026-11-05_161956.sql\"}'),
(29, '2026-11-05 22:35:11', 'Item diubah: BRG-001 - Buku Tulis Okey 32 Halaman Sampul Coklat', 'ITEM_DIUBAH', 1, '{\"nama_item\": \"Pulpen Standard AE7 Hitam\", \"jenis\": \"Alat Tulis\", \"merek\": \"Standard\", \"rak\": \"A1\", \"satuan\": \"pcs\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}', '{\"nama_item\": \"Buku Tulis Okey 32 Halaman Sampul Coklat\", \"jenis\": \"Alat Tulis\", \"merek\": \"Okey\", \"rak\": \"A1\", \"satuan\": \"pcs\", \"tipe_item\": \"BARANG\", \"keterangan\": \"Buku Tulis Okey 32 Halaman hadir dengan sampul cokelat polos dan tampilan minimalis yang mendukung suasana belajar tenang.\", \"batas_minimum\": \"5\"}'),
(30, '2026-11-05 22:38:06', 'Item diubah: BRG-002 - Map Plastik Bussines Snelhecter', 'ITEM_DIUBAH', 1, '{\"nama_item\": \"Buku Tulis Sidu 38 Lembar\", \"jenis\": \"Buku\", \"merek\": \"Sidu\", \"rak\": \"A2\", \"satuan\": \"pcs\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}', '{\"nama_item\": \"Map Plastik Bussines Snelhecter\", \"jenis\": \"Map\", \"merek\": \"Kiky\", \"rak\": \"A2\", \"satuan\": \"PAK\", \"tipe_item\": \"BARANG\", \"keterangan\": \"harga diatas merupakan harga PACK isi 12 pcs map\", \"batas_minimum\": \"5\"}'),
(31, '2026-11-05 22:39:27', 'Item diubah: BRG-003 - Buku Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko', 'ITEM_DIUBAH', 1, '{\"nama_item\": \"Kertas HVS A4 70gr\", \"jenis\": \"Kertas\", \"merek\": \"PaperOne\", \"rak\": \"B1\", \"satuan\": \"rim\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}', '{\"nama_item\": \"Buku Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko\", \"jenis\": \"Buku\", \"merek\": \"Ideals Children\'s Book\", \"rak\": \"B1\", \"satuan\": \"pcs\", \"tipe_item\": \"BARANG\", \"keterangan\": \"Ringkas, rapi, dan mudah dibawa—Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko siap menemani belajar kapan saja. Tampilan bergambar membantu memperjelas arti, sementara layout yang jelas membuat pencarian kata terasa lebih nyaman.\\r\\n\\r\\n\\r\\n\\r\\nKeunggulan Utama\\r\\n\\r\\n- Bergambar: ilustrasi membantu pemahaman arti kata.\\r\\n\\r\\n- Grammar & Conversation: dilengkapi materi grammar dan percakapan.\\r\\n\\r\\n- Cara membaca: dilengkapi cara membaca Inggris-Indonesia original.\\r\\n\\r\\n\\r\\n\\r\\nInformasi Produk\\r\\n\\r\\n- Penulis: Drs. P. Handoko\\r\\n\\r\\n- Penerbit: Lingkar Media\\r\\n\\r\\n- Jumlah Halaman: 128 hlm\\r\\n\\r\\n- Ukuran: 15 x 24 cm\\r\\n\\r\\n- Kertas: berwarna\\r\\n\\r\\n- Cover: Soft Cover\\r\\n\\r\\n- Berat: 200 gr\\r\\n\\r\\n- ISBN: 979-9994-339-6\\r\\n\\r\\n- Asal Produk: Indonesia\\r\\n\\r\\n- Impor/Lokal: Lokal\", \"batas_minimum\": \"5\"}'),
(32, '2026-11-05 22:40:40', 'Item diubah: BRG-004 - Spidol Snowman 12 Warna PW-12A', 'ITEM_DIUBAH', 1, '{\"nama_item\": \"Map Plastik Folio\", \"jenis\": \"Perlengkapan Kantor\", \"merek\": \"-\", \"rak\": \"B2\", \"satuan\": \"pcs\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}', '{\"nama_item\": \"Spidol Snowman 12 Warna PW-12A\", \"jenis\": \"Alat Tulis\", \"merek\": \"SNOWMAN\", \"rak\": \"B2\", \"satuan\": \"pcs\", \"tipe_item\": \"BARANG\", \"keterangan\": \"Buat halaman kosong jadi penuh warna dengan Snowman Spidol 12 Warna PW-12A. Tinta cepat kering, tidak tembus kertas, dan ujung presisi—teman setia untuk mewarnai, menggambar, hingga catatan harian anak dan dewasa.\\r\\n\\r\\n\\r\\n\\r\\nKeunggulan Utama\\r\\n\\r\\n- 12 warna cerah yang merata, pas untuk kreativitas tanpa batas.\\r\\n\\r\\n- Tinta berbasis air: aman dan tidak berbau menyengat.\\r\\n\\r\\n- Ujung runcing presisi (fine tip) untuk garis rapi dan detail halus.\\r\\n\\r\\n- Nyaman digenggam, tutup rapat agar tinta tidak cepat kering.\\r\\n\\r\\n\\r\\n\\r\\nCocok Untuk\\r\\n\\r\\n- Mewarnai, menggambar, dan membuat catatan.\\r\\n\\r\\n- Tugas sekolah, hiasan tulisan, hingga kebutuhan kreatif sehari-hari.\\r\\n\\r\\n\\r\\n\\r\\nIsi & Ukuran\\r\\n\\r\\n- Jumlah isi: 1 set = 12 batang (12 warna lengkap).\\r\\n\\r\\n- Ukuran: Panjang ±14,5 cm, diameter ±1 cm.\", \"batas_minimum\": \"5\"}'),
(33, '2026-11-05 22:41:03', 'Ubah harga: BRG-004 - Spidol Snowman 12 Warna PW-12A', 'HARGA_DIUBAH', 1, '{\"harga_pokok\": \"1200.00\", \"harga_jual\": \"2000.00\"}', '{\"harga_pokok\": \"16000.00\", \"harga_jual\": \"18000.00\"}'),
(34, '2026-11-05 22:41:29', 'Ubah harga: BRG-001 - Buku Tulis Okey 32 Halaman Sampul Coklat', 'HARGA_DIUBAH', 1, '{\"harga_pokok\": \"1800.00\", \"harga_jual\": \"2500.00\"}', '{\"harga_pokok\": \"2000.00\", \"harga_jual\": \"4000.00\"}'),
(35, '2026-11-05 22:42:17', 'Ubah harga: BRG-002 - Map Plastik Bussines Snelhecter', 'HARGA_DIUBAH', 1, '{\"harga_pokok\": \"3000.00\", \"harga_jual\": \"4000.00\"}', '{\"harga_pokok\": \"35000.00\", \"harga_jual\": \"37000.00\"}'),
(36, '2026-11-05 22:43:31', 'Ubah harga: BRG-003 - Buku Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko', 'HARGA_DIUBAH', 1, '{\"harga_pokok\": \"45000.00\", \"harga_jual\": \"52000.00\"}', '{\"harga_pokok\": \"13000.00\", \"harga_jual\": \"15000.00\"}'),
(37, '2026-11-05 22:44:18', 'Item diubah: BRG-005 - Lem Tetes Korea G Lem Besi C Cair - Rekat Kuat, Serbaguna, Mudah Digunakan', 'ITEM_DIUBAH', 1, '{\"nama_item\": \"Stapler HD-10\", \"jenis\": \"Perlengkapan Kantor\", \"merek\": \"Max\", \"rak\": \"B3\", \"satuan\": \"pcs\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}', '{\"nama_item\": \"Lem Tetes Korea G Lem Besi C Cair - Rekat Kuat, Serbaguna, Mudah Digunakan\", \"jenis\": \"Alat Tulis\", \"merek\": \"-\", \"rak\": \"B3\", \"satuan\": \"pcs\", \"tipe_item\": \"BARANG\", \"keterangan\": \"Lem Tetes Korea G Lem Besi C Cair adalah solusi ideal untuk berbagai kebutuhan perekat. Dengan daya rekat yang kuat, lem ini cocok untuk merekatkan berbagai jenis barang. Mudah digunakan, lem ini hadir dalam bentuk tetes yang praktis dan dapat digunakan untuk berbagai aplikasi.\\r\\n\\r\\n\\r\\n\\r\\nKeunggulan Produk:\\r\\n\\r\\n- Daya rekat tinggi\\r\\n\\r\\n- Mudah digunakan\\r\\n\\r\\n- Serbaguna untuk berbagai keperluan\\r\\n\\r\\n\\r\\n\\r\\nInformasi Tambahan:\\r\\n\\r\\nLem ini tersedia dalam kemasan yang praktis dan siap digunakan kapan saja Anda membutuhkannya. Cocok untuk berbagai kebutuhan rumah tangga dan industri. Dengan Lem Tetes Korea G Lem Besi C Cair, pekerjaan perekat Anda menjadi lebih mudah dan efisien.\", \"batas_minimum\": \"5\"}'),
(38, '2026-11-05 22:45:13', 'Item diubah: BRG-006 - Buku Kas Kwarto Soft Cover Kiky 4 Warna, 78 Lembar', 'ITEM_DIUBAH', 1, '{\"nama_item\": \"Isi Staples No.10\", \"jenis\": \"Perlengkapan Kantor\", \"merek\": \"Max\", \"rak\": \"B3\", \"satuan\": \"box\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}', '{\"nama_item\": \"Buku Kas Kwarto Soft Cover Kiky 4 Warna, 78 Lembar\", \"jenis\": \"Buku\", \"merek\": \"Kiky\", \"rak\": \"B3\", \"satuan\": \"pcs\", \"tipe_item\": \"BARANG\", \"keterangan\": \"Buku kas kwarto Kiky dengan soft cover untuk kebutuhan pencatatan yang rapi.\\r\\n\\r\\n\\r\\n\\r\\nDetail Produk\\r\\n\\r\\n- Varian warna: biru, merah, cokelat, dan oranye\\r\\n\\r\\n- Isi: 78 lembar\", \"batas_minimum\": \"5\"}'),
(39, '2026-11-05 22:45:34', 'Ubah harga: BRG-006 - Buku Kas Kwarto Soft Cover Kiky 4 Warna, 78 Lembar', 'HARGA_DIUBAH', 1, '{\"harga_pokok\": \"2200.00\", \"harga_jual\": \"3000.00\"}', '{\"harga_pokok\": \"10000.00\", \"harga_jual\": \"12000.00\"}'),
(40, '2026-11-05 22:46:17', 'Item diubah: BRG-007 - Buku Tulis Polos Tanpa Garis 38 lembar', 'ITEM_DIUBAH', 1, '{\"nama_item\": \"Penghapus Faber-Castell\", \"jenis\": \"Alat Tulis\", \"merek\": \"Faber-Castell\", \"rak\": \"A1\", \"satuan\": \"pcs\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}', '{\"nama_item\": \"Buku Tulis Polos Tanpa Garis 38 lembar\", \"jenis\": \"Buku\", \"merek\": \"Best One\", \"rak\": \"A1\", \"satuan\": \"pcs\", \"tipe_item\": \"BARANG\", \"keterangan\": \"Ringkas, fungsional, dan rapi untuk catatan harian. Buku tulis polos tanpa garis ini siap menemani rutinitas belajar dan kerja—membuat setiap ide mengalir lancar tanpa ribet.\\r\\n\\r\\n\\r\\n\\r\\nKeunggulan Utama:\\r\\n\\r\\n- Kertas 75 gsm yang berkualitas, tidak tembus tinta saat ditulis.\\r\\n\\r\\n- Desain polos tanpa garis, nyaman untuk berbagai jenis catatan.\\r\\n\\r\\n- Isi 38 lembar per buku.\\r\\n\\r\\n\\r\\n\\r\\nCatatan Pembelian:\\r\\n\\r\\n- Sampul random sesuai stok yang tersedia.\\r\\n\\r\\n- Jika ingin motif tertentu, silakan chat terlebih dahulu.\\r\\n\\r\\n\\r\\n\\r\\nDetail Produk:\\r\\n\\r\\n- Merek: Best One\\r\\n\\r\\n- Bahan kertas: 75 gsm\", \"batas_minimum\": \"5\"}'),
(41, '2026-11-07 14:51:36', 'Item baru: 12341234123 - Tip-x joyko', 'ITEM_BARU', 1, NULL, NULL),
(42, '2026-11-07 14:51:44', 'Hapus item: 12341234123 - Tip-x joyko', 'ITEM_DIHAPUS', 1, '{\"kode_item\": \"12341234123\", \"nama_item\": \"Tip-x joyko\", \"jenis\": \"Alat Tulis\", \"merek\": \"Kiky\", \"stok\": \"1\", \"harga_pokok\": \"0.00\", \"harga_jual\": \"7500.00\"}', NULL),
(43, '2026-11-08 21:50:33', 'Hapus item: BRG-008 - Pensil 2B', 'ITEM_DIHAPUS', 1, '{\"kode_item\": \"BRG-008\", \"nama_item\": \"Pensil 2B\", \"jenis\": \"Alat Tulis\", \"merek\": \"Faber-Castell\", \"stok\": \"0\", \"harga_pokok\": \"2000.00\", \"harga_jual\": \"3000.00\"}', NULL),
(44, '2026-11-08 21:52:49', 'Hapus pengguna: andi', 'PENGGUNA_DIHAPUS', 1, '{\"id_pengguna\": \"4\", \"username\": \"andi\", \"nama\": \"Andi\"}', NULL),
(45, '2026-11-08 21:53:07', 'Ubah status pengguna: junaidi menjadi NONAKTIF', 'STATUS_PENGGUNA', 1, '{\"status\": \"AKTIF\"}', '{\"status\": \"NONAKTIF\"}'),
(46, '2026-11-08 21:58:05', 'Item diubah: BRG-012 - Jasa Fotocopy A4 (per lembar)', 'ITEM_DIUBAH', 1, '{\"nama_item\": \"Jasa Fotocopy A4 (per lembar)\", \"jenis\": \"Jasa Fotocopy\", \"merek\": \"-\", \"rak\": \"-\", \"satuan\": \"lembar\", \"tipe_item\": \"JASA\", \"keterangan\": null, \"batas_minimum\": \"5\"}', '{\"nama_item\": \"Jasa Fotocopy A4 (per lembar)\", \"jenis\": \"Jasa Fotocopy\", \"merek\": \"-\", \"rak\": \"-\", \"satuan\": \"lembar\", \"tipe_item\": \"JASA\", \"keterangan\": null, \"batas_minimum\": \"5\"}'),
(47, '2026-11-08 23:31:59', 'Hapus item: BRG-009 - Lakban Bening', 'ITEM_DIHAPUS', 1, NULL, NULL),
(48, '2026-11-08 23:32:05', 'Hapus item: BRG-011 - Amplop Coklat', 'ITEM_DIHAPUS', 1, NULL, NULL),
(49, '2026-11-08 23:32:15', 'Hapus item: BRG-010 - Spidol Snowman Hitam', 'ITEM_DIHAPUS', 1, NULL, NULL);

--
-- Triggers `log_aktivitas`
--
DELIMITER $$
CREATE TRIGGER `trg_log_aktivitas_cegah_delete` BEFORE DELETE ON `log_aktivitas` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Log aktivitas tidak boleh diubah atau dihapus!';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_log_aktivitas_cegah_update` BEFORE UPDATE ON `log_aktivitas` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Log aktivitas tidak boleh diubah atau dihapus!';
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_log_aktivitas_waktu` (`waktu`),
  ADD KEY `idx_log_aktivitas_tipe_aksi` (`tipe_aksi`),
  ADD KEY `fk_log_aktivitas_id_pengguna` (`id_pengguna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id_log` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `fk_log_aktivitas_id_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
