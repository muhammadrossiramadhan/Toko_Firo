<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Batas Stok Minimum - Toko Firo</title>

    {{-- Tailwind CSS CDN untuk preview mandiri tanpa build step, fallback jika Vite belum dijalankan --}}
    <script src="https://cdn.tailwindcss.com"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        .touch-target {
            min-height: 2.75rem;
            min-width: 2.75rem;
        }
    </style>

    @include('partials.skala')
</head>
<body class="bg-gray-100 text-gray-800 text-base antialiased min-h-screen flex">

    {{-- Notifikasi Prototype --}}
    <div id="notif-sukses" class="hidden fixed top-6 right-8 bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg text-base font-medium z-50 transition-opacity">
        Perubahan batas minimum berhasil disimpan (prototype).
    </div>

    {{-- Sidebar Navigasi (Sesuai Standar UI Toko Firo) --}}
    <aside class="w-64 bg-[#08331e] text-white flex flex-col shrink-0 min-h-screen p-4 justify-between">
        <div class="space-y-6">
            {{-- Brand Card Toko Firo (Tanpa Ikon) --}}
            <div class="bg-white rounded-xl p-3 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-600 text-white font-bold flex items-center justify-center text-lg">
                    F
                </div>
                <div class="font-bold text-gray-900 text-lg">
                    Toko Firo
                </div>
            </div>

            {{-- Menu Admin --}}
            <div>
                <div class="text-xs uppercase tracking-wider text-emerald-300 font-semibold px-3 mb-2">
                    Menu Admin
                </div>
                <nav class="space-y-1">
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Dashboard
                    </a>
                    <a href="{{ Route::has('admin.laporan.penjualan') ? route('admin.laporan.penjualan') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Laporan Penjualan
                    </a>
                    <a href="{{ Route::has('admin.barang') ? route('admin.barang') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Kelola Data Barang
                    </a>
                    <a href="{{ Route::has('admin.pembelian') ? route('admin.pembelian') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Pembelian Barang
                    </a>
                    <a href="{{ Route::has('admin.akun') ? route('admin.akun') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Kelola Akun Pengguna
                    </a>
                    <a href="{{ Route::has('admin.batas-stok') ? route('admin.batas-stok') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold text-base shadow-sm">
                        Batas Stok Minimum
                    </a>
                    <a href="{{ Route::has('admin.backup') ? route('admin.backup') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Backup Data
                    </a>
                    <a href="{{ Route::has('admin.log') ? route('admin.log') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Log Aktivitas
                    </a>
                    <a href="{{ Route::has('admin.notifikasi') ? route('admin.notifikasi') : '#' }}" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Pengaturan Notifikasi
                    </a>
                </nav>
            </div>

            {{-- Akses Kasir --}}
            <div>
                <div class="text-xs uppercase tracking-wider text-emerald-300 font-semibold px-3 mb-2">
                    Akses Kasir
                </div>
                <nav class="space-y-1">
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Transaksi Penjualan
                    </a>
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Lihat Stok Barang
                    </a>
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Saldo Harian
                    </a>
                    <a href="#" class="touch-target flex items-center px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                        Info Barang Baru
                    </a>
                </nav>
            </div>
        </div>

        {{-- Profil Pengguna di Bawah Sidebar (Tanpa Avatar / Ikon) --}}
        <div class="pt-4 border-t border-emerald-900/60">
            <div class="bg-[#052314] rounded-xl p-3 mb-2">
                <div class="font-medium text-white text-base truncate">
                    {{ Auth::user()?->nama ?? 'Admin / Pemilik' }}
                </div>
                <div class="text-sm text-emerald-300">
                    Admin / Pemilik
                </div>
            </div>
            <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                @csrf
                <button type="submit" class="touch-target w-full text-left flex items-center px-3 py-2 rounded-lg text-red-300 hover:text-white hover:bg-red-900/40 text-base transition-colors">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Area Konten Utama --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0 pb-32">
        {{-- Header Halaman --}}
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Batas Stok Minimum
            </h1>
            <p class="text-slate-500 text-base mt-1">
                Atur batas peringatan stok untuk setiap barang. Notifikasi otomatis dikirim ke bot Telegram saat stok berada di bawah batas.
            </p>
        </header>

        {{-- Banner Telegram --}}
        <section class="bg-amber-50 border border-amber-200 text-amber-900 rounded-xl p-4 text-base mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <span class="font-bold">Pengingat Telegram Aktif:</span>
                Sistem akan otomatis mengirimkan pesan peringatan saat stok barang mencapai atau di bawah batas minimum yang ditentukan.
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-200/80 text-amber-900 shrink-0">
                Bot Terhubung
            </span>
        </section>

        {{-- Filter Bar & Quick Actions --}}
        <section class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm mb-6 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <input
                    type="text"
                    id="input-cari"
                    placeholder="Cari nama atau kode barang..."
                    onkeyup="filterTabel()"
                    class="touch-target w-full sm:w-72 px-4 py-2 text-base rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                >
                <select
                    id="filter-jenis"
                    onchange="filterTabel()"
                    class="touch-target px-4 py-2 text-base rounded-lg border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800"
                >
                    <option value="">Semua Jenis</option>
                    <option value="ATK">ATK</option>
                    <option value="Fotocopy">Fotocopy</option>
                </select>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="text-base font-medium text-gray-600 mr-1">Aksi Cepat:</span>
                <button
                    type="button"
                    onclick="aturSemua(3)"
                    class="touch-target px-4 py-2.5 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium text-base shadow-sm transition-colors cursor-pointer"
                >
                    Atur semua ke 3 pcs
                </button>
                <button
                    type="button"
                    onclick="aturSemua(5)"
                    class="touch-target px-4 py-2.5 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium text-base shadow-sm transition-colors cursor-pointer"
                >
                    Atur semua ke 5 pcs
                </button>
            </div>
        </section>

        {{-- Tabel Batas Stok --}}
        <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-base" id="tabel-batas-stok">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase font-semibold text-gray-600 tracking-wider">
                            <th class="px-6 py-4">KODE BARANG</th>
                            <th class="px-6 py-4">NAMA BARANG</th>
                            <th class="px-6 py-4">JENIS</th>
                            <th class="px-6 py-4">STOK SEKARANG</th>
                            <th class="px-6 py-4">BATAS MINIMUM</th>
                            <th class="px-6 py-4">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-700">
                        @foreach ($daftarBatasStok as $index => $item)
                            <tr class="hover:bg-gray-50/80 transition-colors row-barang" data-jenis="{{ $item->jenis }}">
                                <td class="px-6 py-4 font-mono font-medium text-gray-700 kol-kode">
                                    {{ $item->kode }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 kol-nama">
                                    {{ $item->nama }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 kol-jenis">
                                    {{ $item->jenis }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $item->stok }} {{ $item->satuan }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="inline-flex items-center">
                                        <button
                                            type="button"
                                            onclick="kurangiBatas(this)"
                                            class="touch-target min-w-[2.75rem] min-h-[2.75rem] flex items-center justify-center bg-gray-100 border border-gray-300 rounded-l-lg hover:bg-gray-200 text-lg font-bold text-gray-700 select-none cursor-pointer"
                                            aria-label="Kurangi batas minimum"
                                        >
                                            -
                                        </button>
                                        <input
                                            type="number"
                                            min="0"
                                            value="{{ $item->batas }}"
                                            data-awal="{{ $item->batas }}"
                                            oninput="hitungPerubahan()"
                                            class="input-batas w-16 min-h-[2.75rem] text-center border-y border-gray-300 text-base font-semibold text-gray-800 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                        >
                                        <button
                                            type="button"
                                            onclick="tambahBatas(this)"
                                            class="touch-target min-w-[2.75rem] min-h-[2.75rem] flex items-center justify-center bg-gray-100 border border-gray-300 rounded-r-lg hover:bg-gray-200 text-lg font-bold text-gray-700 select-none cursor-pointer"
                                            aria-label="Tambah batas minimum"
                                        >
                                            +
                                        </button>
                                        <span class="ml-2 text-gray-500 text-base">
                                            {{ $item->satuan }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($item->status === 'Habis')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-700">
                                            Habis
                                        </span>
                                    @elseif ($item->status === 'Menipis')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-amber-100 text-amber-800">
                                            Menipis
                                        </span>
                                    @elseif ($item->status === 'Normal')
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-700">
                                            Normal
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                            Aman
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Floating Bottom Bar (Bilah Simpan Mengambang Sesuai Figma P14) --}}
        <div id="floating-bar" class="fixed bottom-6 left-72 right-8 bg-white border border-gray-300 shadow-2xl rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 z-30 transition-all">
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 font-bold text-sm rounded-md uppercase tracking-wider">
                    Pemberitahuan
                </span>
                <span id="label-perubahan" class="text-base font-medium text-gray-800">
                    2 perubahan batas minimum belum disimpan.
                </span>
            </div>
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    id="btn-batalkan"
                    onclick="batalkanPerubahan()"
                    class="touch-target px-5 py-2.5 rounded-lg border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 font-medium text-base shadow-sm transition-colors cursor-pointer"
                >
                    Batalkan
                </button>
                <button
                    type="button"
                    id="btn-simpan"
                    onclick="simpanPerubahan()"
                    class="touch-target px-6 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-base shadow-sm transition-colors cursor-pointer"
                >
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </main>

    {{-- Script Vanilla JavaScript (Tanpa Library Eksternal) --}}
    <script>
        function kurangiBatas(btn) {
            const input = btn.parentElement.querySelector('.input-batas');
            let val = parseInt(input.value) || 0;
            if (val > 0) {
                input.value = val - 1;
                hitungPerubahan();
            }
        }

        function tambahBatas(btn) {
            const input = btn.parentElement.querySelector('.input-batas');
            let val = parseInt(input.value) || 0;
            input.value = val + 1;
            hitungPerubahan();
        }

        function aturSemua(angka) {
            const inputs = document.querySelectorAll('.input-batas');
            inputs.forEach(input => {
                input.value = angka;
            });
            hitungPerubahan();
        }

        function hitungPerubahan() {
            const inputs = document.querySelectorAll('.input-batas');
            let jumlahPerubahan = 0;

            inputs.forEach(input => {
                const awal = parseInt(input.getAttribute('data-awal')) || 0;
                const sekarang = parseInt(input.value) || 0;
                if (awal !== sekarang) {
                    jumlahPerubahan++;
                }
            });

            const label = document.getElementById('label-perubahan');
            const floatingBar = document.getElementById('floating-bar');

            if (jumlahPerubahan > 0) {
                label.textContent = jumlahPerubahan + ' perubahan batas minimum belum disimpan.';
                floatingBar.classList.remove('opacity-60');
            } else {
                label.textContent = 'Semua perubahan batas minimum sudah tersimpan.';
            }
        }

        function batalkanPerubahan() {
            const inputs = document.querySelectorAll('.input-batas');
            inputs.forEach(input => {
                input.value = input.getAttribute('data-awal') || 0;
            });
            hitungPerubahan();
        }

        function simpanPerubahan() {
            const inputs = document.querySelectorAll('.input-batas');
            inputs.forEach(input => {
                input.setAttribute('data-awal', input.value);
            });
            hitungPerubahan();

            const notif = document.getElementById('notif-sukses');
            notif.classList.remove('hidden');
            setTimeout(() => {
                notif.classList.add('hidden');
            }, 3000);
        }

        function filterTabel() {
            const kataKunci = (document.getElementById('input-cari').value || '').toLowerCase();
            const filterJenis = (document.getElementById('filter-jenis').value || '').toLowerCase();
            const rows = document.querySelectorAll('.row-barang');

            rows.forEach(row => {
                const kode = row.querySelector('.kol-kode').textContent.toLowerCase();
                const nama = row.querySelector('.kol-nama').textContent.toLowerCase();
                const jenis = row.getAttribute('data-jenis').toLowerCase();

                const cocokTeks = kode.includes(kataKunci) || nama.includes(kataKunci);
                const cocokJenis = (filterJenis === '') || (jenis === filterJenis);

                if (cocokTeks && cocokJenis) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>

