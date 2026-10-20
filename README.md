# Toko Firo - Modul Kelola Akun Pengguna (Branch: `Rossi/User-Settings`)

Dokumentasi implementasi antarmuka (*frontend prototype*) untuk modul **Kelola Akun Pengguna** sistem Toko Firo, mengacu pada rancangan desain antarmuka **Figma Frame P13 (Desktop / Default)**.

---

## 📌 Ringkasan Modul
Modul ini berfungsi sebagai sarana administrasi bagi Pemilik/Admin untuk melihat daftar pengguna, status keaktifan akun, riwayat login terakhir, serta memicu tindakan pengelolaan akun (Ubah Akun, Reset Password, Nonaktifkan/Aktifkan Akun).

Pada branch `Rossi/User-Settings`, pengerjaan difokuskan pada pematangan **Frontend UI/UX Prototype** berbasis *mock data* terstruktur sebelum dihubungkan dengan transaksi backend database.

---

## 🚀 Akses & Uji Coba Cepat

### 1. Prasyarat Lingkungan
- PHP $\ge$ 8.2 (FlyEnv / PHP CLI)
- Node.js & NPM (untuk Tailwind CSS)
- Web Server Laravel Artisan

### 2. Menjalankan Server Lokal
Pastikan branch aktif adalah `Rossi/User-Settings`:
```bash
git checkout Rossi/User-Settings
```

Jalankan server aplikasi:
```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Vite Dev Server (opsional, jika Tailwind dikompilasi live)
npm run dev
```

### 3. URL Akses Langsung
Buka peramban pada alamat rute mandiri:
```text
http://127.0.0.1:8000/admin/pengguna
```

---

## 🎨 Komponen Antarmuka & Kepatuhan UI/UX

Sesuai aturan panduan desain proyek (**K12** dan **K18**):

1. **Kepatuhan K12 (Bebas SVG & Aksesibilitas)**:
   - Bersih dari tag `<svg>`, ikon grafis berbasis vektor, maupun emoji.
   - Kolom pencarian disajikan menggunakan *input form* teks bersih.
   - Ukuran teks utama $\ge 16\text{px}$ (`text-base`).
   - Seluruh elemen interaktif dan tombol aksi memiliki tinggi area sentuh minimal $44\text{px}$ (`min-h-[2.75rem]` / `44px`).
2. **Kepatuhan K18 (Skala Tampilan 80%)**:
   - Memuat parsial `@include('partials.skala')` (`--skala: 80%`) sehingga proporsi tampilan pada browser zoom 100% identik dengan skala Figma 100%.
3. **Data Antarmuka (Figma P13 Mock Data)**:
   - **Junaidi Saleh** (`junaidi`) — Role: Admin (badge hijau), Status: • Aktif, Login: `03 Okt 2026, 08:15`.
   - **Rina Putri** (`rina`) — Role: Kasir (badge biru), Status: • Aktif, Login: `03 Okt 2026, 07:30`.
   - **Siti Aminah** (`siti`) — Role: Kasir (badge biru), Status: • Nonaktif, Login: `28 Sep 2026, 14:00`.
4. **Navigasi Sidebar**:
   - Menu *Kelola Akun Pengguna* berstatus aktif (`bg-emerald-600 text-white`).
   - Kartu profil *Admin / Pemilik* dan tombol keluar terkunci rapi di dasar sidebar menggunakan kontainer `flex flex-col justify-between min-h-screen`.

---

## 🏗️ Isolasi Arsitektur

Modul ini diisolasi sepenuhnya agar tidak menimbulkan konflik merge terhadap branch tim lain (`staging`/`main`):
- **Berkas Rute Mandiri**: Didefinisikan di `routes/admin_pengguna.php` dan didaftarkan pada `bootstrap/app.php` dalam grup rute tambahan (`then:`). Tidak menyentuh `routes/web.php`.
- **Controller Mandiri**: Ditangani oleh `app/Http/Controllers/Admin/PenggunaController.php`.
- **Tampilan Terisolasi**: `resources/views/admin/pengguna/index.blade.php`.
- **Decoupling Autentikasi**: Middleware `auth` dan `role:ADMIN` dilepas sementara pada rute lokal untuk mempermudah peninjauan independen.

---

## 🧪 Pengujian Otomatis (Test Suite)

Pengujian fitur otomatis tersedia pada `tests/Feature/PenggunaAdminTest.php`:
```bash
php artisan test --filter=PenggunaAdminTest
```

**Cakupan Asersi Pengujian (4 passed, 16 assertions):**
- Status respons HTTP 200 pada endpoint `admin.pengguna`.
- Pemastian keberadaan teks pengguna Figma P13 dan tombol `+ Tambah Akun`.
- Verifikasi ketiadaan tag `<svg>` (K12).
- Verifikasi pemuatan parsial skala 80% (K18).
- Status aktif menu navigasi sidebar.

---

## 📝 Catatan untuk Peninjau (*Reviewer*)
1. Pendaftaran modal tambah akun, reset sandi, dan koneksi ke stored procedure/tabel `pengguna` database MariaDB disiapkan pada tahap integrasi backend berikutnya.
2. Aktifkan kembali middleware `auth` dan `role:ADMIN` pada `routes/admin_pengguna.php` sebelum modul digabungkan ke branch `staging`.