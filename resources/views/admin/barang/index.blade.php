<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Data Barang - Toko Firo</title>

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
<body class="min-h-screen flex bg-slate-50 text-gray-800 text-base antialiased">

    {{-- Sidebar Navigasi Admin Terpusat (Kepatuhan K12 & K18) --}}
    @include('partials.sidebar_admin')

    {{-- Area Konten Utama --}}
    <main class="flex-1 min-w-0 p-8">
        {{-- Header Halaman (Tanpa Ikon Lonceng) --}}
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Kelola Data Barang
                </h1>
                <p class="text-slate-500 text-base mt-1">Daftar master barang dan status inventori stok Toko Firo.</p>
            </div>
            <div class="flex items-center gap-3">
                <a
                    href="{{ Route::has('admin.barang.riwayat') ? route('admin.barang.riwayat') : '#' }}"
                    class="touch-target px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-xl text-base shadow-sm transition-colors flex items-center justify-center whitespace-nowrap"
                >
                    Riwayat Transaksi
                </a>
                <button
                    type="button"
                    id="btn-tambah-barang"
                    onclick="bukaModalTambah()"
                    class="touch-target px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-base shadow-sm transition-colors whitespace-nowrap"
                >
                    Tambah Barang Baru
                </button>
            </div>
        </header>

        {{-- Kotak Pesan Sukses dan Pesan Error --}}
        @if (session('sukses'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-900 text-base font-medium">
                {{ session('sukses') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 rounded-xl bg-red-100 border border-red-300 text-red-900 text-base font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-red-100 border border-red-300 text-red-900 text-base">
                <div class="font-bold mb-1">Periksa kembali isian formulir:</div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Baris Pencarian, Filter Jenis, dan Urutan (Form GET) --}}
        <form method="GET" action="{{ route('admin.barang') }}" class="flex flex-col md:flex-row items-center gap-4 mb-4">
            <input
                type="text"
                name="q"
                value="{{ $q }}"
                placeholder="Cari nama atau kode barang..."
                class="touch-target flex-1 w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 placeholder-gray-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent"
            >
            <select
                name="jenis"
                class="touch-target bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm min-w-[9.375rem] focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent"
            >
                <option value="Semua" {{ $jenis === 'Semua' ? 'selected' : '' }}>Jenis: Semua</option>
                @foreach ($daftarJenis as $itemJenis)
                    <option value="{{ $itemJenis }}" {{ $jenis === $itemJenis ? 'selected' : '' }}>
                        {{ $itemJenis }}
                    </option>
                @endforeach
            </select>
            <select
                name="urut"
                class="touch-target bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm min-w-[10.625rem] focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent"
            >
                <option value="terbaru" {{ $urut === 'terbaru' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                <option value="nama" {{ $urut === 'nama' ? 'selected' : '' }}>Urutkan: Nama A-Z</option>
                <option value="stok" {{ $urut === 'stok' ? 'selected' : '' }}>Urutkan: Stok Terkecil</option>
            </select>
            <button
                type="submit"
                class="touch-target px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg text-base shadow-sm transition-colors"
            >
                Cari
            </button>
        </form>

        {{-- Kartu Tabel Utama --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Wrapper scroll horizontal untuk layar kecil --}}
            <div class="overflow-x-auto w-full">
                <table class="w-full border-collapse text-left text-base">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-700 text-base font-semibold">
                            <th scope="col" class="py-3 px-3 whitespace-nowrap">Kode</th>
                            <th scope="col" class="py-3 px-3">Nama Item</th>
                            <th scope="col" class="py-3 px-3 whitespace-nowrap">Jenis</th>
                            <th scope="col" class="py-3 px-3 whitespace-nowrap">Merek</th>
                            <th scope="col" class="py-3 px-3 whitespace-nowrap">Rak</th>
                            <th scope="col" class="py-3 px-3 whitespace-nowrap">Satuan</th>
                            <th scope="col" class="py-3 px-3 whitespace-nowrap">Harga Pokok</th>
                            <th scope="col" class="py-3 px-3 whitespace-nowrap">Harga Jual</th>
                            <th scope="col" class="py-3 px-3 whitespace-nowrap text-center">Stok</th>
                            <th scope="col" class="py-3 px-3 whitespace-nowrap">Status</th>
                            <th scope="col" class="py-3 px-3 whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-800">
                        @forelse ($items as $item)
                            <tr
                                data-kode="{{ $item->kode_item }}"
                                data-nama="{{ $item->nama_item }}"
                                data-jenis="{{ $item->jenis }}"
                                data-merek="{{ $item->merek }}"
                                data-rak="{{ $item->rak }}"
                                data-satuan="{{ $item->satuan }}"
                                data-tipe="{{ $item->tipe_item ?? 'BARANG' }}"
                                data-harga-pokok="{{ (int) $item->harga_pokok }}"
                                data-harga-jual="{{ (int) $item->harga_jual }}"
                                data-stok="{{ $item->stok }}"
                                data-keterangan="{{ $item->keterangan }}"
                                data-batas-minimum="{{ $item->batas_minimum ?? 5 }}"
                                class="baris-barang hover:bg-gray-50/70 transition-colors cursor-pointer"
                            >
                                <td class="py-3 px-3 font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $item->kode_item }}
                                </td>
                                <td class="py-3 px-3 text-gray-900 font-medium">
                                    {{ $item->nama_item }}
                                </td>
                                <td class="py-3 px-3 text-gray-700 whitespace-nowrap">
                                    {{ $item->jenis }}
                                </td>
                                <td class="py-3 px-3 text-gray-700 whitespace-nowrap">
                                    {{ $item->merek }}
                                </td>
                                <td class="py-3 px-3 text-gray-700 whitespace-nowrap">
                                    {{ $item->rak }}
                                </td>
                                <td class="py-3 px-3 text-gray-700 whitespace-nowrap">
                                    {{ $item->satuan }}
                                </td>
                                <td class="py-3 px-3 text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($item->harga_pokok, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-gray-900 whitespace-nowrap text-center">
                                    {{ $item->stok }}
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    @if ($item->status_stok === 'Aman')
                                        <span class="inline-block px-3 py-1 font-semibold text-emerald-800 bg-emerald-100 rounded-full text-base">
                                            Aman
                                        </span>
                                    @elseif ($item->status_stok === 'Menipis')
                                        <span class="inline-block px-3 py-1 font-semibold text-amber-800 bg-amber-100 rounded-full text-base">
                                            Menipis
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 font-semibold text-red-800 bg-red-100 rounded-full text-base">
                                            Habis
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap text-base">
                                    {{-- Tombol Aksi Teks (Tanpa Ikon) --}}
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            disabled
                                            onclick="bukaModalUbah(this)"
                                            class="touch-target px-2 text-gray-400 cursor-not-allowed text-base font-medium btn-aksi-baris btn-ubah-data"
                                        >
                                            Ubah Data
                                        </button>
                                        <button
                                            type="button"
                                            disabled
                                            onclick="bukaModalHarga(this)"
                                            class="touch-target px-2 text-gray-400 cursor-not-allowed text-base font-medium btn-aksi-baris btn-ubah-harga"
                                        >
                                            Ubah Harga
                                        </button>
                                        <button
                                            type="button"
                                            disabled
                                            onclick="bukaModalHapus(this)"
                                            class="touch-target px-2 text-gray-400 cursor-not-allowed text-base font-medium btn-aksi-baris btn-hapus-data"
                                        >
                                            Hapus Data
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-8 text-center text-gray-500 text-base">
                                    Belum ada data barang
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Paginasi --}}
            <div class="px-6 py-4 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                {{-- Informasi Data --}}
                <div class="text-base text-gray-600">
                    Menampilkan {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} barang
                </div>

                {{-- Tombol Navigasi Paginasi --}}
                {{ $items->links('admin.barang.pagination') }}
            </div>
        </div>
    </main>

    {{-- Include Modals --}}
    @include('admin.barang.modals')

    {{-- Interaktivitas JavaScript Murni --}}
    <script>
        let barisAktif = null;

        document.addEventListener('DOMContentLoaded', function () {
            const barisList = document.querySelectorAll('tr.baris-barang');

            barisList.forEach(function (baris) {
                baris.addEventListener('click', function (e) {
                    if (e.target.tagName.toLowerCase() === 'button' && !e.target.disabled) {
                        return;
                    }

                    // Reset baris lain
                    barisList.forEach(function (b) {
                        b.classList.remove('bg-emerald-50', 'ring-1', 'ring-emerald-500');
                        b.querySelectorAll('.btn-aksi-baris').forEach(function (btn) {
                            btn.disabled = true;
                            btn.classList.add('text-gray-400', 'cursor-not-allowed');
                            btn.classList.remove('text-emerald-700', 'text-amber-700', 'text-red-700', 'cursor-pointer', 'hover:underline');
                        });
                    });

                    // Sorot baris terpilih
                    baris.classList.add('bg-emerald-50', 'ring-1', 'ring-emerald-500');
                    barisAktif = baris;

                    // Aktifkan tombol pada baris terpilih
                    const btnUbah = baris.querySelector('.btn-ubah-data');
                    const btnHarga = baris.querySelector('.btn-ubah-harga');
                    const btnHapus = baris.querySelector('.btn-hapus-data');

                    if (btnUbah) {
                        btnUbah.disabled = false;
                        btnUbah.classList.remove('text-gray-400', 'cursor-not-allowed');
                        btnUbah.classList.add('text-emerald-700', 'cursor-pointer', 'hover:underline');
                    }
                    if (btnHarga) {
                        btnHarga.disabled = false;
                        btnHarga.classList.remove('text-gray-400', 'cursor-not-allowed');
                        btnHarga.classList.add('text-amber-700', 'cursor-pointer', 'hover:underline');
                    }
                    if (btnHapus) {
                        btnHapus.disabled = false;
                        btnHapus.classList.remove('text-gray-400', 'cursor-not-allowed');
                        btnHapus.classList.add('text-red-700', 'cursor-pointer', 'hover:underline');
                    }
                });
            });

            // Buka modal tambah otomatis jika ada error validasi
            @if ($errors->any() || session('open_modal') === 'tambah')
                bukaModalTambah();
            @endif
        });

        function bukaModal(idModal) {
            const m = document.getElementById(idModal);
            if (m) {
                m.classList.remove('hidden');
            }
        }

        function tutupModal(idModal) {
            const m = document.getElementById(idModal);
            if (m) {
                m.classList.add('hidden');
            }
        }

        function bukaModalTambah() {
            bukaModal('modal-tambah');
            cekTipeItemTambah();
        }

        function cekTipeItemTambah() {
            const tipe = document.getElementById('tambah-tipe').value;
            const stok = document.getElementById('tambah-stok');
            if (tipe.toUpperCase() === 'JASA') {
                stok.disabled = true;
                stok.value = 0;
                stok.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else {
                stok.disabled = false;
                stok.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        }

        function bukaModalUbah(btn) {
            const tr = btn.closest('tr');
            if (!tr) return;

            document.getElementById('ubah-kode-hidden').value = tr.dataset.kode || '';
            document.getElementById('ubah-kode-display').textContent = tr.dataset.kode || '';
            document.getElementById('ubah-nama').value = tr.dataset.nama || '';
            document.getElementById('ubah-jenis').value = tr.dataset.jenis || '';
            document.getElementById('ubah-merek').value = tr.dataset.merek || '';
            document.getElementById('ubah-rak').value = tr.dataset.rak || '';
            document.getElementById('ubah-satuan').value = tr.dataset.satuan || '';

            const tipeVal = (tr.dataset.tipe || 'BARANG').toUpperCase() === 'JASA' ? 'Jasa' : 'Barang';
            document.getElementById('ubah-tipe').value = tipeVal;

            document.getElementById('ubah-batas').value = tr.dataset.batasMinimum || 5;
            document.getElementById('ubah-keterangan').value = tr.dataset.keterangan || '';

            bukaModal('modal-ubah');
        }

        function bukaModalHarga(btn) {
            const tr = btn.closest('tr');
            if (!tr) return;

            document.getElementById('harga-kode-hidden').value = tr.dataset.kode || '';
            document.getElementById('harga-info-barang').textContent = (tr.dataset.kode || '') + ' - ' + (tr.dataset.nama || '');
            document.getElementById('harga-pokok-input').value = tr.dataset.hargaPokok || 0;
            document.getElementById('harga-jual-input').value = tr.dataset.hargaJual || 0;

            cekPeringatanHarga();
            bukaModal('modal-harga');
        }

        function cekPeringatanHarga() {
            const pokok = parseFloat(document.getElementById('harga-pokok-input').value) || 0;
            const jual = parseFloat(document.getElementById('harga-jual-input').value) || 0;
            const peringatan = document.getElementById('peringatan-harga');

            if (jual < pokok) {
                peringatan.classList.remove('hidden');
            } else {
                peringatan.classList.add('hidden');
            }
        }

        function bukaModalHapus(btn) {
            const tr = btn.closest('tr');
            if (!tr) return;

            document.getElementById('hapus-kode-hidden').value = tr.dataset.kode || '';
            document.getElementById('hapus-info-barang').textContent = (tr.dataset.kode || '') + ' - ' + (tr.dataset.nama || '');

            bukaModal('modal-hapus');
        }
    </script>
</body>
</html>
