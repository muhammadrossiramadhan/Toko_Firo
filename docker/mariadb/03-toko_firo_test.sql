USE toko_firo_test;
/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.20-13.1.1-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: toko_firo
-- ------------------------------------------------------
-- Server version	13.1.1-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `daftar_item`
--

DROP TABLE IF EXISTS `daftar_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `daftar_item` (
  `kode_item` varchar(20) NOT NULL,
  `nama_item` varchar(100) NOT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `merek` varchar(50) DEFAULT NULL,
  `rak` varchar(20) DEFAULT NULL,
  `tipe_item` varchar(20) DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `harga_pokok` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_jual` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stok` int(11) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `batas_minimum` int(11) NOT NULL DEFAULT 5 CHECK (`batas_minimum` >= 0),
  `link_shopee` varchar(255) DEFAULT NULL,
  `deskripsi_produk` text DEFAULT NULL,
  `gambar_url` varchar(255) DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daftar_item`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `daftar_item` WRITE;
/*!40000 ALTER TABLE `daftar_item` DISABLE KEYS */;
INSERT INTO `daftar_item` VALUES
('BRG-001','Buku Tulis Okey 32 Halaman Sampul Coklat','Alat Tulis','Okey','A1','BARANG','pcs',2000.00,4000.00,120,'Buku Tulis Okey 32 Halaman hadir dengan sampul cokelat polos dan tampilan minimalis yang mendukung suasana belajar tenang.',NULL,5,'https://shopee.co.id/-PCS-Buku-Tulis-Okey-32-Halaman-Sampul-Coklat-i.105190766.50619319560?extraParams=%7B%22display_model_id%22%3A278022993601%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134207-81zth-mud29156f37pe2','Buku Tulis Okey 32 Halaman hadir dengan sampul cokelat polos dan tampilan minimalis yang mendukung suasana belajar tenang.','https://down-id.img.susercontent.com/file/id-11134207-81zth-mud29156f37pe2@resize_w900_nl.webp','2026-10-20 08:40:12','2026-11-05 22:41:29',NULL),
('BRG-002','Map Plastik Bussines Snelhecter','Map','Kiky','A2','BARANG','PAK',35000.00,37000.00,59,'harga diatas merupakan harga PACK isi 12 pcs map',NULL,5,'https://shopee.co.id/-PACK-Map-Plastik-Bussines-Snelhecter-i.105190766.47421030512?extraParams=%7B%22display_model_id%22%3A273063677668%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134253-81zth-mv0abhpbh7np43','harga diatas merupakan harga PACK isi 12 pcs map','https://down-id.img.susercontent.com/file/id-11134253-81zth-mv0abhpbh7np43@resize_w900_nl.webp','2026-10-20 08:40:12','2026-11-05 22:42:17',NULL),
('BRG-003','Buku Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko','Buku','Ideals Children\'s Book','B1','BARANG','pcs',13000.00,15000.00,4,'Ringkas, rapi, dan mudah dibawa—Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko siap menemani belajar kapan saja. Tampilan bergambar membantu memperjelas arti, sementara layout yang jelas membuat pencarian kata terasa lebih nyaman.\r\n\r\n\r\n\r\nKeunggulan Utama\r\n\r\n- Bergambar: ilustrasi membantu pemahaman arti kata.\r\n\r\n- Grammar & Conversation: dilengkapi materi grammar dan percakapan.\r\n\r\n- Cara membaca: dilengkapi cara membaca Inggris-Indonesia original.\r\n\r\n\r\n\r\nInformasi Produk\r\n\r\n- Penulis: Drs. P. Handoko\r\n\r\n- Penerbit: Lingkar Media\r\n\r\n- Jumlah Halaman: 128 hlm\r\n\r\n- Ukuran: 15 x 24 cm\r\n\r\n- Kertas: berwarna\r\n\r\n- Cover: Soft Cover\r\n\r\n- Berat: 200 gr\r\n\r\n- ISBN: 979-9994-339-6\r\n\r\n- Asal Produk: Indonesia\r\n\r\n- Impor/Lokal: Lokal',NULL,5,'https://shopee.co.id/Buku-Kamus-Ideal-Inggris-Indonesia-Bergambar-Drs.-P.-Handoko-i.105190766.54719275039?extraParams=%7B%22display_model_id%22%3A361615357532%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134207-81ztf-mubm296rjtok6b','Ringkas, rapi, dan mudah dibawa—Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko siap menemani belajar kapan saja. Tampilan bergambar membantu memperjelas arti, sementara layout yang jelas membuat pencarian kata terasa lebih nyaman.\r\n\r\n\r\n\r\nKeunggulan Utama\r\n\r\n- Bergambar: ilustrasi membantu pemahaman arti kata.\r\n\r\n- Grammar & Conversation: dilengkapi materi grammar dan percakapan.\r\n\r\n- Cara membaca: dilengkapi cara membaca Inggris-Indonesia original.\r\n\r\n\r\n\r\nInformasi Produk\r\n\r\n- Penulis: Drs. P. Handoko\r\n\r\n- Penerbit: Lingkar Media\r\n\r\n- Jumlah Halaman: 128 hlm\r\n\r\n- Ukuran: 15 x 24 cm\r\n\r\n- Kertas: berwarna\r\n\r\n- Cover: Soft Cover\r\n\r\n- Berat: 200 gr\r\n\r\n- ISBN: 979-9994-339-6\r\n\r\n- Asal Produk: Indonesia\r\n\r\n- Impor/Lokal: Lokal','https://down-id.img.susercontent.com/file/id-11134207-81ztf-mubm296rjtok6b@resize_w900_nl.webp','2026-10-20 08:40:12','2026-11-05 22:43:31',NULL),
('BRG-004','Spidol Snowman 12 Warna PW-12A','Alat Tulis','SNOWMAN','B2','BARANG','pcs',16000.00,18000.00,40,'Buat halaman kosong jadi penuh warna dengan Snowman Spidol 12 Warna PW-12A. Tinta cepat kering, tidak tembus kertas, dan ujung presisi—teman setia untuk mewarnai, menggambar, hingga catatan harian anak dan dewasa.\r\n\r\n\r\n\r\nKeunggulan Utama\r\n\r\n- 12 warna cerah yang merata, pas untuk kreativitas tanpa batas.\r\n\r\n- Tinta berbasis air: aman dan tidak berbau menyengat.\r\n\r\n- Ujung runcing presisi (fine tip) untuk garis rapi dan detail halus.\r\n\r\n- Nyaman digenggam, tutup rapat agar tinta tidak cepat kering.\r\n\r\n\r\n\r\nCocok Untuk\r\n\r\n- Mewarnai, menggambar, dan membuat catatan.\r\n\r\n- Tugas sekolah, hiasan tulisan, hingga kebutuhan kreatif sehari-hari.\r\n\r\n\r\n\r\nIsi & Ukuran\r\n\r\n- Jumlah isi: 1 set = 12 batang (12 warna lengkap).\r\n\r\n- Ukuran: Panjang ±14,5 cm, diameter ±1 cm.',NULL,5,'https://shopee.co.id/Spidol-Snowman-12-Warna-PW-12A-i.105190766.48519781419?extraParams=%7B%22display_model_id%22%3A366651586658%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134201-81ztc-mus5b9x95gjo03','Buat halaman kosong jadi penuh warna dengan Snowman Spidol 12 Warna PW-12A. Tinta cepat kering, tidak tembus kertas, dan ujung presisi—teman setia untuk mewarnai, menggambar, hingga catatan harian anak dan dewasa.\r\n\r\n\r\n\r\nKeunggulan Utama\r\n\r\n- 12 warna cerah yang merata, pas untuk kreativitas tanpa batas.\r\n\r\n- Tinta berbasis air: aman dan tidak berbau menyengat.\r\n\r\n- Ujung runcing presisi (fine tip) untuk garis rapi dan detail halus.\r\n\r\n- Nyaman digenggam, tutup rapat agar tinta tidak cepat kering.\r\n\r\n\r\n\r\nCocok Untuk\r\n\r\n- Mewarnai, menggambar, dan membuat catatan.\r\n\r\n- Tugas sekolah, hiasan tulisan, hingga kebutuhan kreatif sehari-hari.\r\n\r\n\r\n\r\nIsi & Ukuran\r\n\r\n- Jumlah isi: 1 set = 12 batang (12 warna lengkap).\r\n\r\n- Ukuran: Panjang ±14,5 cm, diameter ±1 cm.','https://down-id.img.susercontent.com/file/id-11134201-81ztc-mus5b9x95gjo03@resize_w900_nl.webp','2026-10-20 08:40:12','2026-11-05 22:41:03',NULL),
('BRG-005','Lem Tetes Korea G Lem Besi C Cair - Rekat Kuat, Serbaguna, Mudah Digunakan','Alat Tulis','-','B3','BARANG','pcs',12000.00,15000.00,8,'Lem Tetes Korea G Lem Besi C Cair adalah solusi ideal untuk berbagai kebutuhan perekat. Dengan daya rekat yang kuat, lem ini cocok untuk merekatkan berbagai jenis barang. Mudah digunakan, lem ini hadir dalam bentuk tetes yang praktis dan dapat digunakan untuk berbagai aplikasi.\r\n\r\n\r\n\r\nKeunggulan Produk:\r\n\r\n- Daya rekat tinggi\r\n\r\n- Mudah digunakan\r\n\r\n- Serbaguna untuk berbagai keperluan\r\n\r\n\r\n\r\nInformasi Tambahan:\r\n\r\nLem ini tersedia dalam kemasan yang praktis dan siap digunakan kapan saja Anda membutuhkannya. Cocok untuk berbagai kebutuhan rumah tangga dan industri. Dengan Lem Tetes Korea G Lem Besi C Cair, pekerjaan perekat Anda menjadi lebih mudah dan efisien.',NULL,5,'https://shopee.co.id/Lem-Tetes-Korea-G-Lem-Besi-C-Cair-Rekat-Kuat-Serbaguna-Mudah-Digunakan-i.105190766.52269718084?extraParams=%7B%22display_model_id%22%3A421647759871%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134207-81ztf-mum4q8nun18gf2','Lem Tetes Korea G Lem Besi C Cair adalah solusi ideal untuk berbagai kebutuhan perekat. Dengan daya rekat yang kuat, lem ini cocok untuk merekatkan berbagai jenis barang. Mudah digunakan, lem ini hadir dalam bentuk tetes yang praktis dan dapat digunakan untuk berbagai aplikasi.\r\n\r\n\r\n\r\nKeunggulan Produk:\r\n\r\n- Daya rekat tinggi\r\n\r\n- Mudah digunakan\r\n\r\n- Serbaguna untuk berbagai keperluan\r\n\r\n\r\n\r\nInformasi Tambahan:\r\n\r\nLem ini tersedia dalam kemasan yang praktis dan siap digunakan kapan saja Anda membutuhkannya. Cocok untuk berbagai kebutuhan rumah tangga dan industri. Dengan Lem Tetes Korea G Lem Besi C Cair, pekerjaan perekat Anda menjadi lebih mudah dan efisien.','https://down-id.img.susercontent.com/file/id-11134207-81ztf-mum4q8nun18gf2@resize_w900_nl.webp','2026-10-20 08:40:12','2026-11-05 22:44:18',NULL),
('BRG-006','Buku Kas Kwarto Soft Cover Kiky 4 Warna, 78 Lembar','Buku','Kiky','B3','BARANG','pcs',10000.00,12000.00,2,'Buku kas kwarto Kiky dengan soft cover untuk kebutuhan pencatatan yang rapi.\r\n\r\n\r\n\r\nDetail Produk\r\n\r\n- Varian warna: biru, merah, cokelat, dan oranye\r\n\r\n- Isi: 78 lembar',NULL,5,'https://shopee.co.id/Buku-Kas-Kwarto-Soft-Cover-Kiky-4-Warna-78-Lembar-i.105190766.27297022939?extraParams=%7B%22display_model_id%22%3A346688507723%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134207-81zto-mv0defmngv0i42','Buku kas kwarto Kiky dengan soft cover untuk kebutuhan pencatatan yang rapi.\r\n\r\n\r\n\r\nDetail Produk\r\n\r\n- Varian warna: biru, merah, cokelat, dan oranye\r\n\r\n- Isi: 78 lembar','https://down-id.img.susercontent.com/file/id-11134207-81zto-mv0defmngv0i42@resize_w900_nl.webp','2026-10-20 08:40:12','2026-11-05 22:45:34',NULL),
('BRG-007','Buku Tulis Polos Tanpa Garis 38 lembar','Buku','Best One','A1','BARANG','pcs',2500.00,3500.00,25,'Ringkas, fungsional, dan rapi untuk catatan harian. Buku tulis polos tanpa garis ini siap menemani rutinitas belajar dan kerja—membuat setiap ide mengalir lancar tanpa ribet.\r\n\r\n\r\n\r\nKeunggulan Utama:\r\n\r\n- Kertas 75 gsm yang berkualitas, tidak tembus tinta saat ditulis.\r\n\r\n- Desain polos tanpa garis, nyaman untuk berbagai jenis catatan.\r\n\r\n- Isi 38 lembar per buku.\r\n\r\n\r\n\r\nCatatan Pembelian:\r\n\r\n- Sampul random sesuai stok yang tersedia.\r\n\r\n- Jika ingin motif tertentu, silakan chat terlebih dahulu.\r\n\r\n\r\n\r\nDetail Produk:\r\n\r\n- Merek: Best One\r\n\r\n- Bahan kertas: 75 gsm',NULL,5,'https://shopee.co.id/Buku-Tulis-Polos-Tanpa-Garis-38-lembar-i.105190766.58169363821?extraParams=%7B%22display_model_id%22%3A316623626915%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134207-81ztc-muea2k5nrjt029','Ringkas, fungsional, dan rapi untuk catatan harian. Buku tulis polos tanpa garis ini siap menemani rutinitas belajar dan kerja—membuat setiap ide mengalir lancar tanpa ribet.\r\n\r\n\r\n\r\nKeunggulan Utama:\r\n\r\n- Kertas 75 gsm yang berkualitas, tidak tembus tinta saat ditulis.\r\n\r\n- Desain polos tanpa garis, nyaman untuk berbagai jenis catatan.\r\n\r\n- Isi 38 lembar per buku.\r\n\r\n\r\n\r\nCatatan Pembelian:\r\n\r\n- Sampul random sesuai stok yang tersedia.\r\n\r\n- Jika ingin motif tertentu, silakan chat terlebih dahulu.\r\n\r\n\r\n\r\nDetail Produk:\r\n\r\n- Merek: Best One\r\n\r\n- Bahan kertas: 75 gsm','https://down-id.img.susercontent.com/file/id-11134207-81ztc-muea2k5nrjt029@resize_w900_nl.webp','2026-10-20 08:40:12','2026-11-05 22:46:17',NULL),
('BRG-009','Lakban Bening','Perlengkapan Kantor','Daimaru','B2',NULL,'pcs',6000.00,8000.00,11,NULL,NULL,5,NULL,NULL,NULL,'2026-10-20 08:40:12','2026-11-08 23:31:59','2026-11-08 16:31:59'),
('BRG-010','Spidol Snowman Hitam','Alat Tulis','Snowman','A3',NULL,'pcs',5500.00,7500.00,18,NULL,NULL,5,NULL,NULL,NULL,'2026-10-20 08:40:12','2026-11-08 23:32:15','2026-11-08 16:32:15'),
('BRG-011','Amplop Coklat','Kertas','-','B1',NULL,'pcs',600.00,1000.00,199,NULL,NULL,5,NULL,NULL,NULL,'2026-10-20 08:40:12','2026-11-08 23:32:05','2026-11-08 16:32:05'),
('BRG-012','Jasa Fotocopy A4 (per lembar)','Jasa Fotocopy','-','-','JASA','lembar',150.00,300.00,999999,NULL,'/images/barang/BRG-012.jpg',5,NULL,NULL,'/images/barang/BRG-012.jpg','2026-10-20 08:40:12','2026-11-08 21:58:05',NULL);
/*!40000 ALTER TABLE `daftar_item` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `daftar_pembelian`
--

DROP TABLE IF EXISTS `daftar_pembelian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `daftar_pembelian` (
  `kode_transaksi` varchar(20) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `tanggal_transaksi` date NOT NULL,
  `total_bayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `id_pengguna` int(11) DEFAULT NULL,
  PRIMARY KEY (`kode_transaksi`),
  KEY `fk_daftar_pembelian_id_pengguna` (`id_pengguna`),
  CONSTRAINT `fk_daftar_pembelian_id_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daftar_pembelian`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `daftar_pembelian` WRITE;
/*!40000 ALTER TABLE `daftar_pembelian` DISABLE KEYS */;
INSERT INTO `daftar_pembelian` VALUES
('PB-20261003-001','CV Sumber Kertas','2026-10-20',0.00,1);
/*!40000 ALTER TABLE `daftar_pembelian` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `daftar_penjualan`
--

DROP TABLE IF EXISTS `daftar_penjualan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `daftar_penjualan` (
  `kode_transaksi` varchar(20) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `tanggal_transaksi` date NOT NULL,
  `total_bayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `id_kasir` int(11) DEFAULT NULL,
  `metode_bayar` enum('TUNAI','QRIS') DEFAULT NULL,
  PRIMARY KEY (`kode_transaksi`),
  KEY `fk_daftar_penjualan_id_kasir` (`id_kasir`),
  CONSTRAINT `fk_daftar_penjualan_id_kasir` FOREIGN KEY (`id_kasir`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daftar_penjualan`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `daftar_penjualan` WRITE;
/*!40000 ALTER TABLE `daftar_penjualan` DISABLE KEYS */;
INSERT INTO `daftar_penjualan` VALUES
('PJ-20261003-001','Umum','2026-10-20',0.00,2,'TUNAI'),
('PJ-20261027-0001','Umum','2026-10-27',13000.00,3,'TUNAI'),
('PJ-20261027-0002','Umum','2026-10-27',16000.00,3,'TUNAI');
/*!40000 ALTER TABLE `daftar_penjualan` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `daftar_transaksi`
--

DROP TABLE IF EXISTS `daftar_transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `daftar_transaksi` (
  `id_detail` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode_item` varchar(20) NOT NULL,
  `jumlah_item` int(11) NOT NULL DEFAULT 0,
  `item_keluar` int(11) NOT NULL DEFAULT 0,
  `item_masuk` int(11) NOT NULL DEFAULT 0,
  `retur_penjualan` int(11) NOT NULL DEFAULT 0,
  `retur_pembelian` int(11) NOT NULL DEFAULT 0,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `kode_penjualan` varchar(20) DEFAULT NULL,
  `kode_pembelian` varchar(20) DEFAULT NULL,
  `tanggal_log` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail`),
  KEY `daftar_transaksi_kode_item_foreign` (`kode_item`),
  KEY `daftar_transaksi_kode_penjualan_foreign` (`kode_penjualan`),
  KEY `daftar_transaksi_kode_pembelian_foreign` (`kode_pembelian`),
  CONSTRAINT `daftar_transaksi_kode_item_foreign` FOREIGN KEY (`kode_item`) REFERENCES `daftar_item` (`kode_item`),
  CONSTRAINT `daftar_transaksi_kode_pembelian_foreign` FOREIGN KEY (`kode_pembelian`) REFERENCES `daftar_pembelian` (`kode_transaksi`),
  CONSTRAINT `daftar_transaksi_kode_penjualan_foreign` FOREIGN KEY (`kode_penjualan`) REFERENCES `daftar_penjualan` (`kode_transaksi`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daftar_transaksi`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `daftar_transaksi` WRITE;
/*!40000 ALTER TABLE `daftar_transaksi` DISABLE KEYS */;
INSERT INTO `daftar_transaksi` VALUES
(1,'BRG-003',0,0,10,0,0,0.00,NULL,'PB-20261003-001','2026-10-20 08:40:12'),
(2,'BRG-006',0,0,5,0,0,0.00,NULL,'PB-20261003-001','2026-10-20 08:40:12'),
(3,'BRG-001',0,3,0,0,0,0.00,'PJ-20261003-001',NULL,'2026-10-20 08:40:12'),
(4,'BRG-002',0,2,0,0,0,0.00,'PJ-20261003-001',NULL,'2026-10-20 08:40:12'),
(5,'BRG-012',0,40,0,0,0,0.00,'PJ-20261003-001',NULL,'2026-10-20 08:40:12'),
(8,'BRG-011',1,1,0,0,0,1000.00,'PJ-20261027-0001',NULL,'2026-10-27 07:39:22'),
(9,'BRG-002',1,1,0,0,0,4000.00,'PJ-20261027-0001',NULL,'2026-10-27 07:39:22'),
(10,'BRG-009',1,1,0,0,0,8000.00,'PJ-20261027-0001',NULL,'2026-10-27 07:39:22'),
(11,'BRG-002',4,4,0,0,0,16000.00,'PJ-20261027-0002',NULL,'2026-10-27 07:41:44');
/*!40000 ALTER TABLE `daftar_transaksi` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `info_barang`
--

DROP TABLE IF EXISTS `info_barang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `info_barang` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `deskripsi` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info_barang`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `info_barang` WRITE;
/*!40000 ALTER TABLE `info_barang` DISABLE KEYS */;
/*!40000 ALTER TABLE `info_barang` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `log_aktivitas`
--

DROP TABLE IF EXISTS `log_aktivitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_aktivitas` (
  `id_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `waktu` datetime NOT NULL DEFAULT current_timestamp(),
  `keterangan` text DEFAULT NULL,
  `tipe_aksi` varchar(50) DEFAULT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `data_sebelum` text DEFAULT NULL,
  `data_sesudah` text DEFAULT NULL,
  PRIMARY KEY (`id_log`),
  KEY `idx_log_aktivitas_waktu` (`waktu`),
  KEY `idx_log_aktivitas_tipe_aksi` (`tipe_aksi`),
  KEY `fk_log_aktivitas_id_pengguna` (`id_pengguna`),
  CONSTRAINT `fk_log_aktivitas_id_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_aktivitas`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `log_aktivitas` WRITE;
/*!40000 ALTER TABLE `log_aktivitas` DISABLE KEYS */;
INSERT INTO `log_aktivitas` VALUES
(1,'2026-10-17 15:14:15','Item baru: TST_TINKER_01 - Nama Tinker','ITEM_BARU',1,NULL,NULL),
(2,'2026-10-17 15:24:18','Item baru: TST_DEV_01 - Test Dev','ITEM_BARU',1,NULL,NULL),
(3,'2026-10-18 14:58:46','Item baru: dsfsa - tip-x','ITEM_BARU',1,NULL,NULL),
(4,'2026-10-18 14:59:13','Ubah harga: dsfsa - tip-x','HARGA_DIUBAH',1,'{\"harga_pokok\": \"5000.00\", \"harga_jual\": \"1998.00\"}','{\"harga_pokok\": \"5000.00\", \"harga_jual\": \"5400.00\"}'),
(5,'2026-10-18 14:59:42','Hapus item: dsfsa - tip-x','ITEM_DIHAPUS',1,'{\"kode_item\": \"dsfsa\", \"nama_item\": \"tip-x\", \"jenis\": \"ATK\", \"merek\": \"SIDU\", \"stok\": \"6\", \"harga_pokok\": \"5000.00\", \"harga_jual\": \"5400.00\"}',NULL),
(6,'2026-10-20 08:40:12','Item baru: BRG-001 - Pulpen Standard AE7 Hitam','ITEM_BARU',1,NULL,NULL),
(7,'2026-10-20 08:40:12','Item baru: BRG-002 - Buku Tulis Sidu 38 Lembar','ITEM_BARU',1,NULL,NULL),
(8,'2026-10-20 08:40:12','Item baru: BRG-003 - Kertas HVS A4 70gr','ITEM_BARU',1,NULL,NULL),
(9,'2026-10-20 08:40:12','Item baru: BRG-004 - Map Plastik Folio','ITEM_BARU',1,NULL,NULL),
(10,'2026-10-20 08:40:12','Item baru: BRG-005 - Stapler HD-10','ITEM_BARU',1,NULL,NULL),
(11,'2026-10-20 08:40:12','Item baru: BRG-006 - Isi Staples No.10','ITEM_BARU',1,NULL,NULL),
(12,'2026-10-20 08:40:12','Item baru: BRG-007 - Penghapus Faber-Castell','ITEM_BARU',1,NULL,NULL),
(13,'2026-10-20 08:40:12','Item baru: BRG-008 - Pensil 2B','ITEM_BARU',1,NULL,NULL),
(14,'2026-10-20 08:40:12','Item baru: BRG-009 - Lakban Bening','ITEM_BARU',1,NULL,NULL),
(15,'2026-10-20 08:40:12','Item baru: BRG-010 - Spidol Snowman Hitam','ITEM_BARU',1,NULL,NULL),
(16,'2026-10-20 08:40:12','Item baru: BRG-011 - Amplop Coklat','ITEM_BARU',1,NULL,NULL),
(17,'2026-10-20 08:40:12','Item baru: BRG-012 - Jasa Fotocopy A4 (per lembar)','ITEM_BARU',1,NULL,NULL),
(18,'2026-10-20 08:40:12','Atur saldo awal tanggal 2026-10-03 sebesar 500000.00','SALDO_DIATUR',1,NULL,'{\"tanggal\": \"2026-10-03\", \"saldo_awal\": \"500000.00\"}'),
(21,'2026-10-27 07:39:22','Transaksi penjualan kasir PJ-20261027-0001 berhasil diproses (TUNAI). Total: Rp 13.000','PENJUALAN',3,NULL,NULL),
(22,'2026-10-27 07:41:44','Transaksi penjualan kasir PJ-20261027-0002 berhasil diproses (TUNAI). Total: Rp 16.000','PENJUALAN',3,NULL,NULL),
(23,'2026-10-27 16:06:19','Backup database: backup_toko_firo_2026-10-27_160618.sql (BERHASIL)','BACKUP',1,NULL,'{\"jenis\": \"MANUAL\", \"ukuran_kb\": \"108\", \"status\": \"BERHASIL\", \"nama_file\": \"backup_toko_firo_2026-10-27_160618.sql\"}'),
(24,'2026-10-28 03:47:24','Pembaruan profil pengguna: admin','UBAH_PROFIL',1,NULL,NULL),
(25,'2026-10-28 04:09:26','Update pengguna: admin','PENGGUNA_DIUBAH',1,'{\"nama\": \"Rossi\", \"username\": \"admin\", \"role\": \"ADMIN\"}','{\"nama\": \"Rossi\", \"username\": \"admin\", \"role\": \"ADMIN\"}'),
(26,'2026-11-03 18:11:55','Mengubah batas minimum item ACT-BRG-BATAS menjadi 18','STOK',NULL,NULL,NULL),
(27,'2026-11-03 18:15:32','Mengubah batas minimum item TEST-SP menjadi 18','STOK',NULL,NULL,NULL),
(28,'2026-11-05 16:19:56','Backup database: backup_toko_firo_2026-11-05_161956.sql (BERHASIL)','BACKUP',1,NULL,'{\"jenis\": \"MANUAL\", \"ukuran_kb\": \"13\", \"status\": \"BERHASIL\", \"nama_file\": \"backup_toko_firo_2026-11-05_161956.sql\"}'),
(29,'2026-11-05 22:35:11','Item diubah: BRG-001 - Buku Tulis Okey 32 Halaman Sampul Coklat','ITEM_DIUBAH',1,'{\"nama_item\": \"Pulpen Standard AE7 Hitam\", \"jenis\": \"Alat Tulis\", \"merek\": \"Standard\", \"rak\": \"A1\", \"satuan\": \"pcs\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}','{\"nama_item\": \"Buku Tulis Okey 32 Halaman Sampul Coklat\", \"jenis\": \"Alat Tulis\", \"merek\": \"Okey\", \"rak\": \"A1\", \"satuan\": \"pcs\", \"tipe_item\": \"BARANG\", \"keterangan\": \"Buku Tulis Okey 32 Halaman hadir dengan sampul cokelat polos dan tampilan minimalis yang mendukung suasana belajar tenang.\", \"batas_minimum\": \"5\"}'),
(30,'2026-11-05 22:38:06','Item diubah: BRG-002 - Map Plastik Bussines Snelhecter','ITEM_DIUBAH',1,'{\"nama_item\": \"Buku Tulis Sidu 38 Lembar\", \"jenis\": \"Buku\", \"merek\": \"Sidu\", \"rak\": \"A2\", \"satuan\": \"pcs\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}','{\"nama_item\": \"Map Plastik Bussines Snelhecter\", \"jenis\": \"Map\", \"merek\": \"Kiky\", \"rak\": \"A2\", \"satuan\": \"PAK\", \"tipe_item\": \"BARANG\", \"keterangan\": \"harga diatas merupakan harga PACK isi 12 pcs map\", \"batas_minimum\": \"5\"}'),
(31,'2026-11-05 22:39:27','Item diubah: BRG-003 - Buku Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko','ITEM_DIUBAH',1,'{\"nama_item\": \"Kertas HVS A4 70gr\", \"jenis\": \"Kertas\", \"merek\": \"PaperOne\", \"rak\": \"B1\", \"satuan\": \"rim\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}','{\"nama_item\": \"Buku Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko\", \"jenis\": \"Buku\", \"merek\": \"Ideals Children\'s Book\", \"rak\": \"B1\", \"satuan\": \"pcs\", \"tipe_item\": \"BARANG\", \"keterangan\": \"Ringkas, rapi, dan mudah dibawa—Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko siap menemani belajar kapan saja. Tampilan bergambar membantu memperjelas arti, sementara layout yang jelas membuat pencarian kata terasa lebih nyaman.\\r\\n\\r\\n\\r\\n\\r\\nKeunggulan Utama\\r\\n\\r\\n- Bergambar: ilustrasi membantu pemahaman arti kata.\\r\\n\\r\\n- Grammar & Conversation: dilengkapi materi grammar dan percakapan.\\r\\n\\r\\n- Cara membaca: dilengkapi cara membaca Inggris-Indonesia original.\\r\\n\\r\\n\\r\\n\\r\\nInformasi Produk\\r\\n\\r\\n- Penulis: Drs. P. Handoko\\r\\n\\r\\n- Penerbit: Lingkar Media\\r\\n\\r\\n- Jumlah Halaman: 128 hlm\\r\\n\\r\\n- Ukuran: 15 x 24 cm\\r\\n\\r\\n- Kertas: berwarna\\r\\n\\r\\n- Cover: Soft Cover\\r\\n\\r\\n- Berat: 200 gr\\r\\n\\r\\n- ISBN: 979-9994-339-6\\r\\n\\r\\n- Asal Produk: Indonesia\\r\\n\\r\\n- Impor/Lokal: Lokal\", \"batas_minimum\": \"5\"}'),
(32,'2026-11-05 22:40:40','Item diubah: BRG-004 - Spidol Snowman 12 Warna PW-12A','ITEM_DIUBAH',1,'{\"nama_item\": \"Map Plastik Folio\", \"jenis\": \"Perlengkapan Kantor\", \"merek\": \"-\", \"rak\": \"B2\", \"satuan\": \"pcs\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}','{\"nama_item\": \"Spidol Snowman 12 Warna PW-12A\", \"jenis\": \"Alat Tulis\", \"merek\": \"SNOWMAN\", \"rak\": \"B2\", \"satuan\": \"pcs\", \"tipe_item\": \"BARANG\", \"keterangan\": \"Buat halaman kosong jadi penuh warna dengan Snowman Spidol 12 Warna PW-12A. Tinta cepat kering, tidak tembus kertas, dan ujung presisi—teman setia untuk mewarnai, menggambar, hingga catatan harian anak dan dewasa.\\r\\n\\r\\n\\r\\n\\r\\nKeunggulan Utama\\r\\n\\r\\n- 12 warna cerah yang merata, pas untuk kreativitas tanpa batas.\\r\\n\\r\\n- Tinta berbasis air: aman dan tidak berbau menyengat.\\r\\n\\r\\n- Ujung runcing presisi (fine tip) untuk garis rapi dan detail halus.\\r\\n\\r\\n- Nyaman digenggam, tutup rapat agar tinta tidak cepat kering.\\r\\n\\r\\n\\r\\n\\r\\nCocok Untuk\\r\\n\\r\\n- Mewarnai, menggambar, dan membuat catatan.\\r\\n\\r\\n- Tugas sekolah, hiasan tulisan, hingga kebutuhan kreatif sehari-hari.\\r\\n\\r\\n\\r\\n\\r\\nIsi & Ukuran\\r\\n\\r\\n- Jumlah isi: 1 set = 12 batang (12 warna lengkap).\\r\\n\\r\\n- Ukuran: Panjang ±14,5 cm, diameter ±1 cm.\", \"batas_minimum\": \"5\"}'),
(33,'2026-11-05 22:41:03','Ubah harga: BRG-004 - Spidol Snowman 12 Warna PW-12A','HARGA_DIUBAH',1,'{\"harga_pokok\": \"1200.00\", \"harga_jual\": \"2000.00\"}','{\"harga_pokok\": \"16000.00\", \"harga_jual\": \"18000.00\"}'),
(34,'2026-11-05 22:41:29','Ubah harga: BRG-001 - Buku Tulis Okey 32 Halaman Sampul Coklat','HARGA_DIUBAH',1,'{\"harga_pokok\": \"1800.00\", \"harga_jual\": \"2500.00\"}','{\"harga_pokok\": \"2000.00\", \"harga_jual\": \"4000.00\"}'),
(35,'2026-11-05 22:42:17','Ubah harga: BRG-002 - Map Plastik Bussines Snelhecter','HARGA_DIUBAH',1,'{\"harga_pokok\": \"3000.00\", \"harga_jual\": \"4000.00\"}','{\"harga_pokok\": \"35000.00\", \"harga_jual\": \"37000.00\"}'),
(36,'2026-11-05 22:43:31','Ubah harga: BRG-003 - Buku Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko','HARGA_DIUBAH',1,'{\"harga_pokok\": \"45000.00\", \"harga_jual\": \"52000.00\"}','{\"harga_pokok\": \"13000.00\", \"harga_jual\": \"15000.00\"}'),
(37,'2026-11-05 22:44:18','Item diubah: BRG-005 - Lem Tetes Korea G Lem Besi C Cair - Rekat Kuat, Serbaguna, Mudah Digunakan','ITEM_DIUBAH',1,'{\"nama_item\": \"Stapler HD-10\", \"jenis\": \"Perlengkapan Kantor\", \"merek\": \"Max\", \"rak\": \"B3\", \"satuan\": \"pcs\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}','{\"nama_item\": \"Lem Tetes Korea G Lem Besi C Cair - Rekat Kuat, Serbaguna, Mudah Digunakan\", \"jenis\": \"Alat Tulis\", \"merek\": \"-\", \"rak\": \"B3\", \"satuan\": \"pcs\", \"tipe_item\": \"BARANG\", \"keterangan\": \"Lem Tetes Korea G Lem Besi C Cair adalah solusi ideal untuk berbagai kebutuhan perekat. Dengan daya rekat yang kuat, lem ini cocok untuk merekatkan berbagai jenis barang. Mudah digunakan, lem ini hadir dalam bentuk tetes yang praktis dan dapat digunakan untuk berbagai aplikasi.\\r\\n\\r\\n\\r\\n\\r\\nKeunggulan Produk:\\r\\n\\r\\n- Daya rekat tinggi\\r\\n\\r\\n- Mudah digunakan\\r\\n\\r\\n- Serbaguna untuk berbagai keperluan\\r\\n\\r\\n\\r\\n\\r\\nInformasi Tambahan:\\r\\n\\r\\nLem ini tersedia dalam kemasan yang praktis dan siap digunakan kapan saja Anda membutuhkannya. Cocok untuk berbagai kebutuhan rumah tangga dan industri. Dengan Lem Tetes Korea G Lem Besi C Cair, pekerjaan perekat Anda menjadi lebih mudah dan efisien.\", \"batas_minimum\": \"5\"}'),
(38,'2026-11-05 22:45:13','Item diubah: BRG-006 - Buku Kas Kwarto Soft Cover Kiky 4 Warna, 78 Lembar','ITEM_DIUBAH',1,'{\"nama_item\": \"Isi Staples No.10\", \"jenis\": \"Perlengkapan Kantor\", \"merek\": \"Max\", \"rak\": \"B3\", \"satuan\": \"box\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}','{\"nama_item\": \"Buku Kas Kwarto Soft Cover Kiky 4 Warna, 78 Lembar\", \"jenis\": \"Buku\", \"merek\": \"Kiky\", \"rak\": \"B3\", \"satuan\": \"pcs\", \"tipe_item\": \"BARANG\", \"keterangan\": \"Buku kas kwarto Kiky dengan soft cover untuk kebutuhan pencatatan yang rapi.\\r\\n\\r\\n\\r\\n\\r\\nDetail Produk\\r\\n\\r\\n- Varian warna: biru, merah, cokelat, dan oranye\\r\\n\\r\\n- Isi: 78 lembar\", \"batas_minimum\": \"5\"}'),
(39,'2026-11-05 22:45:34','Ubah harga: BRG-006 - Buku Kas Kwarto Soft Cover Kiky 4 Warna, 78 Lembar','HARGA_DIUBAH',1,'{\"harga_pokok\": \"2200.00\", \"harga_jual\": \"3000.00\"}','{\"harga_pokok\": \"10000.00\", \"harga_jual\": \"12000.00\"}'),
(40,'2026-11-05 22:46:17','Item diubah: BRG-007 - Buku Tulis Polos Tanpa Garis 38 lembar','ITEM_DIUBAH',1,'{\"nama_item\": \"Penghapus Faber-Castell\", \"jenis\": \"Alat Tulis\", \"merek\": \"Faber-Castell\", \"rak\": \"A1\", \"satuan\": \"pcs\", \"tipe_item\": null, \"keterangan\": null, \"batas_minimum\": \"5\"}','{\"nama_item\": \"Buku Tulis Polos Tanpa Garis 38 lembar\", \"jenis\": \"Buku\", \"merek\": \"Best One\", \"rak\": \"A1\", \"satuan\": \"pcs\", \"tipe_item\": \"BARANG\", \"keterangan\": \"Ringkas, fungsional, dan rapi untuk catatan harian. Buku tulis polos tanpa garis ini siap menemani rutinitas belajar dan kerja—membuat setiap ide mengalir lancar tanpa ribet.\\r\\n\\r\\n\\r\\n\\r\\nKeunggulan Utama:\\r\\n\\r\\n- Kertas 75 gsm yang berkualitas, tidak tembus tinta saat ditulis.\\r\\n\\r\\n- Desain polos tanpa garis, nyaman untuk berbagai jenis catatan.\\r\\n\\r\\n- Isi 38 lembar per buku.\\r\\n\\r\\n\\r\\n\\r\\nCatatan Pembelian:\\r\\n\\r\\n- Sampul random sesuai stok yang tersedia.\\r\\n\\r\\n- Jika ingin motif tertentu, silakan chat terlebih dahulu.\\r\\n\\r\\n\\r\\n\\r\\nDetail Produk:\\r\\n\\r\\n- Merek: Best One\\r\\n\\r\\n- Bahan kertas: 75 gsm\", \"batas_minimum\": \"5\"}'),
(41,'2026-11-07 14:51:36','Item baru: 12341234123 - Tip-x joyko','ITEM_BARU',1,NULL,NULL),
(42,'2026-11-07 14:51:44','Hapus item: 12341234123 - Tip-x joyko','ITEM_DIHAPUS',1,'{\"kode_item\": \"12341234123\", \"nama_item\": \"Tip-x joyko\", \"jenis\": \"Alat Tulis\", \"merek\": \"Kiky\", \"stok\": \"1\", \"harga_pokok\": \"0.00\", \"harga_jual\": \"7500.00\"}',NULL),
(43,'2026-11-08 21:50:33','Hapus item: BRG-008 - Pensil 2B','ITEM_DIHAPUS',1,'{\"kode_item\": \"BRG-008\", \"nama_item\": \"Pensil 2B\", \"jenis\": \"Alat Tulis\", \"merek\": \"Faber-Castell\", \"stok\": \"0\", \"harga_pokok\": \"2000.00\", \"harga_jual\": \"3000.00\"}',NULL),
(44,'2026-11-08 21:52:49','Hapus pengguna: andi','PENGGUNA_DIHAPUS',1,'{\"id_pengguna\": \"4\", \"username\": \"andi\", \"nama\": \"Andi\"}',NULL),
(45,'2026-11-08 21:53:07','Ubah status pengguna: junaidi menjadi NONAKTIF','STATUS_PENGGUNA',1,'{\"status\": \"AKTIF\"}','{\"status\": \"NONAKTIF\"}'),
(46,'2026-11-08 21:58:05','Item diubah: BRG-012 - Jasa Fotocopy A4 (per lembar)','ITEM_DIUBAH',1,'{\"nama_item\": \"Jasa Fotocopy A4 (per lembar)\", \"jenis\": \"Jasa Fotocopy\", \"merek\": \"-\", \"rak\": \"-\", \"satuan\": \"lembar\", \"tipe_item\": \"JASA\", \"keterangan\": null, \"batas_minimum\": \"5\"}','{\"nama_item\": \"Jasa Fotocopy A4 (per lembar)\", \"jenis\": \"Jasa Fotocopy\", \"merek\": \"-\", \"rak\": \"-\", \"satuan\": \"lembar\", \"tipe_item\": \"JASA\", \"keterangan\": null, \"batas_minimum\": \"5\"}'),
(47,'2026-11-08 23:31:59','Hapus item: BRG-009 - Lakban Bening','ITEM_DIHAPUS',1,NULL,NULL),
(48,'2026-11-08 23:32:05','Hapus item: BRG-011 - Amplop Coklat','ITEM_DIHAPUS',1,NULL,NULL),
(49,'2026-11-08 23:32:15','Hapus item: BRG-010 - Spidol Snowman Hitam','ITEM_DIHAPUS',1,NULL,NULL);
/*!40000 ALTER TABLE `log_aktivitas` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_log_aktivitas_cegah_update` BEFORE UPDATE ON `log_aktivitas`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Log aktivitas tidak boleh diubah atau dihapus!';
END 
*/;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_log_aktivitas_cegah_delete` BEFORE DELETE ON `log_aktivitas`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Log aktivitas tidak boleh diubah atau dihapus!';
END 
*/;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `log_notifikasi`
--

DROP TABLE IF EXISTS `log_notifikasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_notifikasi` (
  `id_notif` int(11) NOT NULL AUTO_INCREMENT,
  `waktu` datetime DEFAULT current_timestamp(),
  `jenis` enum('LAPORAN','STOK_MINIMUM','ITEM_BARU','ITEM_DIUBAH','BACKUP','OTP','TES') NOT NULL,
  `tujuan` enum('TELEGRAM','WHATSAPP') DEFAULT 'TELEGRAM',
  `penerima` varchar(100) DEFAULT NULL,
  `isi` text DEFAULT NULL,
  `status` enum('MENUNGGU','TERKIRIM','GAGAL') DEFAULT 'MENUNGGU',
  `pesan_error` text DEFAULT NULL,
  PRIMARY KEY (`id_notif`),
  KEY `idx_log_notifikasi_waktu` (`waktu`),
  KEY `idx_log_notifikasi_jenis_status` (`jenis`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_notifikasi`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `log_notifikasi` WRITE;
