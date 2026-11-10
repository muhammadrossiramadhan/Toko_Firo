{{-- Komponen Reusable Profil Pengguna & Logout di Sidebar Bawah --}}
@php
    $user = Auth::user();
    $nama = $user?->nama ?? $user?->username ?? 'Pengguna';
    $role = $user?->role ?? '';
    $isAdminOrOwner = in_array(strtoupper((string) $role), ['ADMIN', 'PEMILIK']);
    $roleLabel = $isAdminOrOwner ? 'Admin / Pemilik' : ($role ?: 'Kasir');
    $initial = strtoupper(substr((string) $nama, 0, 1)) ?: ($isAdminOrOwner ? 'A' : 'K');
    $profilUrl = Route::has('admin.profil') ? route('admin.profil') : url('/admin/profil');
    $logoutUrl = Route::has('logout') ? route('logout') : url('/logout');
@endphp

<div class="shrink-0 mt-auto pt-4 border-t border-emerald-900/60">
    @if ($isAdminOrOwner)
        <a href="{{ $profilUrl }}" class="block bg-[#052314] hover:bg-emerald-800/60 rounded-none p-3 mb-2 transition-colors cursor-pointer shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none text-lg">
                    {{ $initial }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-medium text-white text-base truncate">
                        {{ $nama }}
                    </div>
                    <div class="text-sm text-emerald-300">
                        {{ $roleLabel }}
                    </div>
                </div>
            </div>
        </a>
    @else
        <div class="bg-[#052314] rounded-none p-3 mb-2 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center font-bold text-center bg-emerald-700 text-white rounded-none text-lg">
                {{ $initial }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="font-medium text-white text-base truncate">
                    {{ $nama }}
                </div>
                <div class="text-sm text-emerald-300">
                    {{ $roleLabel }}
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ $logoutUrl }}" data-turbo="false">
        @csrf
        <button type="submit" class="touch-target w-full text-left flex items-center px-3 py-2 rounded-none text-red-300 hover:text-white hover:bg-red-900/40 text-base transition-colors min-h-[2.75rem] cursor-pointer">
            Keluar
        </button>
    </form>
</div>
