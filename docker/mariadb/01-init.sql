-- Inisialisasi Database Toko Firo & Testing DB
CREATE DATABASE IF NOT EXISTS `toko_firo` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS `toko_firo_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Buat user aplikasi dan grant permissions
CREATE USER IF NOT EXISTS 'toko_firo'@'%' IDENTIFIED BY 'root';
GRANT ALL PRIVILEGES ON `toko_firo`.* TO 'toko_firo'@'%';
GRANT ALL PRIVILEGES ON `toko_firo_test`.* TO 'toko_firo'@'%';

CREATE USER IF NOT EXISTS 'laravel'@'%' IDENTIFIED BY 'laravel';
GRANT ALL PRIVILEGES ON `toko_firo`.* TO 'laravel'@'%';
GRANT ALL PRIVILEGES ON `toko_firo_test`.* TO 'laravel'@'%';

GRANT ALL PRIVILEGES ON `toko_firo`.* TO 'root'@'%';
GRANT ALL PRIVILEGES ON `toko_firo_test`.* TO 'root'@'%';

FLUSH PRIVILEGES;
