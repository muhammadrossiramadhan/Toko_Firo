{{-- Sidebar Navigasi Admin Terpusat (Kepatuhan K12 & K18) --}}
<aside class="w-64 bg-[#08331e] text-white flex flex-col shrink-0 min-h-screen p-4 justify-between">
    <div class="space-y-6">
        {{-- Brand Card Toko Firo (Tanpa Ikon Grafis / SVG / Emoji) --}}
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
                {{-- 1. Dashboard --}}
                <a
                    href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin/dashboard') }}"
                    class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors {{ request()->routeIs('admin.dashboard*') || request()->is('admin/dashboard*') ? 'bg-emerald-600 text-white font-semibold text-base shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}"
                >
                    Dashboard
                </a>

                {{-- 2. Laporan Penjualan --}}
                <a
                    href="{{ Route::has('admin.laporan.penjualan') ? route('admin.laporan.penjualan') : (Route::has('admin.laporan') ? route('admin.laporan') : url('/admin/laporan-penjualan')) }}"
                    class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors {{ request()->routeIs('admin.laporan*') || request()->is('admin/laporan*') ? 'bg-emerald-600 text-white font-semibold text-base shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}"
                >
                    Laporan Penjualan
                </a>

                {{-- 3. Kelola Data Barang --}}
                <a
                    href="{{ Route::has('admin.barang') ? route('admin.barang') : url('/admin/barang') }}"
                    class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors {{ request()->routeIs('admin.barang*') || request()->is('admin/barang*') ? 'bg-emerald-600 text-white font-semibold text-base shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}"
                >
                    Kelola Data Barang
                </a>

                {{-- 4. Pembelian Barang --}}
                <a
                    href="{{ Route::has('admin.pembelian') ? route('admin.pembelian') : url('/admin/pembelian') }}"
                    class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors {{ request()->routeIs('admin.pembelian*') || request()->is('admin/pembelian*') ? 'bg-emerald-600 text-white font-semibold text-base shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}"
                >
                    Pembelian Barang
                </a>

                {{-- 5. Kelola Akun Pengguna --}}
                <a
                    href="{{ Route::has('admin.pengguna') ? route('admin.pengguna') : (Route::has('admin.akun') ? route('admin.akun') : url('/admin/pengguna')) }}"
                    class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors {{ request()->routeIs('admin.pengguna*') || request()->routeIs('admin.akun*') || request()->is('admin/pengguna*') || request()->is('admin/akun*') ? 'bg-emerald-600 text-white font-semibold text-base shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}"
                >
                    Kelola Akun Pengguna
                </a>

                {{-- 6. Batas Stok Minimum --}}
                <a
                    href="{{ Route::has('admin.batas-stok') ? route('admin.batas-stok') : url('/admin/batas-stok') }}"
                    class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors {{ request()->routeIs('admin.batas-stok*') || request()->is('admin/batas-stok*') ? 'bg-emerald-600 text-white font-semibold text-base shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}"
                >
                    Batas Stok Minimum
                </a>

                {{-- 7. Backup Data --}}
                <a
                    href="{{ Route::has('admin.backup') ? route('admin.backup') : url('/admin/backup') }}"
                    class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors {{ request()->routeIs('admin.backup*') || request()->is('admin/backup*') ? 'bg-emerald-600 text-white font-semibold text-base shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}"
                >
                    Backup Data
                </a>

                {{-- 8. Log Aktivitas --}}
                <a
                    href="{{ Route::has('admin.log') ? route('admin.log') : url('/admin/log-aktivitas') }}"
                    class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors {{ request()->routeIs('admin.log*') || request()->is('admin/log*') ? 'bg-emerald-600 text-white font-semibold text-base shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}"
                >
                    Log Aktivitas
                </a>

                {{-- 9. Pengaturan Notifikasi --}}
                <a
                    href="{{ Route::has('admin.notifikasi') ? route('admin.notifikasi') : url('/admin/pengaturan-notifikasi') }}"
                    class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors {{ request()->routeIs('admin.notifikasi*') || request()->is('admin/pengaturan-notifikasi*') ? 'bg-emerald-600 text-white font-semibold text-base shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}"
                >
                    Pengaturan Notifikasi
                </a>

                {{-- 10. Pengaturan Toko --}}
                <a
                    href="{{ Route::has('admin.toko') ? route('admin.toko') : url('/admin/pengaturan-toko') }}"
                    class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-base transition-colors {{ request()->routeIs('admin.toko*') || request()->is('admin/pengaturan-toko*') || request()->is('admin/toko*') ? 'bg-emerald-600 text-white font-semibold text-base shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}"
                >
                    Pengaturan Toko
                </a>
            </nav>
        </div>

        {{-- Akses Kasir --}}
        <div>
            <div class="text-xs uppercase tracking-wider text-emerald-300 font-semibold px-3 mb-2">
                Akses Kasir
            </div>
            <nav class="space-y-1">
                <a href="#" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                    Transaksi Penjualan
                </a>
                <a href="#" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                    Lihat Stok Barang
                </a>
                <a href="#" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                    Saldo Harian
                </a>
                <a href="#" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-lg text-emerald-100 hover:text-white hover:bg-emerald-900/60 text-base">
                    Info Barang Baru
                </a>
            </nav>
        </div>
    </div>

    {{-- Profil Pengguna di Bawah Sidebar (Dapat diklik menuju profil) --}}
    <div class="pt-4 border-t border-emerald-900/60">
        <a href="{{ Route::has('admin.profil') ? route('admin.profil') : url('/admin/profil') }}" class="block bg-[#052314] hover:bg-emerald-800/60 rounded-xl p-3 mb-2 transition-colors cursor-pointer border border-emerald-600/40 shadow-sm">
            <div class="font-medium text-white text-base truncate">
                {{ Auth::user()?->nama ?? ($profil['nama'] ?? 'Admin / Pemilik') }}
            </div>
            <div class="text-sm text-emerald-300">
                Admin / Pemilik
            </div>
        </a>
        <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
            @csrf
            <button type="submit" class="touch-target w-full text-left flex items-center px-3 py-2 rounded-lg text-red-300 hover:text-white hover:bg-red-900/40 text-base transition-colors">
                Keluar
            </button>
        </form>
    </div>
</aside>

