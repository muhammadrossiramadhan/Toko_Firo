# ATURAN AI (wajib untuk setiap tugas)
PRIORITAS: bila instruksi umum bertentangan, aturan di file ini menang. Ambigu skema atau
desain: TODO dan BERHENTI. Jangan menebak. Laporan maksimal 40 baris.
GIT: DILARANG checkout, switch, restore, reset, clean, stash, add, commit, push, merge,
rebase, dan menghapus file yang sudah ada. Pemilik yang menjalankan git.
RAHASIA: jangan baca atau tampilkan .env. Token dan password tidak boleh masuk kode, test,
log, atau laporan.
DATABASE: dilarang menulis ke database toko_firo (dev). Jangan jalankan file SQL, seed,
migrate, atau npm. Jangan koneksi PDO manual. Test memakai toko_firo_test dengan trait
DatabaseTransactions; RefreshDatabase dilarang. Jika MariaDB mati (2002) atau muncul error
1267 (collation): berhenti dan lapor, jangan memakai trik collation.
SKEMA: nama tabel, kolom, dan procedure dibaca dulu dari database/sql dan database/migrations.
KEAMANAN KODE: Query Builder atau stored procedure dengan binding parameter. Dilarang
menggabungkan input ke dalam SQL (juga di DB::raw, whereRaw, selectRaw, orderByRaw). Input
tanggal divalidasi ketat (format Y-m-d), opsi rentang dan urutan memakai daftar putih, jumlah
baris per halaman konstan (bukan dari input). Pesan error ke pengguna bahasa Indonesia
sederhana, tanpa detail exception, SQL, atau path. Data penjualan dan pengguna tidak ditulis
ke log. Operasi tulis tercatat di log_aktivitas lewat procedure.
TAMPILAN: K12 (tanpa svg, ikon, emoji; font minimal 1rem; tombol minimal 2.75rem) dan K18
(partial skala). View tidak diubah kecuali bug nyata; jika ada, lapor dulu.
TERLARANG DIUBAH kecuali tugas menyebut: routes/web.php, config/auth.php, bootstrap/app.php,
User.php, database/migrations, database/sql, LoginController, LogoutController, OtpController,
CheckRole, EnsurePending2fa, semua file Telegram, beranda, katalog, masuk, verifikasi,
layouts/, components/, public/, resources/css/app.css, partials/sidebar_admin, partials/skala,
semua file markdown selain ATURAN_AI.md. Hanya file yang disebut di tugas yang boleh dibuat
atau diubah.
SHELL: terminal adalah PowerShell. Perintah PHP: & "C:\Program Files\FlyEnv-Data\env\php\php.exe" artisan test
OUTPUT: jangan berasumsi perintah berhasil bila outputnya tidak terlihat. Tulis "output tidak
terlihat" di laporan. Dilarang menulis hasil test yang tidak benar-benar terlihat.
VERIFIKASI (output apa adanya): php artisan test (cukup baris FAILED dan baris Tests:);
git --no-pager status --short; git --no-pager diff --stat; tinker jumlah objek toko_firo
(harus 26).

