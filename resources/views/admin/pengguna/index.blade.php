<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Akun Pengguna - Toko Firo</title>

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
        {{-- Header Halaman --}}
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Kelola Akun Pengguna
                </h1>
                <p class="text-gray-600 text-base mt-1">
                    Daftar pengguna dan hak akses sistem Toko Firo.
                </p>
            </div>
            <div>
                <button
                    type="button"
                    onclick="bukaModalTambah()"
                    class="touch-target px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-base shadow-sm transition-colors flex items-center justify-center whitespace-nowrap cursor-pointer"
                >
                    + Tambah Akun
                </button>
            </div>
        </header>

        {{-- Flash Message Alerts --}}
        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl text-base font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl text-base font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl text-base">
                <div class="font-bold mb-1">Terdapat kesalahan pengisian data:</div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Bar Pencarian & Filter --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('admin.pengguna.index') }}" class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') ?? request('keyword') ?? '' }}"
                        placeholder="Cari pengguna berdasarkan nama atau username..."
                        class="touch-target w-full px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>
                <div>
                    <select
                        name="role"
                        class="touch-target px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
                    >
                        <option value="">Semua Role</option>
                        <option value="ADMIN" {{ (request('role') ?? request('peran')) === 'ADMIN' ? 'selected' : '' }}>Admin</option>
                        <option value="KASIR" {{ (request('role') ?? request('peran')) === 'KASIR' ? 'selected' : '' }}>Kasir</option>
                    </select>
                </div>
                <div>
                    <select
                        name="status"
                        class="touch-target px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
                    >
                        <option value="">Semua Status</option>
                        <option value="AKTIF" {{ request('status') === 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                        <option value="NONAKTIF" {{ request('status') === 'NONAKTIF' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div>
                    <button
                        type="submit"
                        class="touch-target px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-medium rounded-lg text-base transition-colors cursor-pointer"
                    >
                        Filter
                    </button>
                    @if(request('q') || request('role') || request('status') || request('peran'))
                        <a
                            href="{{ route('admin.pengguna.index') }}"
                            class="touch-target inline-flex items-center px-4 py-2 text-slate-600 hover:text-slate-900 text-base ml-2"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Akun Pengguna --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-base">
                    <thead>
                        <tr class="bg-gray-50 text-gray-700 border-b border-gray-200">
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Nama Lengkap</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Username</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Role</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Status</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap">Login Terakhir</th>
                            <th class="py-3 px-4 font-semibold whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-800">
                        @forelse ($pengguna as $item)
                            @php
                                $isSelf = (int) $item->id_pengguna === (int) (auth()->id() ?? auth()->user()?->id_pengguna);
                                $roleDisplay = strtoupper($item->role ?? '') === 'ADMIN' ? 'Admin' : 'Kasir';
                                $statusDisplay = strtoupper($item->status ?? '') === 'AKTIF' ? 'Aktif' : 'Nonaktif';
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        {{-- Inisial Avatar --}}
                                        @php
                                            $bgInisial = match($item->inisial ?? 'A') {
                                                'J' => 'bg-emerald-600',
                                                'R' => 'bg-blue-600',
                                                default => 'bg-slate-600',
                                            };
                                        @endphp
                                        <div class="w-9 h-9 rounded-full {{ $bgInisial }} text-white font-bold flex items-center justify-center text-sm shrink-0">
                                            {{ $item->inisial ?? 'A' }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 flex items-center gap-2">
                                                <span>{{ $item->nama }}</span>
                                                @if ($isSelf)
                                                    <span class="text-xs px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-normal">Anda</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-mono text-gray-700">
                                    {{ $item->username }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if ($roleDisplay === 'Admin')
                                        <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-semibold text-sm">
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold text-sm">
                                            Kasir
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-medium">
                                    @if ($statusDisplay === 'Aktif')
                                        <span class="text-emerald-600">
                                            &bull; Aktif
                                        </span>
                                    @else
                                        <span class="text-gray-500">
                                            &bull; Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-gray-600">
                                    {{ $item->login_terakhir }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Tombol Ubah --}}
                                        <button
                                            type="button"
                                            onclick="bukaModalEdit({{ $item->id_pengguna }}, '{{ addslashes($item->nama) }}', '{{ addslashes($item->username) }}', '{{ strtolower($item->role ?? 'kasir') }}', '')"
                                            class="touch-target px-3 py-1.5 border border-blue-500 text-blue-600 hover:bg-blue-50 font-medium rounded-lg text-base flex items-center justify-center transition-colors cursor-pointer"
                                        >
                                            Ubah
                                        </button>

                                        {{-- Tombol Reset Password --}}
                                        <button
                                            type="button"
                                            onclick="bukaModalReset({{ $item->id_pengguna }}, '{{ addslashes($item->nama) }}', '{{ addslashes($item->username) }}')"
                                            class="touch-target px-3 py-1.5 border border-amber-500 text-amber-600 hover:bg-amber-50 font-medium rounded-lg text-base flex items-center justify-center transition-colors cursor-pointer"
                                        >
                                            Reset Password
                                        </button>

                                        {{-- Tombol Toggle Status --}}
                                        @if ($item->status === 'AKTIF')
                                            @if ($isSelf)
                                                <button
                                                    type="button"
                                                    disabled
                                                    title="Tidak dapat menonaktifkan akun sendiri"
                                                    class="touch-target px-3 py-1.5 border border-gray-300 text-gray-400 font-medium rounded-lg text-base flex items-center justify-center cursor-not-allowed bg-gray-50"
                                                >
                                                    Nonaktifkan
                                                </button>
                                            @else
                                                <button
                                                    type="button"
                                                    onclick="bukaModalStatus({{ $item->id_pengguna }}, '{{ addslashes($item->nama) }}', 'NONAKTIF')"
                                                    class="touch-target px-3 py-1.5 border border-red-500 text-red-600 hover:bg-red-50 font-medium rounded-lg text-base flex items-center justify-center transition-colors cursor-pointer"
                                                >
                                                    Nonaktifkan
                                                </button>
                                            @endif
                                        @else
                                            <button
                                                type="button"
                                                onclick="bukaModalStatus({{ $item->id_pengguna }}, '{{ addslashes($item->nama) }}', 'AKTIF')"
                                                class="touch-target px-3 py-1.5 border border-emerald-500 text-emerald-600 hover:bg-emerald-50 font-medium rounded-lg text-base flex items-center justify-center transition-colors cursor-pointer"
                                            >
                                                Aktifkan
                                            </button>
                                        @endif

                                        {{-- Tombol Hapus Akun --}}
                                        @if ($isSelf)
                                            <button
                                                type="button"
                                                disabled
                                                title="Tidak dapat menghapus akun sendiri"
                                                class="touch-target px-3 py-1.5 border border-gray-300 text-gray-400 font-medium rounded-lg text-base flex items-center justify-center cursor-not-allowed bg-gray-50"
                                            >
                                                Hapus
                                            </button>
                                        @else
                                            <button
                                                type="button"
                                                onclick="bukaModalHapus({{ $item->id_pengguna }}, '{{ addslashes($item->nama) }}', '{{ addslashes($item->username) }}')"
                                                class="touch-target px-3 py-1.5 border border-red-700 text-red-700 hover:bg-red-50 font-medium rounded-lg text-base flex items-center justify-center transition-colors cursor-pointer"
                                            >
                                                Hapus
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">
                                    Tidak ada data pengguna terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Tabel & Paginasi --}}
            <div class="p-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-gray-600 text-base">
                <div>
                    Menampilkan {{ $pengguna->firstItem() ?? 0 }} - {{ $pengguna->lastItem() ?? 0 }} dari {{ $pengguna->total() }} pengguna terdaftar
                </div>
                <div>
                    {{ $pengguna->links() }}
                </div>
            </div>
        </div>
    </main>

    {{-- ========================================================================= --}}
    {{-- MODAL 1: TAMBAH AKUN PENGGUNA --}}
    {{-- ========================================================================= --}}
    <div id="modalTambah" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-200">
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-4">
                <h3 class="text-xl font-bold text-gray-900">Tambah Akun Pengguna Baru</h3>
                <button type="button" onclick="tutupModal('modalTambah')" class="text-gray-400 hover:text-gray-600 font-bold text-xl px-2">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.pengguna.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="nama" required placeholder="Contoh: Rina Putri" class="touch-target w-full px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Username *</label>
                    <input type="text" name="username" required placeholder="Contoh: rina_kasir" class="touch-target w-full px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password Awal *</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter" class="touch-target w-full px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Peran / Role *</label>
                    <select name="peran" required class="touch-target w-full px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="kasir">Kasir</option>
                        <option value="admin">Admin</option>
                        <option value="pemilik">Pemilik</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon (Opsional)</label>
                    <input type="text" name="nomor_telepon" placeholder="Contoh: 081234567890" class="touch-target w-full px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-200">
                    <button type="button" onclick="tutupModal('modalTambah')" class="touch-target px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 font-medium">Batal</button>
                    <button type="submit" class="touch-target px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL 2: EDIT AKUN PENGGUNA --}}
    {{-- ========================================================================= --}}
    <div id="modalEdit" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-200">
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-4">
                <h3 class="text-xl font-bold text-gray-900">Ubah Data Akun Pengguna</h3>
                <button type="button" onclick="tutupModal('modalEdit')" class="text-gray-400 hover:text-gray-600 font-bold text-xl px-2">&times;</button>
            </div>
            <form id="formEdit" method="POST" action="" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap *</label>
                    <input type="text" id="editNama" name="nama" required class="touch-target w-full px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Username *</label>
                    <input type="text" id="editUsername" name="username" required class="touch-target w-full px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Peran / Role *</label>
                    <select id="editPeran" name="peran" required class="touch-target w-full px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="kasir">Kasir</option>
                        <option value="admin">Admin</option>
                        <option value="pemilik">Pemilik</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon (Opsional)</label>
                    <input type="text" id="editTelepon" name="nomor_telepon" class="touch-target w-full px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-200">
                    <button type="button" onclick="tutupModal('modalEdit')" class="touch-target px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 font-medium">Batal</button>
                    <button type="submit" class="touch-target px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL 3: RESET PASSWORD --}}
    {{-- ========================================================================= --}}
    <div id="modalReset" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-200">
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-4">
                <h3 class="text-xl font-bold text-gray-900">Reset Password Pengguna</h3>
                <button type="button" onclick="tutupModal('modalReset')" class="text-gray-400 hover:text-gray-600 font-bold text-xl px-2">&times;</button>
            </div>
            <p class="text-base text-gray-600 mb-4">
                Atur ulang password untuk akun: <strong id="resetTargetNama" class="text-gray-900"></strong> (<span id="resetTargetUsername" class="font-mono"></span>)
            </p>
            <form id="formReset" method="POST" action="" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password Baru *</label>
                    <input type="password" name="password_baru" required minlength="6" placeholder="Minimal 6 karakter" class="touch-target w-full px-4 py-2 border border-gray-300 rounded-lg text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-200">
                    <button type="button" onclick="tutupModal('modalReset')" class="touch-target px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 font-medium">Batal</button>
                    <button type="submit" class="touch-target px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg">Reset Password</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL 4: TOGGLE STATUS (NONAKTIFKAN / AKTIFKAN) --}}
    {{-- ========================================================================= --}}
    <div id="modalStatus" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-200">
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-4">
                <h3 class="text-xl font-bold text-gray-900" id="statusModalJudul">Ubah Status Akun</h3>
                <button type="button" onclick="tutupModal('modalStatus')" class="text-gray-400 hover:text-gray-600 font-bold text-xl px-2">&times;</button>
            </div>
            <p class="text-base text-gray-700 mb-4" id="statusModalPesan">
                Apakah Anda yakin ingin mengubah status akun ini?
            </p>
            <form id="formStatus" method="POST" action="" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" id="statusTargetValue" name="status" value="">
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-200">
                    <button type="button" onclick="tutupModal('modalStatus')" class="touch-target px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 font-medium">Batal</button>
                    <button type="submit" id="statusSubmitBtn" class="touch-target px-5 py-2 text-white font-medium rounded-lg">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL 5: HAPUS AKUN PENGGUNA --}}
    {{-- ========================================================================= --}}
    <div id="modalHapus" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-200">
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-4">
                <h3 class="text-xl font-bold text-red-700">Hapus Akun Pengguna</h3>
                <button type="button" onclick="tutupModal('modalHapus')" class="text-gray-400 hover:text-gray-600 font-bold text-xl px-2">&times;</button>
            </div>
            <p class="text-base text-gray-700 mb-2">
                Apakah Anda yakin ingin menghapus akun <strong id="hapusTargetNama" class="text-gray-900"></strong> (<span id="hapusTargetUsername" class="font-mono"></span>)?
            </p>
            <p class="text-sm text-gray-500 mb-4">
                Tindakan ini tidak dapat dibatalkan. Jika akun telah memiliki riwayat transaksi, sistem akan menolak penghapusan untuk melindungi integritas data toko.
            </p>
            <form id="formHapus" method="POST" action="" class="space-y-4">
                @csrf
                @method('DELETE')
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-200">
                    <button type="button" onclick="tutupModal('modalHapus')" class="touch-target px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 font-medium">Batal</button>
                    <button type="submit" class="touch-target px-5 py-2 bg-red-700 hover:bg-red-800 text-white font-medium rounded-lg">Ya, Hapus Akun</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script JavaScript Interaktif Modal --}}
    <script>
        function bukaModalTambah() {
            document.getElementById('modalTambah').classList.remove('hidden');
        }

        function bukaModalEdit(id, nama, username, peran, telepon) {
            document.getElementById('formEdit').action = '/admin/pengguna/' + id;
            document.getElementById('editNama').value = nama;
            document.getElementById('editUsername').value = username;
            document.getElementById('editPeran').value = peran;
            document.getElementById('editTelepon').value = telepon || '';
            document.getElementById('modalEdit').classList.remove('hidden');
        }

        function bukaModalReset(id, nama, username) {
            document.getElementById('formReset').action = '/admin/pengguna/' + id + '/reset-password';
            document.getElementById('resetTargetNama').textContent = nama;
            document.getElementById('resetTargetUsername').textContent = username;
            document.getElementById('modalReset').classList.remove('hidden');
        }

        function bukaModalStatus(id, nama, targetStatus) {
            document.getElementById('formStatus').action = '/admin/pengguna/' + id + '/status';
            document.getElementById('statusTargetValue').value = targetStatus;

            const judul = document.getElementById('statusModalJudul');
            const pesan = document.getElementById('statusModalPesan');
            const btn = document.getElementById('statusSubmitBtn');

            if (targetStatus === 'NONAKTIF') {
                judul.textContent = 'Nonaktifkan Akun Pengguna';
                pesan.innerHTML = 'Apakah Anda yakin ingin menonaktifkan akun <strong>' + nama + '</strong>? Pengguna tidak akan dapat login lagi sampai diaktifkan kembali.';
                btn.className = 'touch-target px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg';
                btn.textContent = 'Nonaktifkan';
            } else {
                judul.textContent = 'Aktifkan Kembali Akun Pengguna';
                pesan.innerHTML = 'Apakah Anda yakin ingin mengaktifkan kembali akun <strong>' + nama + '</strong>? Pengguna dapat login kembali ke sistem.';
                btn.className = 'touch-target px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg';
                btn.textContent = 'Aktifkan';
            }

            document.getElementById('modalStatus').classList.remove('hidden');
        }

        function bukaModalHapus(id, nama, username) {
            document.getElementById('formHapus').action = '/admin/pengguna/' + id;
            document.getElementById('hapusTargetNama').textContent = nama;
            document.getElementById('hapusTargetUsername').textContent = username;
            document.getElementById('modalHapus').classList.remove('hidden');
        }

        function tutupModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Tutup modal jika user klik di luar kotak modal
        window.onclick = function(event) {
            const modals = ['modalTambah', 'modalEdit', 'modalReset', 'modalStatus', 'modalHapus'];
            modals.forEach(id => {
                const el = document.getElementById(id);
                if (event.target === el) {
                    el.classList.add('hidden');
                }
            });
        };
    </script>
</body>
</html>
