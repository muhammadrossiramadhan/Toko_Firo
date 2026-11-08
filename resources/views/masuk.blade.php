@extends('layouts.auth')

@section('title', 'Masuk - Toko Firo')

@section('content')
<div class="max-w-[420px] w-full px-6 lg:px-10">
    <h2 class="font-heading font-bold text-[32px] leading-[40px] text-ink">Masuk</h2>
    <p class="text-text-muted mt-4">Khusus staf Toko Firo</p>

    @if ($errors->has('login'))
        <div class="mt-4 p-3 bg-danger-50 border border-danger/20 text-danger text-sm rounded-none">
            {{ $errors->first('login') }}
        </div>
    @endif

    <form method="POST" action="{{ route('masuk.proses') }}" class="mt-8 space-y-6">
        @csrf

        <div>
            <label for="username" class="block text-sm font-medium text-ink">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                value="{{ old('username') }}"
                placeholder="Masukkan username"
                required
                class="w-full h-[43px] mt-1.5 px-4 border border-border rounded-none bg-surface text-ink placeholder:text-text-disabled focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-50 transition"
            >
            @error('username')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password-input" class="block text-sm font-medium text-ink">Password</label>
            <div class="relative mt-1.5">
                <input
                    type="password"
                    id="password-input"
                    name="password"
                    placeholder="Masukkan password"
                    required
                    class="w-full min-h-[2.75rem] h-11 px-4 pr-16 border border-border rounded-none bg-surface text-ink placeholder:text-text-disabled focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-50 transition"
                >
                <button
                    type="button"
                    id="toggle-password"
                    class="touch-target absolute inset-y-0 right-0 flex items-center pr-3 text-xs font-semibold text-emerald-700 hover:text-emerald-900 focus:outline-none min-h-[2.75rem] cursor-pointer z-10"
                    aria-label="Toggle password visibility"
                >
                    Lihat
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="touch-target w-full min-h-[2.75rem] h-[47px] bg-green-700 text-white font-medium rounded-none hover:bg-green-800 transition cursor-pointer"
        >
            Masuk
        </button>
    </form>

    <div class="mt-6 space-y-3">
        <p class="text-sm text-text-muted">Lupa password? Hubungi pemilik toko.</p>
        <div>
            <a href="/" data-turbo="false" class="touch-target text-sm text-green-700 hover:text-green-800 transition inline-flex items-center min-h-[2.75rem]">Kembali ke beranda</a>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggle-password')?.addEventListener('click', function() {
        const input = document.getElementById('password-input');
        if (input) {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? 'Tutup' : 'Lihat';
        }
    });
</script>
@endsection
