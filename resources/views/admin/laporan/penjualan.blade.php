<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Penjualan - Toko Firo</title>

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
        @media print {
            aside, button, form, .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            main {
                padding: 0 !important;
            }
        }
    </style>

    @include('partials.skala')
</head>
<body class="min-h-screen flex bg-slate-50 text-gray-800 text-base antialiased">

    {{-- Sidebar Navigasi Admin Terpusat --}}
    @include('partials.sidebar_admin')

    {{-- Area Konten Utama --}}
    <main class="flex-1 min-w-0 p-8">
        {{-- Header Halaman --}}
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Laporan Penjualan
            </h1>
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    onclick="window.print()"
                    class="touch-target px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-base shadow-sm transition-colors cursor-pointer"
                >
                    Simpan sebagai PDF
                </button>
                <button
                    type="button"
                    id="btn-kirim-telegram"
                    onclick="kirimLaporanTelegram(this)"
                    class="touch-target px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-medium rounded-xl text-base shadow-sm transition-colors cursor-pointer"
                >
                    Kirim ke Telegram
                </button>
            </div>
        </header>

        {{-- Alert Notifikasi Dinamis Telegram --}}
        <div id="telegram-alert" class="hidden mb-6 p-4 rounded-xl text-base font-medium"></div>

        {{-- Pesan Notifikasi jika ada --}}
        @if (session('pesan') || $pesan)
            <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-base font-medium">
                {{ session('pesan') ?? $pesan }}
            </div>
        @endif

        {{-- Tab Penjualan dan Pembelian --}}
        <div class="flex items-center gap-4 border-b border-gray-200 mb-6">
            <button
                type="button"
                class="touch-target px-4 py-2 border-b-2 border-emerald-600 text-emerald-800 font-bold text-base"
            >
                Penjualan
            </button>
            <button
                type="button"
                disabled
                class="touch-target px-4 py-2 text-gray-400 font-medium text-base cursor-not-allowed"
            >
                Pembelian
            </button>
        </div>

        {{-- Baris Filter Tanggal & Tautan Cepat --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                {{-- Form Filter Kustom --}}
                <form method="GET" action="{{ route('admin.laporan.penjualan') }}" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="rentang" value="kustom">

                    <div class="flex items-center gap-2">
                        <label for="mulai" class="text-base font-medium text-gray-700">Dari tanggal</label>
                        <input
                            type="date"
                            id="mulai"
                            name="mulai"
                            value="{{ $filter['mulai'] }}"
                            class="touch-target bg-white border border-gray-300 rounded-lg px-3 py-2 text-base text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-600"
                        >
                    </div>

                    <div class="flex items-center gap-2">
                        <label for="sampai" class="text-base font-medium text-gray-700">Sampai tanggal</label>
                        <input
                            type="date"
                            id="sampai"
                            name="sampai"
                            value="{{ $filter['sampai'] }}"
                            class="touch-target bg-white border border-gray-300 rounded-lg px-3 py-2 text-base text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-600"
                        >
                    </div>

                    <button
                        type="submit"
                        class="touch-target px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg text-base shadow-sm transition-colors"
                    >
                        Terapkan
                    </button>
                </form>

                {{-- Tautan Rentang Cepat --}}
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        href="{{ route('admin.laporan.penjualan', ['rentang' => 'hari-ini']) }}"
                        class="touch-target flex items-center justify-center px-4 py-2 rounded-lg text-base font-medium border transition-colors {{ $filter['rentang'] === 'hari-ini' ? 'bg-emerald-100 text-emerald-800 border-emerald-300 font-semibold' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                    >
                        Hari ini
                    </a>
                    <a
                        href="{{ route('admin.laporan.penjualan', ['rentang' => '7-hari']) }}"
                        class="touch-target flex items-center justify-center px-4 py-2 rounded-lg text-base font-medium border transition-colors {{ $filter['rentang'] === '7-hari' ? 'bg-emerald-100 text-emerald-800 border-emerald-300 font-semibold' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                    >
                        7 hari
                    </a>
                    <a
                        href="{{ route('admin.laporan.penjualan', ['rentang' => 'bulan-ini']) }}"
                        class="touch-target flex items-center justify-center px-4 py-2 rounded-lg text-base font-medium border transition-colors {{ $filter['rentang'] === 'bulan-ini' ? 'bg-emerald-100 text-emerald-800 border-emerald-300 font-semibold' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                    >
                        Bulan ini
                    </a>
                </div>
            </div>
        </div>

        {{-- Tiga Kartu Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Kartu 1: Total Penjualan --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="text-sm uppercase tracking-wider text-gray-500 font-medium mb-1">
                    Total Penjualan
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">
                    Rp {{ number_format($ringkasan['total_penjualan'], 0, ',', '.') }}
                </div>
                <div class="text-sm text-gray-500">
                    {{ $ringkasan['pembanding_penjualan'] ?? 'Belum ada pembanding' }}
                </div>
            </div>

            {{-- Kartu 2: Jumlah Nota --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="text-sm uppercase tracking-wider text-gray-500 font-medium mb-1">
                    Jumlah Nota
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">
                    {{ number_format($ringkasan['jumlah_nota'], 0, ',', '.') }}
                </div>
                <div class="text-sm text-gray-500">
                    {{ $ringkasan['pembanding_nota'] ?? 'Belum ada pembanding' }}
                </div>
            </div>

            {{-- Kartu 3: Barang Terjual --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="text-sm uppercase tracking-wider text-gray-500 font-medium mb-1">
                    Barang Terjual
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">
                    {{ number_format($ringkasan['barang_terjual'], 0, ',', '.') }} item
                </div>
                <div class="text-sm text-gray-500">
                    {{ $ringkasan['barang_terlaris'] ? 'Terlaris: ' . $ringkasan['barang_terlaris'] : 'Belum ada data' }}
                </div>
            </div>
        </div>

        {{-- Kartu Riwayat Transaksi --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">
                    Riwayat Transaksi
                </h2>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full min-w-[43.75rem] border-collapse text-left text-base">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold">
                            <th class="py-3.5 px-6">Tanggal</th>
                            <th class="py-3.5 px-6">No. Transaksi</th>
                            <th class="py-3.5 px-6">Kasir</th>
                            <th class="py-3.5 px-6 text-center">Item</th>
                            <th class="py-3.5 px-6 text-center">Qty</th>
                            <th class="py-3.5 px-6 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($transaksi as $trx)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3.5 px-6 text-gray-700 whitespace-nowrap">
                                    {{ date('d/m/y', strtotime($trx->tanggal)) }}
                                </td>
                                <td class="py-3.5 px-6 text-gray-900 font-medium whitespace-nowrap">
                                    {{ $trx->kode_transaksi }}
                                </td>
                                <td class="py-3.5 px-6 text-gray-700">
                                    {{ $trx->nama_kasir }}
                                </td>
                                <td class="py-3.5 px-6 text-center text-gray-700">
                                    {{ $trx->jumlah_item }}
                                </td>
                                <td class="py-3.5 px-6 text-center text-gray-700">
                                    {{ $trx->total_qty }}
                                </td>
                                <td class="py-3.5 px-6 text-right font-medium whitespace-nowrap">
                                    @if ($trx->subtotal < 0)
                                        <span class="text-red-600">-Rp {{ number_format(abs($trx->subtotal), 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-900">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500 text-base">
                                    Belum ada transaksi pada rentang ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Paginasi Riwayat Transaksi --}}
            <div class="px-6 py-4 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-base text-gray-600">
                    Menampilkan {{ $transaksi->firstItem() ?? 0 }}-{{ $transaksi->lastItem() ?? 0 }} dari {{ $transaksi->total() }} transaksi
                </div>

                {{ $transaksi->links('admin.laporan.pagination') }}
            </div>
        </div>

        {{-- Kartu Rekap Otomatis --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-2">
                Rekap Otomatis
            </h2>
            <p class="text-base text-gray-600">
                Rekap otomatis belum tersedia.
            </p>
        </div>
    </main>

    {{-- Script Interaksi Kirim Laporan ke Telegram --}}
    <script>
        function kirimLaporanTelegram(btn) {
            if (!btn || btn.disabled) return;

            const teksAsli = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Mengirim...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            const alertBox = document.getElementById('telegram-alert');
            if (alertBox) {
                alertBox.classList.add('hidden');
            }

            const payload = {
                mulai: '{{ $filter['mulai'] ?? '' }}',
                sampai: '{{ $filter['sampai'] ?? '' }}',
                rentang: '{{ $filter['rentang'] ?? '' }}'
            };

            fetch('{{ route('admin.laporan.kirim-telegram') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(async (response) => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan pada server saat mengirim laporan.');
                }
                return data;
            })
            .then((data) => {
                if (alertBox) {
                    alertBox.className = 'mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-base font-medium';
                    alertBox.innerText = data.message || 'Laporan penjualan berhasil dikirim ke Telegram!';
                    alertBox.classList.remove('hidden');
                } else {
                    alert(data.message || 'Laporan penjualan berhasil dikirim ke Telegram!');
                }
            })
            .catch((error) => {
                if (alertBox) {
                    alertBox.className = 'mb-6 p-4 rounded-xl bg-rose-50 border border-rose-300 text-rose-900 text-base font-medium';
                    alertBox.innerText = error.message || 'Gagal mengirim laporan ke Telegram.';
                    alertBox.classList.remove('hidden');
                } else {
                    alert(error.message || 'Gagal mengirim laporan ke Telegram.');
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = teksAsli;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            });
        }
    </script>
</body>
</html>

