<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Backup Data - Toko Firo</title>

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

    {{-- Notifikasi Feedback Interaktif --}}
    <div id="notif-feedback" class="hidden fixed top-6 right-8 bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg text-base font-medium z-50 transition-opacity">
        Aksi backup berhasil dijalankan.
    </div>

    {{-- Sidebar Navigasi Admin Terpusat --}}
    @include('partials.sidebar_admin')

    {{-- Area Konten Utama --}}
    <main class="flex-1 min-w-0 p-8">
        {{-- Header Halaman --}}
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Backup Data
            </h1>
            <p class="text-slate-500 text-base mt-1">
                Pencadangan database toko untuk mengamankan riwayat transaksi dan data master barang Toko Firo.
            </p>
        </header>

        {{-- Pesan Flash Notifikasi jika ada --}}
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-base font-medium">
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-300 text-rose-900 text-base font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- Kartu Pengaturan Backup (Grid 2 Kolom Sesuai Figma P14/P15) --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- Kartu Kiri: Backup Manual --}}
            <div class="border-2 border-emerald-500 bg-white p-6 rounded-xl shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl font-bold text-gray-900">
                            Backup Manual
                        </h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            Tersedia
                        </span>
                    </div>
                    <p class="text-base text-gray-600 mb-6 leading-relaxed">
                        Unduh salinan cadangan database Toko Firo saat ini ke file SQL terenkripsi.
                    </p>
                </div>
                <div>
                    <button
                        type="button"
                        id="btn-buat-backup"
                        onclick="jalankanBackup(this)"
                        class="touch-target w-full sm:w-auto px-6 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-base shadow-sm transition-colors text-center cursor-pointer"
                    >
                        Jalankan Backup Sekarang
                    </button>
                </div>
            </div>

            {{-- Kartu Kanan: Backup Otomatis --}}
            <div class="border border-slate-200 bg-white p-6 rounded-xl shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl font-bold text-gray-900">
                            Backup Otomatis
                        </h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            Aktif
                        </span>
                    </div>
                    <p class="text-base text-gray-600 mb-6 leading-relaxed">
                        Jadwal aktif: Setiap hari pukul 22.00 WIB. Disimpan di server toko.
                    </p>
                </div>
                <div>
                    <a
                        href="{{ Route::has('admin.notifikasi.index') ? route('admin.notifikasi.index') : (Route::has('admin.notifikasi') ? route('admin.notifikasi') : url('/admin/pengaturan-notifikasi')) }}"
                        class="touch-target inline-block w-full sm:w-auto px-6 py-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-base shadow-sm transition-colors text-center cursor-pointer"
                    >
                        Atur Jadwal Backup
                    </a>
                </div>
            </div>
        </section>

        {{-- Tabel Riwayat Backup --}}
        <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="p-5 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h3 class="text-lg font-bold text-gray-900">
                    Riwayat Backup Tersimpan
                </h3>
                <span class="text-sm font-medium text-gray-500">
                    Total {{ $totalFile ?? count($riwayatBackup) }} file backup ({{ $totalUkuran ?? '0 KB' }})
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-base">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase font-semibold text-gray-600 tracking-wider">
                            <th class="px-6 py-4">Waktu Backup</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4">Ukuran File</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-700">
                        @forelse ($riwayatBackup as $item)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    {{ $item->waktu }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md bg-gray-100 text-gray-700">
                                        {{ $item->tipe }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono font-medium text-gray-700">
                                    {{ $item->ukuran }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a
                                        href="{{ route('admin.backup.unduh', $item->nama_file) }}"
                                        class="touch-target text-emerald-700 hover:text-emerald-800 hover:underline font-medium min-h-[2.75rem] inline-flex items-center justify-end cursor-pointer"
                                    >
                                        Unduh
                                    </a>
                                    <button
                                        type="button"
                                        onclick="hapusCadangan('{{ $item->nama_file }}')"
                                        class="touch-target text-red-600 hover:text-red-800 hover:underline font-medium min-h-[2.75rem] inline-flex items-center justify-end cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-base">
                                    Belum ada riwayat cadangan database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($riwayatBackup instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $riwayatBackup->hasPages())
                <div class="p-4 border-t border-gray-200">
                    {{ $riwayatBackup->links() }}
                </div>
            @endif
        </section>

        {{-- Kotak Peringatan Bawah --}}
        <section class="bg-blue-50 border border-blue-200 text-blue-900 rounded-lg p-4 text-base mt-2">
            <div>
                <span class="font-bold">Pemberitahuan:</span>
                Backup disimpan di server lokal. Untuk keamanan tambahan, unduh file backup secara berkala ke perangkat terpisah.
            </div>
        </section>
    </main>

    {{-- Script Vanilla JavaScript Interaksi --}}
    <script>
        let notifTimer = null;
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

            if (notifTimer) clearTimeout(notifTimer);
            notifTimer = setTimeout(() => {
                notif.classList.add('hidden');
            }, 3500);
        }

        function jalankanBackup(btn) {
            if (!btn || btn.disabled) return;

            const teksAsli = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Sedang membuat cadangan database...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            fetch('{{ route('admin.backup.buat') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ tipe: 'Manual' })
            })
            .then(async (response) => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan saat memproses backup.');
                }
                return data;
            })
            .then((data) => {
                tampilkanNotif(data.message || 'Cadangan database berhasil dibuat!', 'sukses');
                setTimeout(() => {
                    window.location.reload();
                }, 1200);
            })
            .catch((error) => {
                tampilkanNotif(error.message || 'Gagal membuat cadangan database.', 'error');
                btn.disabled = false;
                btn.innerText = teksAsli;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            });
        }

        function hapusCadangan(filename) {
            if (!confirm('Apakah Anda yakin ingin menghapus berkas cadangan ini?')) {
                return;
            }

            fetch('{{ url('/admin/backup') }}/' + encodeURIComponent(filename), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(async (response) => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menghapus file cadangan.');
                }
                return data;
            })
            .then((data) => {
                tampilkanNotif(data.message || 'Berkas cadangan berhasil dihapus.', 'sukses');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .catch((error) => {
                tampilkanNotif(error.message || 'Gagal menghapus berkas cadangan.', 'error');
            });
        }
    </script>
</body>
</html>
