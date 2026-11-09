-- Inisialisasi Database Toko Firo & Testing DB
CREATE DATABASE IF NOT EXISTS `toko_firo` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS `toko_firo_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Buat user dan grant permissions
CREATE USER IF NOT EXISTS 'toko_firo'@'%' IDENTIFIED BY 'root';
GRANT ALL PRIVILEGES ON `toko_firo`.* TO 'toko_firo'@'%';
GRANT ALL PRIVILEGES ON `toko_firo_test`.* TO 'toko_firo'@'%';

GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY 'root' WITH GRANT OPTION;

FLUSH PRIVILEGES;
