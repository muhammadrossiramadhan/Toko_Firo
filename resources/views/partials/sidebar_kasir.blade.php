{{-- 1. Satu Tombol Hamburger Eksklusif di Topbar / Header Utama Mobile (z-50) --}}
<header class="md:hidden w-full sticky top-0 z-50 flex items-center justify-between bg-[#08331e] text-white px-4 py-3 border-b border-emerald-900 shadow-md">
    <div class="flex items-center gap-3">
        {{-- Tombol Hamburger Menu di Pojok Kiri Atas sebelum teks TOKO FIRO --}}
        <button type="button" onclick="toggleSidebar()" class="touch-target px-3.5 py-2 bg-emerald-800 hover:bg-emerald-700 active:bg-emerald-900 text-white font-bold rounded-none flex items-center gap-2 cursor-pointer shadow-sm text-base" aria-label="Buka Menu Navigasi">
            <span style="font-size: 1.25rem; line-height: 1;">&#9776;</span>
            <span class="text-sm font-semibold">Menu</span>
        </button>
        {{-- Teks TOKO FIRO --}}
        <span class="font-bold text-lg tracking-wider text-white">TOKO FIRO</span>
    </div>
    {{-- User Avatar Initial --}}
    <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none text-sm shadow-sm">
        {{ strtoupper(substr(Auth::user()->nama ?? Auth::user()->username ?? 'K', 0, 1)) }}
    </div>
</header>

{{-- 2. Implementasi Overlay/Backdrop Gelap Menutupi Konten (z-[55]) --}}
<div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-[55] hidden transition-opacity backdrop-blur-sm cursor-pointer" aria-hidden="true"></div>

{{-- 3. Sidebar Navigasi Kasir (z-[60] di Mobile, fixed inset-y-0 left-0, Responsif Drawer di HP) --}}
<aside id="sidebarMenu" class="fixed inset-y-0 left-0 z-[60] w-72 max-w-[85vw] -translate-x-full md:translate-x-0 md:static md:w-64 md:min-h-screen md:sticky md:top-0 md:flex-shrink-0 flex flex-col justify-between bg-[#08331e] text-white overflow-y-auto p-4 transition-transform duration-300 ease-in-out shadow-2xl md:shadow-none outline-none focus:outline-none" tabindex="-1" style="outline: none;">
    <div class="space-y-6">
        {{-- Header Khusus Mobile untuk Menutup Drawer (Hanya tombol tutup ✕, tidak ada hamburger lagi) --}}
        <div class="flex items-center justify-between pb-3 md:hidden border-b border-emerald-800/80">
            <span class="text-xs uppercase tracking-wider text-emerald-300 font-bold">Navigasi Kasir</span>
            <button type="button" onclick="toggleSidebar()" class="touch-target px-3 py-1.5 bg-emerald-900/80 hover:bg-emerald-800 text-white rounded-none text-sm font-semibold cursor-pointer border border-emerald-700/50" aria-label="Tutup Menu">
                &#10005; Tutup
            </button>
        </div>

        <!-- Logo Area di Sidebar -->
        <div class="flex items-center justify-center py-6 border-b border-white/10">
            <img src="{{ asset('images/logo-footer.png') }}" 
                 alt="Toko Firo" 
                 class="object-contain h-12 w-auto" 
                 style="height: 48px; max-height: 48px; width: auto;">
        </div>

        @if (in_array(strtoupper((string) (Auth::user()?->role ?? '')), ['ADMIN', 'PEMILIK']))
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
                <a href="{{ route('kasir.dashboard') }}" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors {{ request()->routeIs('kasir.dashboard*') || request()->is('kasir/dashboard*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}">
                    Dashboard
                </a>
                <a href="{{ route('kasir.transaksi') }}" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors {{ request()->routeIs('kasir.transaksi*') || request()->is('kasir/transaksi*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}">
                    Transaksi Penjualan
                </a>
                <a href="{{ route('kasir.stok') }}" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors {{ request()->routeIs('kasir.stok*') || request()->is('kasir/stok*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}">
                    Lihat Stok Barang
                </a>
                <a href="{{ route('kasir.saldo') }}" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors {{ request()->routeIs('kasir.saldo*') || request()->is('kasir/saldo*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}">
                    Saldo
                </a>
                <a href="{{ route('kasir.info-barang') }}" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors {{ request()->routeIs('kasir.info-barang*') || request()->is('kasir/info-barang*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}">
                    Info Barang Baru
                </a>
                <a href="{{ route('kasir.pengembalian') }}" class="touch-target flex items-center min-h-[2.75rem] px-3 py-2.5 rounded-none text-base transition-colors {{ request()->routeIs('kasir.pengembalian*') || request()->is('kasir/pengembalian*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-100 hover:text-white hover:bg-emerald-900/60' }}">
                    Pengembalian Barang
                </a>
            </nav>
        </div>
    </div>

    {{-- Profil Pengguna & Logout --}}
    <div class="pt-4 border-t border-emerald-900/60">
        @if (in_array(strtoupper((string) (Auth::user()?->role ?? '')), ['ADMIN', 'PEMILIK']))
            <a href="{{ Route::has('admin.profil') ? route('admin.profil') : url('/admin/profil') }}" class="block bg-[#052314] hover:bg-emerald-800/60 rounded-none p-3 mb-2 transition-colors cursor-pointer shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none text-lg">
                        {{ strtoupper(substr((string) (Auth::user()?->nama ?? Auth::user()?->username ?? 'A'), 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-medium text-white text-base truncate">
                            {{ Auth::user()?->nama ?? Auth::user()?->username ?? 'Admin / Pemilik' }}
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
                    {{ strtoupper(substr((string) (Auth::user()?->nama ?? Auth::user()?->username ?? 'K'), 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-medium text-white text-base truncate">
                        {{ Auth::user()?->nama ?? Auth::user()?->username ?? 'Kasir' }}
                    </div>
                    <div class="text-sm text-emerald-300">
                        {{ Auth::user()?->role ?? 'Kasir' }}
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('logout') }}" data-turbo="false">
            @csrf
            <button type="submit" class="touch-target w-full text-left flex items-center px-3 py-2 rounded-none text-red-300 hover:text-white hover:bg-red-900/40 text-base transition-colors min-h-[2.75rem] cursor-pointer">
                Keluar
            </button>
        </form>
    </div>
</aside>

<script>
    function toggleSidebar() {
        var sidebar = document.getElementById('sidebarMenu');
        var backdrop = document.getElementById('sidebarBackdrop');
        if (!sidebar || !backdrop) return;
        var isClosed = sidebar.classList.contains('-translate-x-full');
        if (isClosed) {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var sidebar = document.getElementById('sidebarMenu');
            if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                toggleSidebar();
            }
        }
    });
</script>