/*!40000 ALTER TABLE `log_notifikasi` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_notifikasi` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_10_09_000001_create_daftar_item_table',1),
(5,'2026_10_09_000002_create_daftar_pembelian_table',1),
(6,'2026_10_09_000003_create_daftar_penjualan_table',1),
(7,'2026_10_09_000004_create_daftar_transaksi_table',1),
(8,'2026_10_09_000005_create_log_aktivitas_table',1),
(9,'2026_10_10_100143_add_foto_to_daftar_item',2),
(10,'2026_10_10_200000_modify_users_table_for_auth',2),
(11,'2026_10_24_105035_add_saldo_akhir_to_saldo_harian_table',3),
(12,'2026_11_05_222000_add_katalog_columns_to_daftar_item_table',4),
(13,'2026_11_08_223000_add_deleted_at_to_daftar_item_table',5),
(14,'2026_10_20_000000_create_saldo_harian_table',6),
(15,'2026_11_09_064248_create_log_notifikasi_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `notifikasi_dibaca`
--

DROP TABLE IF EXISTS `notifikasi_dibaca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifikasi_dibaca` (
  `id_log` int(10) unsigned NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `dibaca_pada` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`,`id_pengguna`),
  KEY `idx_notifikasi_dibaca_id_pengguna` (`id_pengguna`),
  CONSTRAINT `fk_notifikasi_dibaca_id_log` FOREIGN KEY (`id_log`) REFERENCES `log_aktivitas` (`id_log`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_notifikasi_dibaca_id_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifikasi_dibaca`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `notifikasi_dibaca` WRITE;
/*!40000 ALTER TABLE `notifikasi_dibaca` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifikasi_dibaca` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `otp_2fa`
--

DROP TABLE IF EXISTS `otp_2fa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `otp_2fa` (
  `id_otp` int(11) NOT NULL AUTO_INCREMENT,
  `id_pengguna` int(11) NOT NULL,
  `kode_hash` varchar(255) NOT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `kadaluarsa_pada` datetime NOT NULL,
  `digunakan` tinyint(4) DEFAULT 0,
  `percobaan` int(11) DEFAULT 0,
  PRIMARY KEY (`id_otp`),
  KEY `idx_otp_2fa_id_pengguna` (`id_pengguna`),
  CONSTRAINT `fk_otp_2fa_id_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp_2fa`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `otp_2fa` WRITE;
/*!40000 ALTER TABLE `otp_2fa` DISABLE KEYS */;
/*!40000 ALTER TABLE `otp_2fa` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `pengaturan_notifikasi`
--

DROP TABLE IF EXISTS `pengaturan_notifikasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengaturan_notifikasi` (
  `kunci` varchar(50) NOT NULL,
  `nilai` varchar(255) DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`kunci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengaturan_notifikasi`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `pengaturan_notifikasi` WRITE;
/*!40000 ALTER TABLE `pengaturan_notifikasi` DISABLE KEYS */;
INSERT INTO `pengaturan_notifikasi` VALUES
('backup_frekuensi','HARIAN','2026-10-20 08:40:12'),
('backup_jam','22:00','2026-10-20 08:40:12'),
('backup_otomatis','0','2026-10-20 08:40:12'),
('laporan_jam','21:00','2026-10-20 08:40:12'),
('laporan_otomatis','1','2026-10-20 08:40:12'),
('notif_backup','1','2026-10-20 08:40:12'),
('notif_item_baru','1','2026-10-20 08:40:12'),
('notif_otp','1','2026-10-20 08:40:12'),
('notif_stok_minimum','1','2026-10-20 08:40:12'),
('telegram_chat_id_pemilik','','2026-10-20 08:40:12');
/*!40000 ALTER TABLE `pengaturan_notifikasi` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `pengguna`
--

DROP TABLE IF EXISTS `pengguna`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('ADMIN','KASIR') NOT NULL,
  `status` enum('AKTIF','NONAKTIF') DEFAULT 'AKTIF',
  `telegram_chat_id` varchar(50) DEFAULT NULL,
  `login_terakhir` datetime DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pengguna`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_pengguna_role_status` (`role`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengguna`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `pengguna` WRITE;
/*!40000 ALTER TABLE `pengguna` DISABLE KEYS */;
INSERT INTO `pengguna` VALUES
(1,'Rossi','admin','$2y$12$BSX/FWAh2gBzDI1yrN56K.eywMd5GgjANAcJ7UN4VN1Su6fBssqhK','ADMIN','AKTIF',NULL,'2026-10-19 02:36:47','2026-10-16 15:56:54','2026-10-28 04:09:26'),
(2,'Junaidi','junaidi','$2y$12$V5PmkqQ6kbflTJXPnF7PNuD8bP3a4rMQLoAVCWI07RAlKp7kyxdPi','ADMIN','NONAKTIF',NULL,NULL,'2026-10-20 08:40:12','2026-11-08 21:53:07'),
(3,'Rina','rina','$2y$12$9ryKLpywqdK.bRgqfNdT4.FKw0/oMcZYrwO2V8A245FSaC.3Ku5Z6','KASIR','AKTIF',NULL,NULL,'2026-10-20 08:40:12','2026-10-27 01:52:14');
/*!40000 ALTER TABLE `pengguna` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `riwayat_backup`
--

DROP TABLE IF EXISTS `riwayat_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `riwayat_backup` (
  `id_backup` int(11) NOT NULL AUTO_INCREMENT,
  `waktu` datetime DEFAULT current_timestamp(),
  `jenis` enum('MANUAL','OTOMATIS') NOT NULL,
  `ukuran_kb` int(11) DEFAULT NULL,
  `status` enum('BERHASIL','GAGAL') NOT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `pesan_error` text DEFAULT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_backup`),
  KEY `idx_riwayat_backup_id_pengguna` (`id_pengguna`),
  KEY `idx_riwayat_backup_waktu` (`waktu`),
  CONSTRAINT `fk_riwayat_backup_id_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riwayat_backup`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `riwayat_backup` WRITE;
/*!40000 ALTER TABLE `riwayat_backup` DISABLE KEYS */;
INSERT INTO `riwayat_backup` VALUES
(1,'2026-10-27 16:06:19','MANUAL',108,'BERHASIL','backup_toko_firo_2026-10-27_160618.sql',NULL,1),
(2,'2026-11-05 16:19:56','MANUAL',13,'BERHASIL','backup_toko_firo_2026-11-05_161956.sql',NULL,1);
/*!40000 ALTER TABLE `riwayat_backup` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `saldo_harian`
--

DROP TABLE IF EXISTS `saldo_harian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `saldo_harian` (
  `tanggal` date NOT NULL,
  `saldo_awal` decimal(15,2) DEFAULT 0.00,
  `diatur_oleh` int(11) DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `saldo_akhir` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`tanggal`),
  KEY `idx_saldo_harian_diatur_oleh` (`diatur_oleh`),
  CONSTRAINT `fk_saldo_harian_diatur_oleh` FOREIGN KEY (`diatur_oleh`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saldo_harian`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `saldo_harian` WRITE;
/*!40000 ALTER TABLE `saldo_harian` DISABLE KEYS */;
INSERT INTO `saldo_harian` VALUES
('2026-10-03',500000.00,1,'2026-10-20 08:40:12',0.00),
('2026-10-27',0.00,NULL,'2026-10-27 01:56:17',29000.00);
/*!40000 ALTER TABLE `saldo_harian` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kasir') NOT NULL DEFAULT 'kasir',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Pemilik Toko','admin','admin@tokofiro.local',NULL,'$2y$12$n4NOgXA1UgN5pCep.W5DmuxBLJXLEH5jWcmAlXNNZsnztGXhbN0vy','admin',NULL,'2026-10-21 18:28:54','2026-10-21 18:28:54'),
(2,'Kasir 1','kasir1',NULL,NULL,'$2y$12$TPwrfnzYZ9ltFci4UEZ5/uQXRlIrkmipE3xkM9toj0ikFHeDlPHAK','kasir',NULL,'2026-10-21 18:28:54','2026-10-21 18:28:54');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Temporary table structure for view `v_barang_terlaris`
--

DROP TABLE IF EXISTS `v_barang_terlaris`;
/*!50001 DROP VIEW IF EXISTS `v_barang_terlaris`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_barang_terlaris` AS SELECT
 NULL AS `kode_item`,
 NULL AS `nama_item`,
 NULL AS `total_terjual` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_info_barang`
--

DROP TABLE IF EXISTS `v_info_barang`;
/*!50001 DROP VIEW IF EXISTS `v_info_barang`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_info_barang` AS SELECT
 NULL AS `id_log`,
 NULL AS `waktu`,
 NULL AS `tipe_aksi`,
 NULL AS `keterangan`,
 NULL AS `data_sebelum`,
 NULL AS `data_sesudah`,
 NULL AS `id_pengguna` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_katalog_publik`
--

DROP TABLE IF EXISTS `v_katalog_publik`;
/*!50001 DROP VIEW IF EXISTS `v_katalog_publik`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_katalog_publik` AS SELECT
 NULL AS `kode_item`,
 NULL AS `nama_item`,
 NULL AS `jenis`,
 NULL AS `merek`,
 NULL AS `satuan`,
 NULL AS `harga_jual`,
 NULL AS `stok_tampil`,
 NULL AS `status_stok`,
 NULL AS `keterangan` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_stok_item`
--

DROP TABLE IF EXISTS `v_stok_item`;
/*!50001 DROP VIEW IF EXISTS `v_stok_item`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_stok_item` AS SELECT
 NULL AS `kode_item`,
 NULL AS `nama_item`,
 NULL AS `stok`,
 NULL AS `satuan`,
 NULL AS `harga_pokok`,
 NULL AS `harga_jual`,
 NULL AS `margin_untung`,
 NULL AS `status_stok`,
 NULL AS `jenis`,
 NULL AS `merek`,
 NULL AS `rak`,
 NULL AS `tipe_item`,
 NULL AS `batas_minimum` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_stok_menipis`
--

DROP TABLE IF EXISTS `v_stok_menipis`;
/*!50001 DROP VIEW IF EXISTS `v_stok_menipis`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_stok_menipis` AS SELECT
 NULL AS `kode_item`,
 NULL AS `nama_item`,
 NULL AS `stok`,
 NULL AS `satuan`,
 NULL AS `harga_pokok`,
 NULL AS `harga_jual`,
 NULL AS `margin_untung`,
 NULL AS `status_stok`,
 NULL AS `jenis`,
 NULL AS `merek`,
 NULL AS `rak`,
 NULL AS `tipe_item`,
 NULL AS `batas_minimum` */;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'toko_firo'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_barang_terlaris` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_barang_terlaris`(
    IN `p_start` DATE,
    IN `p_end` DATE,
    IN `p_limit` INT
)
BEGIN
    DECLARE v_limit INT;
    SET v_limit = IF(p_limit IS NULL OR p_limit <= 0, 10, p_limit);

    SELECT
        di.`kode_item`,
        di.`nama_item`,
        SUM(COALESCE(dt.`item_keluar`, 0) - COALESCE(dt.`retur_penjualan`, 0)) AS `total_terjual`
    FROM `daftar_transaksi` dt
    JOIN `daftar_item` di USING (`kode_item`)
    WHERE dt.`kode_penjualan` IS NOT NULL
      AND dt.`tanggal_log` >= p_start
      AND dt.`tanggal_log` < p_end + INTERVAL 1 DAY
    GROUP BY di.`kode_item`, di.`nama_item`
    ORDER BY `total_terjual` DESC
    LIMIT v_limit;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_buat_otp` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_buat_otp`(
    IN `p_id_pengguna` INT,
    IN `p_kode_hash` VARCHAR(255)
)
BEGIN
    UPDATE `otp_2fa`
    SET `digunakan` = 1
    WHERE `id_pengguna` = p_id_pengguna AND `digunakan` = 0;

    INSERT INTO `otp_2fa` (
        `id_pengguna`,
        `kode_hash`,
        `dibuat_pada`,
        `kadaluarsa_pada`,
        `digunakan`,
        `percobaan`
    ) VALUES (
        p_id_pengguna,
        p_kode_hash,
        NOW(),
        NOW() + INTERVAL 5 MINUTE,
        0,
        0
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_cari_item` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cari_item`(
    IN `p_keyword` VARCHAR(100)
)
BEGIN
    SELECT *
    FROM `v_stok_item`
    WHERE `nama_item` LIKE CONCAT('%', p_keyword, '%')
       OR `kode_item` = p_keyword;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_catat_backup` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_catat_backup`(
    IN `p_jenis` VARCHAR(50),
    IN `p_ukuran_kb` INT,
    IN `p_status` VARCHAR(50),
    IN `p_nama_file` VARCHAR(255),
    IN `p_pesan_error` TEXT,
    IN `p_pelaku` INT
)
BEGIN
    INSERT INTO `riwayat_backup` (
        `waktu`, `jenis`, `ukuran_kb`, `status`, `nama_file`, `pesan_error`, `id_pengguna`
    ) VALUES (
        NOW(), p_jenis, p_ukuran_kb, p_status, p_nama_file, p_pesan_error, p_pelaku
    );

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Backup database: ', p_nama_file, ' (', p_status, ')'),
        'BACKUP',
        p_pelaku,
        NULL,
        JSON_OBJECT(
            'jenis', p_jenis,
            'ukuran_kb', p_ukuran_kb,
            'status', p_status,
            'nama_file', p_nama_file
        )
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_catat_login` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_catat_login`(
    IN `p_id_pengguna` INT
)
BEGIN
    DECLARE v_username VARCHAR(50);

    SELECT `username` INTO v_username
    FROM `pengguna`
    WHERE `id_pengguna` = p_id_pengguna;

    UPDATE `pengguna`
    SET `login_terakhir` = NOW()
    WHERE `id_pengguna` = p_id_pengguna;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`
    ) VALUES (
        NOW(),
        CONCAT('Login: ', COALESCE(v_username, '')),
        'LOGIN',
        p_id_pengguna
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_catat_notifikasi` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_catat_notifikasi`(
    IN `p_jenis` VARCHAR(50),
    IN `p_tujuan` VARCHAR(50),
    IN `p_penerima` VARCHAR(100),
    IN `p_isi` TEXT
)
BEGIN
    DECLARE v_tujuan VARCHAR(50);

    SET v_tujuan = COALESCE(NULLIF(TRIM(p_tujuan), ''), 'TELEGRAM');

    INSERT INTO `log_notifikasi` (
        `waktu`, `jenis`, `tujuan`, `penerima`, `isi`, `status`
    ) VALUES (
        NOW(), p_jenis, v_tujuan, p_penerima, p_isi, 'MENUNGGU'
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_create_item` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_item`(
    IN `p_kode` VARCHAR(20),
    IN `p_nama` VARCHAR(100),
    IN `p_jenis` VARCHAR(50),
    IN `p_merek` VARCHAR(50),
    IN `p_rak` VARCHAR(20),
    IN `p_satuan` VARCHAR(20),
    IN `p_h_pokok` DECIMAL(15,2),
    IN `p_h_jual` DECIMAL(15,2),
    IN `p_stok` INT,
    IN `p_tipe` VARCHAR(20),
    IN `p_ket` TEXT,
    IN `p_batas` INT,
    IN `p_id_pengguna` INT
)
BEGIN
    DECLARE v_stok INT;
    DECLARE v_batas INT;

    IF EXISTS (SELECT 1 FROM `daftar_item` WHERE `kode_item` = p_kode) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kode item sudah dipakai!';
    END IF;

    SET v_stok = p_stok;
    IF p_tipe = 'JASA' AND (v_stok IS NULL OR v_stok = 0) THEN
        SET v_stok = 999999;
    END IF;

    SET v_batas = COALESCE(p_batas, 5);

    INSERT INTO `daftar_item` (
        `kode_item`, `nama_item`, `jenis`, `merek`, `rak`, `tipe_item`,
        `satuan`, `harga_pokok`, `harga_jual`, `stok`, `keterangan`,
        `batas_minimum`, `dibuat_pada`, `diubah_pada`
    ) VALUES (
        p_kode, p_nama, p_jenis, p_merek, p_rak, p_tipe,
        p_satuan, p_h_pokok, p_h_jual, v_stok, p_ket,
        v_batas, NOW(), NOW()
    );

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Item baru: ', p_kode, ' - ', p_nama),
        'ITEM_BARU',
        p_id_pengguna,
        NULL,
        NULL
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_create_pembelian` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_pembelian`(
    IN `p_kode` VARCHAR(20),
    IN `p_supp` VARCHAR(100),
    IN `p_id_pengguna` INT
)
BEGIN
    IF p_supp IS NULL OR TRIM(p_supp) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Nama supplier wajib diisi!';
    END IF;

    INSERT INTO `daftar_pembelian` (
        `kode_transaksi`,
        `nama_supplier`,
        `tanggal_transaksi`,
        `total_bayar`,
        `id_pengguna`
    ) VALUES (
        p_kode,
        TRIM(p_supp),
        CURDATE(),
        0.00,
        p_id_pengguna
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_create_pengguna` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_pengguna`(
    IN p_nama VARCHAR(100),
    IN p_username VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_password_hash VARCHAR(255),
    IN p_role ENUM('ADMIN','KASIR'),
    IN p_pelaku INT
)
BEGIN
    IF EXISTS (SELECT 1 FROM toko_firo.pengguna WHERE username = p_username) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Username sudah dipakai!';
    END IF;

    INSERT INTO toko_firo.pengguna (nama, username, password_hash, role, status, dibuat_pada, diubah_pada)
    VALUES (p_nama, p_username, p_password_hash, p_role, 'AKTIF', NOW(), NOW());

    INSERT INTO toko_firo.log_aktivitas (waktu, keterangan, tipe_aksi, id_pengguna)
    VALUES (NOW(), CONCAT('Pengguna baru: ', p_username), 'PENGGUNA_BARU', p_pelaku);
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_create_penjualan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_penjualan`(
    IN `p_kode` VARCHAR(20),
    IN `p_cust` VARCHAR(100),
    IN `p_id_kasir` INT,
    IN `p_metode` ENUM('TUNAI','QRIS')
)
BEGIN
    DECLARE v_cust VARCHAR(100);

    SET v_cust = TRIM(p_cust);
    IF v_cust IS NULL OR v_cust = '' THEN
        SET v_cust = 'Umum';
    END IF;

    INSERT INTO `daftar_penjualan` (
        `kode_transaksi`,
        `nama_pelanggan`,
        `tanggal_transaksi`,
        `total_bayar`,
        `id_kasir`,
        `metode_bayar`
    ) VALUES (
        p_kode,
        v_cust,
        CURDATE(),
        0.00,
        p_id_kasir,
        p_metode
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_delete_item` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_item`(
    IN `p_kode` VARCHAR(20),
    IN `p_id_pengguna` INT
)
BEGIN
    DECLARE v_nama VARCHAR(100);
    DECLARE v_jenis VARCHAR(50);
    DECLARE v_merek VARCHAR(50);
    DECLARE v_stok INT;
    DECLARE v_h_pokok DECIMAL(15,2);
    DECLARE v_h_jual DECIMAL(15,2);

    IF EXISTS (SELECT 1 FROM `daftar_transaksi` WHERE `kode_item` = p_kode) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Item tidak bisa dihapus karena sudah memiliki riwayat transaksi!';
    END IF;

    SELECT `nama_item`, `jenis`, `merek`, `stok`, `harga_pokok`, `harga_jual`
    INTO v_nama, v_jenis, v_merek, v_stok, v_h_pokok, v_h_jual
    FROM `daftar_item`
    WHERE `kode_item` = p_kode;

    IF v_nama IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Item tidak ditemukan!';
    END IF;

    DELETE FROM `daftar_item` WHERE `kode_item` = p_kode;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Hapus item: ', p_kode, ' - ', v_nama),
        'ITEM_DIHAPUS',
        p_id_pengguna,
        JSON_OBJECT(
            'kode_item', p_kode,
            'nama_item', v_nama,
            'jenis', v_jenis,
            'merek', v_merek,
            'stok', v_stok,
            'harga_pokok', v_h_pokok,
            'harga_jual', v_h_jual
        ),
        NULL
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_delete_item_v2` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_item_v2`(
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

    
    
    
    IF EXISTS (SELECT 1 FROM `daftar_transaksi` WHERE `kode_item` = p_kode) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Item tidak bisa dihapus karena sudah memiliki riwayat transaksi!';
    END IF;

    
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
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_delete_pengguna` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_pengguna`(IN p_id INT, IN p_pelaku INT)
BEGIN DECLARE v_username VARCHAR(50); DECLARE v_nama VARCHAR(100); IF p_id = p_pelaku THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tidak bisa menghapus akun sendiri!'; END IF; SELECT username, nama INTO v_username, v_nama FROM pengguna WHERE id_pengguna = p_id; IF v_username IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengguna tidak ditemukan!'; END IF; IF p_id = 1 OR LOWER(v_username) = 'admin' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akun Administrator Pemilik Toko utama tidak dapat dihapus!'; END IF; IF EXISTS (SELECT 1 FROM daftar_penjualan WHERE id_kasir = p_id) OR EXISTS (SELECT 1 FROM daftar_pembelian WHERE id_pengguna = p_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akun memiliki riwayat transaksi dan tidak dapat dihapus. Silakan gunakan fitur Nonaktifkan Akun.'; END IF; DELETE FROM pengguna WHERE id_pengguna = p_id; INSERT INTO log_aktivitas (waktu, keterangan, tipe_aksi, id_pengguna, data_sebelum, data_sesudah) VALUES (NOW(), CONCAT('Hapus pengguna: ', v_username), 'PENGGUNA_DIHAPUS', p_pelaku, JSON_OBJECT('id_pengguna', p_id, 'username', v_username, 'nama', v_nama), NULL); END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_info_barang` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_info_barang`(
    IN `p_id_pengguna` INT,
    IN `p_tipe` VARCHAR(50),
    IN `p_limit` INT,
    IN `p_offset` INT
)
BEGIN
    DECLARE v_limit INT;
    DECLARE v_offset INT;

    SET v_limit = IF(p_limit IS NULL OR p_limit <= 0, 20, p_limit);
    SET v_offset = IF(p_offset IS NULL OR p_offset < 0, 0, p_offset);

    SELECT
        vib.*,
        IF(nd.`id_log` IS NOT NULL, 1, 0) AS `sudah_dibaca`
    FROM `v_info_barang` vib
    LEFT JOIN `notifikasi_dibaca` nd
      ON nd.`id_log` = vib.`id_log`
     AND nd.`id_pengguna` = p_id_pengguna
    WHERE (p_tipe IS NULL OR vib.`tipe_aksi` = p_tipe)
    ORDER BY vib.`waktu` DESC
    LIMIT v_limit OFFSET v_offset;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_laporan_penjualan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_laporan_penjualan`(
    IN `start_d` DATE,
    IN `end_d` DATE,
    IN `p_id_kasir` INT
)
BEGIN
    IF p_id_kasir IS NOT NULL THEN
        SELECT lp.*
        FROM `v_laporan_penjualan` lp
        JOIN `daftar_penjualan` dp ON lp.`kode_transaksi` = dp.`kode_transaksi`
        WHERE lp.`tanggal_transaksi` BETWEEN start_d AND end_d
          AND dp.`id_kasir` = p_id_kasir;
    ELSE
        SELECT *
        FROM `v_laporan_penjualan`
        WHERE `tanggal_transaksi` BETWEEN start_d AND end_d;
    END IF;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_log` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_log`(
    IN `p_tipe` VARCHAR(50),
    IN `p_start` DATE,
    IN `p_end` DATE,
    IN `p_keyword` VARCHAR(100),
    IN `p_limit` INT,
    IN `p_offset` INT
)
BEGIN
    DECLARE v_limit INT;
    DECLARE v_offset INT;

    SET v_limit = IF(p_limit IS NULL OR p_limit <= 0, 50, p_limit);
    SET v_offset = IF(p_offset IS NULL OR p_offset < 0, 0, p_offset);

    SELECT
        la.*,
        p.`nama` AS `nama_pengguna`
    FROM `log_aktivitas` la
    LEFT JOIN `pengguna` p ON la.`id_pengguna` = p.`id_pengguna`
    WHERE (p_tipe IS NULL OR la.`tipe_aksi` = p_tipe)
      AND (p_start IS NULL OR la.`waktu` >= p_start)
      AND (p_end IS NULL OR la.`waktu` < p_end + INTERVAL 1 DAY)
      AND (p_keyword IS NULL OR la.`keterangan` LIKE CONCAT('%', p_keyword, '%'))
    ORDER BY la.`waktu` DESC
    LIMIT v_limit OFFSET v_offset;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_log_notifikasi` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_log_notifikasi`(
    IN `p_jenis` VARCHAR(50),
    IN `p_limit` INT,
    IN `p_offset` INT
)
BEGIN
    DECLARE v_limit INT;
    DECLARE v_offset INT;

    SET v_limit = IF(p_limit IS NULL OR p_limit <= 0, 50, p_limit);
    SET v_offset = IF(p_offset IS NULL OR p_offset < 0, 0, p_offset);

    SELECT *
    FROM `log_notifikasi`
    WHERE (p_jenis IS NULL OR `jenis` = p_jenis)
    ORDER BY `waktu` DESC
    LIMIT v_limit OFFSET v_offset;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_pengguna_login` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_pengguna_login`(
    IN `p_username` VARCHAR(50)
)
BEGIN
    SELECT `id_pengguna`, `nama`, `username`, `password_hash`, `role`
    FROM `pengguna`
    WHERE `username` = p_username AND `status` = 'AKTIF';
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_saldo_harian` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_saldo_harian`(
    IN `p_tanggal` DATE,
    IN `p_id_kasir` INT
)
BEGIN
    DECLARE v_tgl_jangkar DATE;
    DECLARE v_saldo_awal DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_pemasukan_sebelumnya DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_pengeluaran_sebelumnya DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_saldo_awal_hari DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_pemasukan_hari DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_pengeluaran_hari DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_saldo_akhir DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_jumlah_nota INT DEFAULT 0;

    
    SELECT MAX(`tanggal`) INTO v_tgl_jangkar
    FROM `saldo_harian`
    WHERE `tanggal` <= p_tanggal;

    IF v_tgl_jangkar IS NULL THEN
        SET v_saldo_awal = 0.00;
        SET v_tgl_jangkar = '1970-01-01';
    ELSE
        SELECT `saldo_awal` INTO v_saldo_awal
        FROM `saldo_harian`
        WHERE `tanggal` = v_tgl_jangkar;
    END IF;

    
    IF v_tgl_jangkar < p_tanggal THEN
        SELECT COALESCE(SUM(`total_bayar`), 0.00) INTO v_pemasukan_sebelumnya
        FROM `daftar_penjualan`
        WHERE `tanggal_transaksi` >= v_tgl_jangkar
          AND `tanggal_transaksi` < p_tanggal;

        SELECT COALESCE(SUM(`total_bayar`), 0.00) INTO v_pengeluaran_sebelumnya
        FROM `daftar_pembelian`
        WHERE `tanggal_transaksi` >= v_tgl_jangkar
          AND `tanggal_transaksi` < p_tanggal;
    END IF;

    SET v_saldo_awal_hari = v_saldo_awal + v_pemasukan_sebelumnya - v_pengeluaran_sebelumnya;

    
    IF p_id_kasir IS NOT NULL THEN
        SELECT
            COALESCE(SUM(`total_bayar`), 0.00),
            COUNT(*)
        INTO v_pemasukan_hari, v_jumlah_nota
        FROM `daftar_penjualan`
        WHERE `tanggal_transaksi` = p_tanggal
          AND `id_kasir` = p_id_kasir;

        SELECT
            NULL AS `saldo_awal_hari`,
            v_pemasukan_hari AS `pemasukan`,
            NULL AS `pengeluaran`,
            NULL AS `saldo_akhir`,
            v_jumlah_nota AS `jumlah_nota`;
    ELSE
        SELECT
            COALESCE(SUM(`total_bayar`), 0.00),
            COUNT(*)
        INTO v_pemasukan_hari, v_jumlah_nota
        FROM `daftar_penjualan`
        WHERE `tanggal_transaksi` = p_tanggal;

        SELECT COALESCE(SUM(`total_bayar`), 0.00) INTO v_pengeluaran_hari
        FROM `daftar_pembelian`
        WHERE `tanggal_transaksi` = p_tanggal;

        SET v_saldo_akhir = v_saldo_awal_hari + v_pemasukan_hari - v_pengeluaran_hari;

        SELECT
            v_saldo_awal_hari AS `saldo_awal_hari`,
            v_pemasukan_hari AS `pemasukan`,
            v_pengeluaran_hari AS `pengeluaran`,
            v_saldo_akhir AS `saldo_akhir`,
            v_jumlah_nota AS `jumlah_nota`;
    END IF;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_setting` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_setting`(
    IN `p_kunci` VARCHAR(50)
)
BEGIN
    SELECT *
    FROM `pengaturan_notifikasi`
    WHERE `kunci` = p_kunci;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_item_kritis_dari_nota` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_item_kritis_dari_nota`(
    IN `p_kode_nota` VARCHAR(20)
)
BEGIN
    SELECT DISTINCT
        di.`kode_item`,
        di.`nama_item`,
        di.`stok`,
        di.`batas_minimum`,
        CASE
            WHEN di.`stok` <= 0 THEN 'HABIS'
            ELSE 'MENIPIS'
        END AS `status_stok`
    FROM `daftar_transaksi` dt
    JOIN `daftar_item` di ON dt.`kode_item` = di.`kode_item`
    WHERE (dt.`kode_penjualan` = p_kode_nota OR dt.`kode_pembelian` = p_kode_nota)
      AND di.`stok` <= di.`batas_minimum`;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_reset_password` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reset_password`(
    IN `p_id` INT,
    IN `p_password_hash` VARCHAR(255),
    IN `p_pelaku` INT
)
BEGIN
    DECLARE v_username VARCHAR(50);

    SELECT `username` INTO v_username
    FROM `pengguna`
    WHERE `id_pengguna` = p_id;

    IF v_username IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengguna tidak ditemukan!';
    END IF;

    UPDATE `pengguna`
    SET `password_hash` = p_password_hash,
        `diubah_pada` = NOW()
    WHERE `id_pengguna` = p_id;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`
    ) VALUES (
        NOW(),
        CONCAT('Reset password pengguna: ', v_username),
        'RESET_PASSWORD',
        p_pelaku
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_retur_pembelian` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_retur_pembelian`(
    IN `p_nota` VARCHAR(20),
    IN `p_item` VARCHAR(20),
    IN `p_qty` INT
)
BEGIN
    DECLARE v_total_bought INT DEFAULT 0;
    DECLARE v_already_returned INT DEFAULT 0;
    DECLARE v_stok INT;

    IF p_qty IS NULL OR p_qty <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah retur harus lebih dari 0!';
    END IF;

    SELECT
        COALESCE(SUM(`item_masuk`), 0),
        COALESCE(SUM(`retur_pembelian`), 0)
    INTO v_total_bought, v_already_returned
    FROM `daftar_transaksi`
    WHERE `kode_pembelian` = p_nota AND `kode_item` = p_item;

    IF (p_qty + v_already_returned) > v_total_bought THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah retur melebihi jumlah yang dibeli!';
    END IF;

    SELECT `stok` INTO v_stok
    FROM `daftar_item`
    WHERE `kode_item` = p_item;

    IF v_stok IS NULL OR v_stok < p_qty THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak cukup untuk retur pembelian!';
    END IF;

    INSERT INTO `daftar_transaksi` (
        `kode_pembelian`, `kode_item`, `retur_pembelian`
    ) VALUES (
        p_nota, p_item, p_qty
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_retur_penjualan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_retur_penjualan`(
    IN `p_nota` VARCHAR(20),
    IN `p_item` VARCHAR(20),
    IN `p_qty` INT
)
BEGIN
    DECLARE v_total_sold INT DEFAULT 0;
    DECLARE v_already_returned INT DEFAULT 0;

    IF p_qty IS NULL OR p_qty <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah retur harus lebih dari 0!';
    END IF;

    SELECT
        COALESCE(SUM(`item_keluar`), 0),
        COALESCE(SUM(`retur_penjualan`), 0)
    INTO v_total_sold, v_already_returned
    FROM `daftar_transaksi`
    WHERE `kode_penjualan` = p_nota AND `kode_item` = p_item;

    IF (p_qty + v_already_returned) > v_total_sold THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah retur melebihi jumlah yang terjual!';
    END IF;

    INSERT INTO `daftar_transaksi` (
        `kode_penjualan`, `kode_item`, `retur_penjualan`
    ) VALUES (
        p_nota, p_item, p_qty
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_ringkasan_dashboard` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ringkasan_dashboard`(
    IN `p_tanggal` DATE
)
BEGIN
    SELECT
        (SELECT COALESCE(SUM(`total_bayar`), 0.00)
         FROM `daftar_penjualan`
         WHERE `tanggal_transaksi` = p_tanggal) AS `total_penjualan`,

        (SELECT COUNT(*)
         FROM `daftar_penjualan`
         WHERE `tanggal_transaksi` = p_tanggal) AS `jumlah_nota`,

        (SELECT COUNT(*)
         FROM `daftar_item`
         WHERE `stok` <= `batas_minimum`
           AND (`tipe_item` != 'JASA' OR `tipe_item` IS NULL)) AS `jumlah_item_kritis`,

        (SELECT MAX(`waktu`)
         FROM `riwayat_backup`
         WHERE `status` = 'BERHASIL') AS `backup_terakhir`;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_set_batas_minimum` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_set_batas_minimum`(IN p_kode VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, IN p_nilai INT, IN p_pelaku INT)
BEGIN UPDATE daftar_item SET batas_minimum = p_nilai WHERE kode_item = p_kode; INSERT INTO log_aktivitas (waktu, tipe_aksi, keterangan) VALUES (NOW(), 'STOK', CONCAT('Mengubah batas minimum item ', p_kode, ' menjadi ', p_nilai)); END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_set_batas_minimum_semua` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_set_batas_minimum_semua`(
    IN `p_nilai` INT,
    IN `p_pelaku` INT
)
BEGIN
    IF p_nilai < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Batas minimum tidak boleh negatif!';
    END IF;

    UPDATE `daftar_item`
    SET `batas_minimum` = p_nilai,
        `diubah_pada` = NOW()
    WHERE `tipe_item` != 'JASA' OR `tipe_item` IS NULL;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Set batas minimum semua item menjadi ', p_nilai),
        'BATAS_STOK',
        p_pelaku,
        NULL,
        JSON_OBJECT('batas_minimum_semua', p_nilai)
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_set_saldo_awal` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_set_saldo_awal`(
    IN `p_tanggal` DATE,
    IN `p_nilai` DECIMAL(15,2),
    IN `p_pelaku` INT
)
BEGIN
    INSERT INTO `saldo_harian` (
        `tanggal`, `saldo_awal`, `diatur_oleh`, `dibuat_pada`
    ) VALUES (
        p_tanggal, p_nilai, p_pelaku, NOW()
    ) ON DUPLICATE KEY UPDATE
        `saldo_awal` = p_nilai,
        `diatur_oleh` = p_pelaku;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Atur saldo awal tanggal ', p_tanggal, ' sebesar ', p_nilai),
        'SALDO_DIATUR',
        p_pelaku,
        NULL,
        JSON_OBJECT('tanggal', p_tanggal, 'saldo_awal', p_nilai)
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_set_setting` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_set_setting`(
    IN `p_kunci` VARCHAR(50),
    IN `p_nilai` VARCHAR(255),
    IN `p_pelaku` INT
)
BEGIN
    DECLARE v_old_nilai VARCHAR(255);

    SELECT `nilai` INTO v_old_nilai
    FROM `pengaturan_notifikasi`
    WHERE `kunci` = p_kunci;

    INSERT INTO `pengaturan_notifikasi` (`kunci`, `nilai`)
    VALUES (p_kunci, p_nilai)
    ON DUPLICATE KEY UPDATE `nilai` = p_nilai;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Ubah pengaturan: ', p_kunci),
        'SETTING_DIUBAH',
        p_pelaku,
        IF(v_old_nilai IS NOT NULL, JSON_OBJECT('nilai', v_old_nilai), NULL),
        JSON_OBJECT('nilai', p_nilai)
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_set_status_pengguna` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_set_status_pengguna`(IN p_id INT, IN p_status ENUM('AKTIF','NONAKTIF'), IN p_pelaku INT)
BEGIN DECLARE v_old_status VARCHAR(20); DECLARE v_username VARCHAR(50); SELECT status, username INTO v_old_status, v_username FROM pengguna WHERE id_pengguna = p_id; IF v_username IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengguna tidak ditemukan!'; END IF; IF p_status = 'NONAKTIF' THEN IF p_id = p_pelaku THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tidak bisa menonaktifkan akun sendiri!'; END IF; IF p_id = 1 OR LOWER(v_username) = 'admin' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akun Administrator Pemilik Toko utama tidak dapat dinonaktifkan!'; END IF; END IF; UPDATE pengguna SET status = p_status, diubah_pada = NOW() WHERE id_pengguna = p_id; INSERT INTO log_aktivitas (waktu, keterangan, tipe_aksi, id_pengguna, data_sebelum, data_sesudah) VALUES (NOW(), CONCAT('Ubah status pengguna: ', v_username, ' menjadi ', p_status), 'STATUS_PENGGUNA', p_pelaku, JSON_OBJECT('status', v_old_status), JSON_OBJECT('status', p_status)); END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_tambah_item_pembelian` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tambah_item_pembelian`(
    IN `p_nota` VARCHAR(20),
    IN `p_item` VARCHAR(20),
    IN `p_qty` INT
)
BEGIN
    INSERT INTO `daftar_transaksi` (`kode_pembelian`, `kode_item`, `item_masuk`)
    VALUES (p_nota, p_item, p_qty);
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_tambah_item_penjualan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tambah_item_penjualan`(
    IN `p_nota` VARCHAR(20),
    IN `p_item` VARCHAR(20),
    IN `p_qty` INT
)
BEGIN
    INSERT INTO `daftar_transaksi` (`kode_penjualan`, `kode_item`, `item_keluar`)
    VALUES (p_nota, p_item, p_qty);
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_tandai_dibaca` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tandai_dibaca`(
    IN `p_id_pengguna` INT,
    IN `p_id_log` INT
)
BEGIN
    IF p_id_log IS NULL THEN
        INSERT INTO `notifikasi_dibaca` (`id_log`, `id_pengguna`, `dibaca_pada`)
        SELECT `id_log`, p_id_pengguna, NOW()
        FROM `v_info_barang`
        WHERE `id_log` NOT IN (
            SELECT `id_log`
            FROM `notifikasi_dibaca`
            WHERE `id_pengguna` = p_id_pengguna
        );
    ELSE
        INSERT IGNORE INTO `notifikasi_dibaca` (`id_log`, `id_pengguna`, `dibaca_pada`)
        VALUES (p_id_log, p_id_pengguna, NOW());
    END IF;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_update_harga` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_harga`(
    IN `p_kode` VARCHAR(20),
    IN `p_h_pokok` DECIMAL(15,2),
    IN `p_h_jual` DECIMAL(15,2),
    IN `p_id_pengguna` INT
)
BEGIN
    DECLARE v_old_h_pokok DECIMAL(15,2);
    DECLARE v_old_h_jual DECIMAL(15,2);
    DECLARE v_nama VARCHAR(100);

    SELECT `harga_pokok`, `harga_jual`, `nama_item`
    INTO v_old_h_pokok, v_old_h_jual, v_nama
    FROM `daftar_item`
    WHERE `kode_item` = p_kode;

    IF v_old_h_pokok IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Item tidak ditemukan!';
    END IF;

    UPDATE `daftar_item`
    SET `harga_pokok` = p_h_pokok,
        `harga_jual` = p_h_jual,
        `diubah_pada` = NOW()
    WHERE `kode_item` = p_kode;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Ubah harga: ', p_kode, ' - ', v_nama),
        'HARGA_DIUBAH',
        p_id_pengguna,
        JSON_OBJECT('harga_pokok', v_old_h_pokok, 'harga_jual', v_old_h_jual),
        JSON_OBJECT('harga_pokok', p_h_pokok, 'harga_jual', p_h_jual)
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_update_item` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_item`(
    IN `p_kode` VARCHAR(20),
    IN `p_nama` VARCHAR(100),
    IN `p_jenis` VARCHAR(50),
    IN `p_merek` VARCHAR(50),
    IN `p_id_pengguna` INT
)
BEGIN
    DECLARE v_old_nama VARCHAR(100);
    DECLARE v_old_jenis VARCHAR(50);
    DECLARE v_old_merek VARCHAR(50);

    SELECT `nama_item`, `jenis`, `merek`
    INTO v_old_nama, v_old_jenis, v_old_merek
    FROM `daftar_item`
    WHERE `kode_item` = p_kode;

    IF v_old_nama IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Item tidak ditemukan!';
    END IF;

    UPDATE `daftar_item`
    SET `nama_item` = p_nama,
        `jenis` = p_jenis,
        `merek` = p_merek,
        `diubah_pada` = NOW()
    WHERE `kode_item` = p_kode;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Item diubah: ', p_kode, ' - ', p_nama),
        'ITEM_DIUBAH',
        p_id_pengguna,
        JSON_OBJECT('nama', v_old_nama, 'jenis', v_old_jenis, 'merek', v_old_merek),
        JSON_OBJECT('nama', p_nama, 'jenis', p_jenis, 'merek', p_merek)
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_update_item_v2` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_item_v2`(
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
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_update_pengguna` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_pengguna`(
    IN `p_id` INT,
    IN `p_nama` VARCHAR(100),
    IN `p_username` VARCHAR(50),
    IN `p_role` ENUM('ADMIN','KASIR'),
    IN `p_pelaku` INT
)
BEGIN
    DECLARE v_old_nama VARCHAR(100);
    DECLARE v_old_username VARCHAR(50);
    DECLARE v_old_role VARCHAR(20);

    SELECT `nama`, `username`, `role`
    INTO v_old_nama, v_old_username, v_old_role
    FROM `pengguna`
    WHERE `id_pengguna` = p_id;

    IF v_old_nama IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengguna tidak ditemukan!';
    END IF;

    IF EXISTS (SELECT 1 FROM `pengguna` WHERE `username` = p_username AND `id_pengguna` != p_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Username sudah dipakai!';
    END IF;

    UPDATE `pengguna`
    SET `nama` = p_nama,
        `username` = p_username,
        `role` = p_role,
        `diubah_pada` = NOW()
    WHERE `id_pengguna` = p_id;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Update pengguna: ', p_username),
        'PENGGUNA_DIUBAH',
        p_pelaku,
        JSON_OBJECT('nama', v_old_nama, 'username', v_old_username, 'role', v_old_role),
        JSON_OBJECT('nama', p_nama, 'username', p_username, 'role', p_role)
    );
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_update_status_notifikasi` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_status_notifikasi`(
    IN `p_id` INT,
    IN `p_status` VARCHAR(50),
    IN `p_pesan_error` TEXT
)
BEGIN
    UPDATE `log_notifikasi`
    SET `status` = p_status,
        `pesan_error` = p_pesan_error
    WHERE `id_notif` = p_id;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_verifikasi_otp` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_verifikasi_otp`(
    IN `p_id_pengguna` INT,
    IN `p_kode_hash` VARCHAR(255)
)
BEGIN
    DECLARE v_id_otp INT;
    DECLARE v_kadaluarsa DATETIME;
    DECLARE v_percobaan INT;
    DECLARE v_kode_hash VARCHAR(255);

    SELECT `id_otp`, `kadaluarsa_pada`, `percobaan`, `kode_hash`
    INTO v_id_otp, v_kadaluarsa, v_percobaan, v_kode_hash
    FROM `otp_2fa`
    WHERE `id_pengguna` = p_id_pengguna AND `digunakan` = 0
    ORDER BY `dibuat_pada` DESC, `id_otp` DESC
    LIMIT 1;

    IF v_id_otp IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kode tidak valid';
    END IF;

    IF v_kadaluarsa < NOW() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kode kedaluwarsa';
    END IF;

    IF v_percobaan >= 5 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Terlalu banyak percobaan';
    END IF;

    IF v_kode_hash != p_kode_hash THEN
        UPDATE `otp_2fa`
        SET `percobaan` = `percobaan` + 1
        WHERE `id_otp` = v_id_otp;

        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kode tidak valid';
    END IF;

    UPDATE `otp_2fa`
    SET `digunakan` = 1
    WHERE `id_otp` = v_id_otp;

    SELECT `role`
    FROM `pengguna`
    WHERE `id_pengguna` = p_id_pengguna;
END
;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `v_barang_terlaris`
--

/*!50001 DROP VIEW IF EXISTS `v_barang_terlaris`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_uca1400_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_barang_terlaris` AS select `di`.`kode_item` AS `kode_item`,`di`.`nama_item` AS `nama_item`,sum(coalesce(`dt`.`item_keluar`,0) - coalesce(`dt`.`retur_penjualan`,0)) AS `total_terjual` from (`daftar_transaksi` `dt` join `daftar_item` `di` on(`dt`.`kode_item` = `di`.`kode_item`)) where `dt`.`kode_penjualan` is not null group by `di`.`kode_item`,`di`.`nama_item` order by sum(coalesce(`dt`.`item_keluar`,0) - coalesce(`dt`.`retur_penjualan`,0)) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_info_barang`
--

/*!50001 DROP VIEW IF EXISTS `v_info_barang`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_uca1400_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_info_barang` AS select `log_aktivitas`.`id_log` AS `id_log`,`log_aktivitas`.`waktu` AS `waktu`,`log_aktivitas`.`tipe_aksi` AS `tipe_aksi`,`log_aktivitas`.`keterangan` AS `keterangan`,`log_aktivitas`.`data_sebelum` AS `data_sebelum`,`log_aktivitas`.`data_sesudah` AS `data_sesudah`,`log_aktivitas`.`id_pengguna` AS `id_pengguna` from `log_aktivitas` where `log_aktivitas`.`tipe_aksi` in ('ITEM_BARU','ITEM_DIUBAH','HARGA_DIUBAH') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_katalog_publik`
--

/*!50001 DROP VIEW IF EXISTS `v_katalog_publik`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_uca1400_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_katalog_publik` AS select `daftar_item`.`kode_item` AS `kode_item`,`daftar_item`.`nama_item` AS `nama_item`,`daftar_item`.`jenis` AS `jenis`,`daftar_item`.`merek` AS `merek`,`daftar_item`.`satuan` AS `satuan`,`daftar_item`.`harga_jual` AS `harga_jual`,case when `daftar_item`.`tipe_item` = 'JASA' then NULL else `daftar_item`.`stok` end AS `stok_tampil`,case when `daftar_item`.`tipe_item` = 'JASA' then 'TERSEDIA' when `daftar_item`.`stok` <= 0 then 'HABIS' when `daftar_item`.`stok` <= coalesce(`daftar_item`.`batas_minimum`,5) then 'MENIPIS' when `daftar_item`.`stok` <= 15 then 'NORMAL' else 'AMAN' end AS `status_stok`,`daftar_item`.`keterangan` AS `keterangan` from `daftar_item` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_stok_item`
--

/*!50001 DROP VIEW IF EXISTS `v_stok_item`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_uca1400_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_stok_item` AS select `daftar_item`.`kode_item` AS `kode_item`,`daftar_item`.`nama_item` AS `nama_item`,`daftar_item`.`stok` AS `stok`,`daftar_item`.`satuan` AS `satuan`,`daftar_item`.`harga_pokok` AS `harga_pokok`,`daftar_item`.`harga_jual` AS `harga_jual`,`daftar_item`.`harga_jual` - `daftar_item`.`harga_pokok` AS `margin_untung`,case when `daftar_item`.`stok` <= 0 then 'HABIS' when `daftar_item`.`stok` <= coalesce(`daftar_item`.`batas_minimum`,5) then 'MENIPIS' when `daftar_item`.`stok` <= 15 then 'NORMAL' else 'AMAN' end AS `status_stok`,`daftar_item`.`jenis` AS `jenis`,`daftar_item`.`merek` AS `merek`,`daftar_item`.`rak` AS `rak`,`daftar_item`.`tipe_item` AS `tipe_item`,`daftar_item`.`batas_minimum` AS `batas_minimum` from `daftar_item` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_stok_menipis`
--

/*!50001 DROP VIEW IF EXISTS `v_stok_menipis`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_uca1400_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_stok_menipis` AS select `v_stok_item`.`kode_item` AS `kode_item`,`v_stok_item`.`nama_item` AS `nama_item`,`v_stok_item`.`stok` AS `stok`,`v_stok_item`.`satuan` AS `satuan`,`v_stok_item`.`harga_pokok` AS `harga_pokok`,`v_stok_item`.`harga_jual` AS `harga_jual`,`v_stok_item`.`margin_untung` AS `margin_untung`,`v_stok_item`.`status_stok` AS `status_stok`,`v_stok_item`.`jenis` AS `jenis`,`v_stok_item`.`merek` AS `merek`,`v_stok_item`.`rak` AS `rak`,`v_stok_item`.`tipe_item` AS `tipe_item`,`v_stok_item`.`batas_minimum` AS `batas_minimum` from `v_stok_item` where `v_stok_item`.`status_stok` in ('HABIS','MENIPIS') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-11-09  8:23:55

