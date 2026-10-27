<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengaturan Notifikasi Bot - Toko Firo</title>

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
<body class="min-h-screen flex bg-slate-50 text-slate-800 text-base antialiased">

    {{-- Notifikasi Feedback Prototype --}}
    <div id="notif-feedback" class="hidden fixed top-6 right-8 bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg text-base font-medium z-50 transition-opacity">
        Pemberitahuan berhasil diproses (prototype).
    </div>

    {{-- Sidebar Navigasi Admin Terpusat --}}
    @include('partials.sidebar_admin')

    {{-- Area Konten Utama --}}
    <main class="flex-1 min-w-0 p-8">
        {{-- Header Halaman --}}
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">
                Pengaturan Notifikasi Bot
            </h1>
            <p class="text-slate-500 text-base mt-1">
                Konfigurasi bot perpesanan instan untuk notifikasi otomatis toko, peringatan stok, dan laporan transaksi.
            </p>
        </header>

        {{-- Grid Konfigurasi Bot (Telegram & WhatsApp) --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch mb-6">
            {{-- Kartu Kiri: Telegram Bot --}}
            <div class="border border-slate-200 rounded-2xl bg-white p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-lg font-bold text-slate-800">
                            Telegram Bot
                        </h2>
                        @if ($telegramConfig['is_connected'] ?? false)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                • Terhubung
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                • {{ $telegramConfig['status'] ?? 'Belum Terhubung' }}
                            </span>
                        @endif
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 my-4 font-mono text-sm space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <span class="text-slate-500 font-sans font-medium text-xs uppercase tracking-wider">Bot Token:</span>
                            <span class="text-slate-800 font-medium select-all break-all">{{ $telegramConfig['bot_token'] }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 pt-1.5 border-t border-slate-200/60">
                            <span class="text-slate-500 font-sans font-medium text-xs uppercase tracking-wider">Chat ID:</span>
                            <span class="text-slate-800 font-medium select-all">{{ $telegramConfig['chat_id'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-auto pt-4 flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        id="btn-test-koneksi"
                        onclick="kirimPesanUjiCoba(this)"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-5 py-2.5 rounded-xl min-h-[2.75rem] transition-colors cursor-pointer text-base shadow-sm"
                    >
                        Kirim Pesan Tes
                    </button>
                    <button
                        type="button"
                        onclick="tampilkanNotif('Koneksi bot Telegram diputuskan.', 'sukses')"
                        class="border border-red-300 hover:bg-red-50 text-red-600 font-medium px-5 py-2.5 rounded-xl min-h-[2.75rem] transition-colors cursor-pointer text-base shadow-sm"
                    >
                        Putuskan
                    </button>
                </div>
            </div>

            {{-- Kartu Kanan: WhatsApp Bot --}}
            <div class="border border-slate-200 rounded-2xl bg-white p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-lg font-bold text-slate-800">
                            WhatsApp Bot
                        </h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-500 border border-slate-200">
                            Segera Hadir
                        </span>
                    </div>
                    <div class="my-4 space-y-2">
                        <p class="text-base text-slate-600 leading-relaxed">
                            Integrasi WhatsApp Business API sedang dalam tahap pengembangan aktif untuk mendukung ekosistem toko modern.
                        </p>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            Fitur ini akan memungkinkan pengiriman otomatis nota struk belanja digital pelanggan, konfirmasi pesanan, serta rekap kasir harian secara instan.
                        </p>
                    </div>
                </div>
                <div class="mt-auto pt-4">
                    <button
                        type="button"
                        disabled
                        class="border border-slate-200 bg-slate-100 text-slate-400 font-medium px-5 py-2.5 rounded-xl min-h-[2.75rem] cursor-not-allowed text-base"
                    >
                        Hubungkan WhatsApp
                    </button>
                </div>
            </div>
        </section>

        {{-- Kartu Pengaturan: Jenis Notifikasi yang Dikirim --}}
        <section class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">
                Jenis Notifikasi yang Dikirim
            </h2>
            <div class="divide-y divide-slate-100">
                @foreach ($daftarPengaturan as $pengaturan)
                    <label class="py-4 px-3 flex items-center justify-between hover:bg-slate-50/70 rounded-xl transition-colors min-h-[2.75rem] cursor-pointer">
                        <span class="text-base font-medium text-slate-800 pr-4">
                            {{ $pengaturan->label }}
                        </span>
                        <div class="min-h-[2.75rem] min-w-[2.75rem] flex items-center justify-end shrink-0">
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input
                                    type="checkbox"
                                    id="toggle-{{ $pengaturan->id }}"
                                    {{ $pengaturan->aktif ? 'checked' : '' }}
                                    onchange="simpanPengaturan('{{ $pengaturan->id }}', this.checked)"
                                    class="sr-only peer"
                                >
                                <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </section>

        {{-- Tabel Riwayat Notifikasi Terkirim --}}
        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800">
                    Riwayat Notifikasi Terkirim
                </h2>
                <span class="text-sm font-medium text-slate-500">
                    4 entri terakhir
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-base">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase font-semibold text-slate-600 tracking-wider">
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4">Pesan</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($riwayatNotifikasi as $notif)
                            <tr class="hover:bg-slate-50/70 transition-colors min-h-[2.75rem]">
                                <td class="px-6 py-4 font-mono font-medium text-slate-600 whitespace-nowrap">
                                    {{ $notif->waktu }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $notif->tipe }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-800">
                                    {{ $notif->pesan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($notif->status === 'Terkirim')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Terkirim
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-50 text-red-700 border border-red-200">
                                            Gagal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    {{-- Script Vanilla JavaScript Feedback & Interaksi --}}
    <script>
        let notifTimeout = null;
        function tampilkanNotif(pesan, jenis = 'sukses') {
            const notif = document.getElementById('notif-feedback');
            if (!notif) return;

            notif.textContent = pesan;
            notif.classList.remove('hidden', 'bg-emerald-600', 'bg-rose-600');

            if (jenis === 'error') {
                notif.classList.add('bg-rose-600');
            } else {
                notif.classList.add('bg-emerald-600');
            }

            if (notifTimeout) clearTimeout(notifTimeout);
            notifTimeout = setTimeout(() => {
                notif.classList.add('hidden');
            }, 3500);
        }

        function kirimPesanUjiCoba(btn) {
            if (!btn || btn.disabled) return;

            const teksAsli = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Mengirim tes...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            fetch('{{ route('admin.notifikasi.test') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(async (response) => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan saat menghubungi server Telegram.');
                }
                return data;
            })
            .then((data) => {
                tampilkanNotif(data.message || 'Pesan uji coba berhasil dikirim ke Telegram!', 'sukses');
            })
            .catch((error) => {
                tampilkanNotif(error.message || 'Gagal mengirim pesan uji coba.', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = teksAsli;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            });
        }

        function simpanPengaturan(id, aktif) {
            fetch('{{ route('admin.notifikasi.simpan') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: id, aktif: aktif })
            })
            .then(async (response) => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menyimpan pengaturan notifikasi.');
                }
                return data;
            })
            .then((data) => {
                tampilkanNotif(data.message || 'Pengaturan notifikasi berhasil diperbarui.', 'sukses');
            })
            .catch((error) => {
                tampilkanNotif(error.message || 'Gagal menyimpan pengaturan.', 'error');
            });
        }
    </script>
</body>
</html>
