@extends('layouts.auth')

@section('title', 'Masuk - Toko Firo')

@section('content')
<div class="max-w-[420px] w-full px-10">
    <h2 class="font-heading font-bold text-[32px] leading-[40px] text-ink">Masuk</h2>
    <p class="text-text-muted mt-4">Khusus staf Toko Firo</p>

    <form method="POST" action="/masuk" class="mt-8 space-y-6">
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
                class="w-full h-[43px] mt-1.5 px-4 border border-border rounded-lg bg-surface text-ink placeholder:text-text-disabled focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-50 transition"
            >
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
                    class="w-full h-[43px] px-4 pr-10 border border-border rounded-lg bg-surface text-ink placeholder:text-text-disabled focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-50 transition"
                >
                <button
                    type="button"
                    id="toggle-password"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-text-muted hover:text-ink focus:outline-none cursor-pointer"
                    aria-label="Toggle password visibility"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
        </div>

        <button
            type="submit"
            class="w-full h-[47px] bg-green-700 text-white font-medium rounded-lg hover:bg-green-800 transition cursor-pointer"
        >
            Masuk
        </button>
    </form>

    <div class="mt-6 space-y-3">
        <p class="text-sm text-text-muted">Lupa password? Hubungi pemilik toko.</p>
        <div>
            <a href="/" class="text-sm text-green-700 hover:text-green-800 transition inline-block">← Kembali ke beranda</a>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggle-password')?.addEventListener('click', function() {
        const input = document.getElementById('password-input');
        if (input) {
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    });
</script>
@endsection
