<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengembalian Barang - Toko Firo</title>

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
<body class="bg-gray-100 text-gray-800 text-base antialiased min-h-screen flex m-0 p-0">

    {{-- Sidebar Navigasi Kasir Mandiri --}}
    <aside class="w-64 bg-[#08331e] text-white h-screen sticky top-0 flex-shrink-0 flex flex-col justify-between overflow-y-auto p-4 outline-none focus:outline-none" tabindex="-1" style="outline: none;">
        <div class="space-y-6">
            {{-- Brand Logo Toko Firo --}}
            <div class="rounded-none p-2 flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Toko Firo" class="w-full max-w-[180px] h-auto object-contain">
            </div>

            @if (strtoupper(Auth::user()->role ?? '') === 'ADMIN')
            <div class="mb-2">
                <a href="{{ route('admin.dashboard') }}" class="text-white hover:text-gray-300 font-bold transition-colors flex items-center gap-2 min-h-[2.75rem] rounded-none p-2 outline-none focus:outline-none focus:ring-0" style="outline: none !important; box-shadow: none !important;">
                    &larr; Kembali ke Menu Admin
                </a>
            </div>
            @endif

            {{-- Menu Navigasi Kasir --}}
            <div>
                <div class="text-xs uppercase tracking-wider text-emerald-300 font-semibold px-3 mb-2">
                    Menu Kasir
                </div>
                <nav class="space-y-1">
                    {{-- 1. Dashboard Kasir --}}
                    <a
                        href="{{ Route::has('kasir.dashboard') ? route('kasir.dashboard') : url('/kasir/dashboard') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Dashboard
                    </a>

                    {{-- 2. Transaksi Penjualan --}}
                    <a
                        href="{{ Route::has('kasir.transaksi') ? route('kasir.transaksi') : url('/kasir/transaksi') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Transaksi Penjualan
                    </a>

                    {{-- 3. Lihat Stok Barang --}}
                    <a
                        href="{{ Route::has('kasir.stok') ? route('kasir.stok') : '#' }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Lihat Stok Barang
                    </a>

                    {{-- 4. Saldo --}}
                    <a
                        href="{{ Route::has('kasir.saldo') ? route('kasir.saldo') : '#' }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Saldo
                    </a>

                    {{-- 5. Info Barang Baru --}}
                    <a
                        href="{{ Route::has('kasir.info-barang') ? route('kasir.info-barang') : '#' }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors text-emerald-100 hover:text-white hover:bg-emerald-900/60"
                    >
                        Info Barang Baru
                    </a>

                    {{-- 6. Pengembalian Barang (Aktif) --}}
                    <a
                        href="{{ route('kasir.pengembalian') }}"
                        class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors bg-emerald-600 text-white font-semibold shadow-sm"
                    >
                        Pengembalian Barang
                    </a>
                </nav>
            </div>
        </div>

        {{-- Profil Pengguna di Bagian Bawah Sidebar --}}
        <div class="pt-4 border-t border-emerald-900/60">
            @if (in_array(strtoupper((string) (Auth::user()?->role ?? '')), ['ADMIN', 'PEMILIK']))
                <a href="{{ Route::has('admin.profil') ? route('admin.profil') : url('/admin/profil') }}" class="block bg-[#052314] hover:bg-emerald-800/60 rounded-none p-3 mb-2 transition-colors cursor-pointer shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none text-lg">
                            {{ $kasir['inisial'] ?? strtoupper(substr((string) (Auth::user()?->nama ?? Auth::user()?->username ?? 'A'), 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-medium text-white text-base truncate">
                                {{ $kasir['nama'] ?? Auth::user()?->nama ?? Auth::user()?->username ?? 'Admin / Pemilik' }}
                            </div>
                            <div class="text-sm text-emerald-300">
                                Admin / Pemilik
                            </div>
                        </div>
                    </div>
                </a>
            @else
                <div class="bg-[#052314] rounded-none p-3 mb-2 flex items-center gap-3">
                    <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none text-lg">
                        {{ $kasir['inisial'] ?? 'K' }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-medium text-white text-base truncate">
                            {{ $kasir['nama'] ?? 'Kasir' }}
                        </div>
                        <div class="text-sm text-emerald-300">
                            {{ $kasir['role'] ?? 'Kasir' }}
                        </div>
                    </div>
                </div>
            @endif
            <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}" data-turbo="false">
                @csrf
                <button
                    type="submit"
                    class="touch-target w-full text-left flex items-center px-3 py-2 rounded-none text-red-300 hover:text-white hover:bg-red-900/40 text-base transition-colors min-h-[2.75rem] cursor-pointer"
                >
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Area Konten Utama Pengembalian Barang --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto min-w-0">
        {{-- Header Halaman --}}
        <header class="flex items-center justify-between pb-6 border-b border-gray-200 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pengembalian Barang</h1>
                <p class="text-gray-500 text-base mt-1">Proses pengembalian barang berdasarkan transaksi yang sudah tercatat.</p>
            </div>
            <div class="flex items-center gap-3 bg-white px-4 py-2 border border-gray-200 rounded-none shadow-sm h-[3.5rem]">
                <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none">
                    {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}
                </div>
                <div class="flex flex-col text-left justify-center">
                    <span class="text-sm font-bold text-gray-800 leading-tight">{{ Auth::user()->nama ?? 'Pengguna' }}</span>
                    <span class="text-xs text-gray-500">{{ (Auth::user()->role ?? '') === 'ADMIN' ? 'Admin / Pemilik' : 'Kasir' }}</span>
                </div>
            </div>
        </header>

        {{-- Toast Notification --}}
        <div id="toast-notif" class="fixed top-6 right-6 z-50 bg-slate-800 text-white font-medium px-5 py-3 rounded-none shadow-xl transition-all duration-300 opacity-0 pointer-events-none text-base border border-slate-700"></div>

        {{-- Stepper Bar --}}
        <div class="grid grid-cols-3 gap-2 mb-6">
            <div id="stepper-1" class="text-center py-2.5 px-3 rounded-none text-sm font-semibold transition-colors bg-emerald-600 text-white">
                1. Cari Transaksi
            </div>
            <div id="stepper-2" class="text-center py-2.5 px-3 rounded-none text-sm font-semibold transition-colors bg-slate-100 text-slate-600">
                2. Detail & Pilih Barang
            </div>
            <div id="stepper-3" class="text-center py-2.5 px-3 rounded-none text-sm font-semibold transition-colors bg-slate-100 text-slate-600">
                3. Konfirmasi & Hasil
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STEP 1: Cari Transaksi --}}
        {{-- ============================================================ --}}
        <div id="step-1">
            {{-- Pencarian Transaksi --}}
            <div class="bg-white rounded-none shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Cari Transaksi</h2>
                <div class="flex gap-3">
                    <input
                        type="text"
                        id="input-cari"
                        placeholder="Cari kode transaksi (contoh: PJ-20261101-0001)"
                        class="touch-target flex-1 bg-slate-50 border-0 rounded-none px-4 py-2.5 text-base focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none min-h-[2.75rem]"
                    >
                    <button
                        type="button"
                        id="btn-cari"
                        class="touch-target px-6 py-2.5 bg-emerald-600 text-white rounded-none hover:bg-emerald-700 transition-colors text-base font-semibold min-h-[2.75rem]"
                    >
                        Cari
                    </button>
                </div>
            </div>

            {{-- Tabel Transaksi Terbaru --}}
            <div class="bg-white rounded-none shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-gray-800" id="label-tabel-transaksi">Transaksi Terbaru</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-base">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="text-left px-6 py-3 font-semibold text-slate-700">Kode</th>
                                <th class="text-left px-6 py-3 font-semibold text-slate-700">Tanggal</th>
                                <th class="text-left px-6 py-3 font-semibold text-slate-700">Pelanggan</th>
                                <th class="text-right px-6 py-3 font-semibold text-slate-700">Total</th>
                                <th class="text-left px-6 py-3 font-semibold text-slate-700">Metode</th>
                                <th class="text-center px-6 py-3 font-semibold text-slate-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-transaksi" class="divide-y divide-slate-100">
                            @forelse ($transaksiTerbaru as $trx)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3 font-mono text-sm text-emerald-700">{{ $trx->kode_transaksi ?? '-' }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $trx->tanggal_transaksi ? \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d M Y, H:i') : '-' }}</td>
                                <td class="px-6 py-3 text-slate-700">{{ $trx->nama_pelanggan ?: 'Umum' }}</td>
                                <td class="px-6 py-3 text-right font-medium">Rp {{ number_format($trx->total_bayar ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-block px-2.5 py-1 rounded-none text-xs font-medium {{ ($trx->metode_bayar ?? '') === 'TUNAI' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $trx->metode_bayar ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <button
                                        type="button"
                                        onclick="pilihTransaksi('{{ $trx->kode_transaksi }}')"
                                        class="touch-target px-4 py-1.5 bg-emerald-600 text-white text-sm rounded-none hover:bg-emerald-700 transition-colors font-medium min-h-[2.75rem]"
                                    >
                                        Pilih
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    Belum ada data transaksi.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STEP 2: Detail & Pilih Barang --}}
        {{-- ============================================================ --}}
        <div id="step-2" class="hidden">
            {{-- Header Transaksi --}}
            <div class="bg-white rounded-none shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Detail Transaksi</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-sm text-slate-500">Nota</div>
                        <div class="font-semibold text-slate-800 font-mono" id="detail-nota">-</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Tanggal</div>
                        <div class="font-semibold text-slate-800" id="detail-tanggal">-</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Kasir</div>
                        <div class="font-semibold text-slate-800" id="detail-kasir">-</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Total</div>
                        <div class="font-semibold text-slate-800" id="detail-total">-</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Metode</div>
                        <div class="font-semibold text-slate-800" id="detail-metode">-</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Pelanggan</div>
                        <div class="font-semibold text-slate-800" id="detail-pelanggan">-</div>
                    </div>
                </div>
            </div>

            {{-- Tabel Item Returnable --}}
            <div class="bg-white rounded-none shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-gray-800">Pilih Barang yang Dikembalikan</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-base">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="text-left px-6 py-3 font-semibold text-slate-700">Nama Barang</th>
                                <th class="text-left px-6 py-3 font-semibold text-slate-700">Satuan</th>
                                <th class="text-right px-6 py-3 font-semibold text-slate-700">Harga</th>
                                <th class="text-center px-6 py-3 font-semibold text-slate-700">Terjual</th>
                                <th class="text-center px-6 py-3 font-semibold text-slate-700">Sudah Diretur</th>
                                <th class="text-center px-6 py-3 font-semibold text-slate-700">Bisa Diretur</th>
                                <th class="text-center px-6 py-3 font-semibold text-slate-700">Jumlah Retur</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-items" class="divide-y divide-slate-100">
                            {{-- Populated by JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Alasan & Total Refund --}}
            <div class="bg-white rounded-none shadow-sm p-6 mb-6">
                <div class="mb-4">
                    <label for="input-alasan" class="block text-sm font-semibold text-slate-700 mb-2">Alasan Pengembalian (opsional)</label>
                    <textarea
                        id="input-alasan"
                        rows="3"
                        placeholder="Contoh: Barang rusak, salah kirim, dll."
                        class="touch-target w-full bg-slate-50 border-0 rounded-none px-4 py-2.5 text-base focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none resize-none min-h-[2.75rem]"
                    ></textarea>
                </div>
                <div class="flex items-center justify-between border-t border-slate-200 pt-4">
                    <div class="text-base text-slate-600">Total Refund:</div>
                    <div class="text-xl font-bold text-emerald-700" id="total-refund">Rp 0</div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between">
                <button
                    type="button"
                    id="btn-kembali-step1"
                    class="touch-target px-6 py-2.5 border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium rounded-none transition-colors text-base min-h-[2.75rem]"
                >
                    Kembali
                </button>
                <button
                    type="button"
                    id="btn-proses"
                    class="touch-target px-6 py-2.5 bg-emerald-600 text-white rounded-none hover:bg-emerald-700 transition-colors text-base font-semibold min-h-[2.75rem]"
                >
                    Proses Pengembalian
                </button>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STEP 3: Konfirmasi & Hasil --}}
        {{-- ============================================================ --}}
        <div id="step-3" class="hidden">
            <div class="bg-white rounded-none shadow-sm p-8 text-center">
                <div class="inline-flex items-center justify-center px-4 py-2 rounded-none bg-emerald-100 text-emerald-800 font-bold text-sm tracking-wider uppercase mb-4">BERHASIL</div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Pengembalian Berhasil</h2>
                <p class="text-slate-500 mb-6" id="hasil-pesan">Pengembalian barang berhasil diproses.</p>

                <div class="bg-slate-50 rounded-none p-6 text-left max-w-lg mx-auto mb-6">
                    <div class="mb-3">
                        <span class="text-sm text-slate-500">Nota:</span>
                        <span class="font-semibold text-slate-800 font-mono ml-2" id="hasil-nota">-</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-sm text-slate-500">Barang Dikembalikan:</span>
                        <div id="hasil-items" class="mt-1 space-y-1"></div>
                    </div>
                    <div class="border-t border-slate-200 pt-3 mt-3 flex items-center justify-between">
                        <span class="text-base font-semibold text-slate-700">Total Refund:</span>
                        <span class="text-lg font-bold text-emerald-700" id="hasil-total-refund">Rp 0</span>
                    </div>
                </div>

                <button
                    type="button"
                    id="btn-retur-lagi"
                    class="touch-target px-6 py-2.5 bg-emerald-600 text-white rounded-none hover:bg-emerald-700 transition-colors text-base font-semibold min-h-[2.75rem]"
                >
                    Retur Lagi
                </button>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- Riwayat Retur (selalu tampil) --}}
        {{-- ============================================================ --}}
        <div class="mt-8 bg-white rounded-none shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-gray-800">Riwayat Pengembalian</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-base">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-6 py-3 font-semibold text-slate-700">Waktu</th>
                            <th class="text-left px-6 py-3 font-semibold text-slate-700">Pelaku</th>
                            <th class="text-left px-6 py-3 font-semibold text-slate-700">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($riwayatRetur as $retur)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ $retur->waktu ? \Carbon\Carbon::parse($retur->waktu)->format('d M Y, H:i') : '-' }}</td>
                            <td class="px-6 py-3 text-slate-700">{{ $retur->pelaku ?? '-' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $retur->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                                Belum ada riwayat pengembalian.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // ============================================================
        // State
        // ============================================================
        let currentStep = 1;
        let selectedNota = null;
        let detailData = null;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // ============================================================
        // Helpers
        // ============================================================
        function formatRupiah(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        }

        function formatTanggal(str) {
            if (!str) return '-';
            const d = new Date(str);
            const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            return d.getDate() + ' ' + bulan[d.getMonth()] + ' ' + d.getFullYear() + ', ' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ============================================================
        // Toast
        // ============================================================
        function alertToast(pesan, tipe) {
            const el = document.getElementById('toast-notif');
            if (!el) return;
            el.textContent = pesan;
            el.style.backgroundColor = tipe === 'error' ? '#dc2626' : '#059669';
            el.style.borderColor = tipe === 'error' ? '#b91c1c' : '#047857';
            el.classList.remove('opacity-0', 'pointer-events-none');
            el.classList.add('opacity-100');
            setTimeout(() => {
                el.classList.remove('opacity-100');
                el.classList.add('opacity-0', 'pointer-events-none');
            }, 3500);
        }

        // ============================================================
        // Step Management
        // ============================================================
        function showStep(step) {
            document.getElementById('step-1').classList.toggle('hidden', step !== 1);
            document.getElementById('step-2').classList.toggle('hidden', step !== 2);
            document.getElementById('step-3').classList.toggle('hidden', step !== 3);
            currentStep = step;
            updateStepper(step);
        }

        function updateStepper(active) {
            for (let i = 1; i <= 3; i++) {
                const el = document.getElementById('stepper-' + i);
                if (!el) continue;
                if (i < active) {
                    el.className = 'text-center py-2.5 px-3 rounded-none text-sm font-semibold transition-colors bg-emerald-100 text-emerald-800';
                } else if (i === active) {
                    el.className = 'text-center py-2.5 px-3 rounded-none text-sm font-semibold transition-colors bg-emerald-600 text-white';
                } else {
                    el.className = 'text-center py-2.5 px-3 rounded-none text-sm font-semibold transition-colors bg-slate-100 text-slate-600';
                }
            }
        }

        // ============================================================
        // Step 1: Search
        // ============================================================
        const inputCari = document.getElementById('input-cari');
        const btnCari = document.getElementById('btn-cari');

        btnCari.addEventListener('click', function() { cariTransaksi(); });
        inputCari.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') cariTransaksi();
        });

        function cariTransaksi() {
            const q = inputCari.value.trim();
            if (!q || q.length < 2) {
                alertToast('Masukkan minimal 2 karakter untuk mencari.', 'error');
                return;
            }

            btnCari.disabled = true;
            btnCari.textContent = 'Mencari...';

            fetch('/kasir/pengembalian/cari?q=' + encodeURIComponent(q), {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
            .then(async res => {
                const data = await res.json();
                return { ok: res.ok, status: res.status, data };
            })
            .then(({ ok, data }) => {
                if (!ok) {
                    alertToast(data.message || 'Gagal mencari transaksi.', 'error');
                    return;
                }
                renderTabelTransaksi(data.data || [], true);
            })
            .catch(() => {
                alertToast('Terjadi kesalahan koneksi.', 'error');
            })
            .finally(() => {
                btnCari.disabled = false;
                btnCari.textContent = 'Cari';
            });
        }

        function renderTabelTransaksi(rows, isSearch) {
            const tbody = document.getElementById('tbody-transaksi');
            const label = document.getElementById('label-tabel-transaksi');
            label.textContent = isSearch ? 'Hasil Pencarian' : 'Transaksi Terbaru';

            if (!rows || rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">' + (isSearch ? 'Tidak ditemukan transaksi yang cocok.' : 'Belum ada data transaksi.') + '</td></tr>';
                return;
            }

            let html = '';
            rows.forEach(trx => {
                const kode = escapeHtml(trx.kode_transaksi || '-');
                const tanggal = formatTanggal(trx.tanggal_transaksi);
                const pelanggan = escapeHtml(trx.nama_pelanggan || 'Umum');
                const total = formatRupiah(trx.total_bayar || 0);
                const metode = escapeHtml(trx.metode_bayar || '-');
                const metodeCls = (trx.metode_bayar || '') === 'TUNAI' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700';

                html += '<tr class="hover:bg-slate-50 transition-colors">';
                html += '<td class="px-6 py-3 font-mono text-sm text-emerald-700">' + kode + '</td>';
                html += '<td class="px-6 py-3 text-slate-600">' + escapeHtml(tanggal) + '</td>';
                html += '<td class="px-6 py-3 text-slate-700">' + pelanggan + '</td>';
                html += '<td class="px-6 py-3 text-right font-medium">' + total + '</td>';
                html += '<td class="px-6 py-3"><span class="inline-block px-2.5 py-1 rounded-none text-xs font-medium ' + metodeCls + '">' + metode + '</span></td>';
                html += '<td class="px-6 py-3 text-center"><button type="button" onclick="pilihTransaksi(\'' + escapeHtml(trx.kode_transaksi || '') + '\')" class="touch-target px-4 py-1.5 bg-emerald-600 text-white text-sm rounded-none hover:bg-emerald-700 transition-colors font-medium min-h-[2.75rem]">Pilih</button></td>';
                html += '</tr>';
            });

            tbody.innerHTML = html;
        }

        // ============================================================
        // Step 1 -> Step 2: Select transaction
        // ============================================================
        function pilihTransaksi(nota) {
            if (!nota) return;
            selectedNota = nota;

            alertToast('Memuat detail transaksi...', 'success');

            fetch('/kasir/pengembalian/' + encodeURIComponent(nota) + '/detail', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
            .then(async res => {
                const data = await res.json();
                return { ok: res.ok, status: res.status, data };
            })
            .then(({ ok, data }) => {
                if (!ok) {
                    alertToast(data.error || 'Gagal memuat detail transaksi.', 'error');
                    return;
                }
                detailData = data;
                renderDetail(data);
                showStep(2);
            })
            .catch(() => {
                alertToast('Terjadi kesalahan koneksi.', 'error');
            });
        }

        function renderDetail(data) {
            const trx = data.transaksi;
            document.getElementById('detail-nota').textContent = trx.kode_transaksi || '-';
            document.getElementById('detail-tanggal').textContent = formatTanggal(trx.tanggal_transaksi);
            document.getElementById('detail-kasir').textContent = trx.kasir_nama || '-';
            document.getElementById('detail-total').textContent = formatRupiah(trx.total_bayar || 0);
            document.getElementById('detail-metode').textContent = trx.metode_bayar || '-';
            document.getElementById('detail-pelanggan').textContent = trx.nama_pelanggan || 'Umum';

            const tbody = document.getElementById('tbody-items');
            const items = data.items || [];

            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center text-slate-400">Tidak ada barang yang bisa diretur.</td></tr>';
                return;
            }

            let html = '';
            items.forEach((item, idx) => {
                html += '<tr class="hover:bg-slate-50 transition-colors">';
                html += '<td class="px-6 py-3 text-slate-800">' + escapeHtml(item.nama_item || '-') + '</td>';
                html += '<td class="px-6 py-3 text-slate-600">' + escapeHtml(item.satuan || '-') + '</td>';
                html += '<td class="px-6 py-3 text-right font-medium">' + formatRupiah(item.harga_jual || 0) + '</td>';
                html += '<td class="px-6 py-3 text-center">' + (item.total_terjual || 0) + '</td>';
                html += '<td class="px-6 py-3 text-center">' + (item.total_diretur || 0) + '</td>';
                html += '<td class="px-6 py-3 text-center font-semibold text-emerald-700">' + (item.bisa_diretur || 0) + '</td>';
                html += '<td class="px-6 py-3 text-center">';
                html += '<input type="number" min="0" max="' + (item.bisa_diretur || 0) + '" value="0"';
                html += ' data-kode="' + escapeHtml(item.kode_item || '') + '"';
                html += ' data-harga="' + (item.harga_jual || 0) + '"';
                html += ' class="retur-qty touch-target w-20 border border-slate-300 rounded-none px-2 py-1.5 text-center text-base focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none min-h-[2.75rem]"';
                html += '>';
                html += '</td>';
                html += '</tr>';
            });

            tbody.innerHTML = html;

            // Attach change listeners
            document.querySelectorAll('.retur-qty').forEach(input => {
                input.addEventListener('input', hitungTotalRefund);
            });

            hitungTotalRefund();
        }

        function hitungTotalRefund() {
            let total = 0;
            document.querySelectorAll('.retur-qty').forEach(input => {
                const qty = parseInt(input.value) || 0;
                const max = parseInt(input.max) || 0;
                const harga = parseFloat(input.dataset.harga) || 0;

                // Clamp value
                if (qty > max) {
                    input.value = max;
                }
                if (qty < 0) {
                    input.value = 0;
                }

                const validQty = Math.min(Math.max(parseInt(input.value) || 0, 0), max);
                total += validQty * harga;
            });

            document.getElementById('total-refund').textContent = formatRupiah(total);
        }

        // ============================================================
        // Step 2: Back button
        // ============================================================
        document.getElementById('btn-kembali-step1').addEventListener('click', function() {
            showStep(1);
            selectedNota = null;
            detailData = null;
            document.getElementById('input-alasan').value = '';
        });

        // ============================================================
        // Step 2 -> Step 3: Process return
        // ============================================================
        document.getElementById('btn-proses').addEventListener('click', function() {
            const items = [];
            document.querySelectorAll('.retur-qty').forEach(input => {
                const qty = parseInt(input.value) || 0;
                if (qty > 0) {
                    items.push({
                        kode_item: input.dataset.kode,
                        qty: qty
                    });
                }
            });

            if (items.length === 0) {
                alertToast('Pilih minimal 1 barang untuk diretur.', 'error');
                return;
            }

            const alasan = document.getElementById('input-alasan').value.trim();
            const btnProses = document.getElementById('btn-proses');
            btnProses.disabled = true;
            btnProses.textContent = 'Memproses...';

            fetch('/kasir/pengembalian/proses', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    nota: selectedNota,
                    items: items,
                    alasan: alasan || null,
                })
            })
            .then(async res => {
                const data = await res.json();
                return { ok: res.ok, status: res.status, data };
            })
            .then(({ ok, data }) => {
                if (!ok) {
                    alertToast(data.error || data.message || 'Gagal memproses pengembalian.', 'error');
                    return;
                }

                renderHasil(data);
                showStep(3);
                alertToast(data.message || 'Pengembalian berhasil diproses.', 'success');
            })
            .catch(() => {
                alertToast('Terjadi kesalahan koneksi.', 'error');
            })
            .finally(() => {
                btnProses.disabled = false;
                btnProses.textContent = 'Proses Pengembalian';
            });
        });

        function renderHasil(data) {
            document.getElementById('hasil-pesan').textContent = data.message || 'Pengembalian barang berhasil diproses.';
            document.getElementById('hasil-nota').textContent = data.nota || '-';
            document.getElementById('hasil-total-refund').textContent = formatRupiah(data.total_refund || 0);

            const container = document.getElementById('hasil-items');
            const items = data.items || [];
            let html = '';
            items.forEach(item => {
                html += '<div class="flex items-center justify-between text-sm py-1">';
                html += '<span class="text-slate-700">' + escapeHtml(item.nama_item || item.kode_item || '-') + ' x' + (item.qty || 0) + '</span>';
                html += '<span class="font-medium text-slate-800">' + formatRupiah(item.subtotal || 0) + '</span>';
                html += '</div>';
            });
            container.innerHTML = html;
        }

        // ============================================================
        // Step 3: Retur Lagi
        // ============================================================
        document.getElementById('btn-retur-lagi').addEventListener('click', function() {
            selectedNota = null;
            detailData = null;
            document.getElementById('input-alasan').value = '';
            document.getElementById('input-cari').value = '';
            showStep(1);
        });
    </script>
</body>
</html>
