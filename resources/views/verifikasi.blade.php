@extends('layouts.auth')

@section('title', 'Verifikasi 2 Langkah - Toko Firo')

@section('content')
<div class="max-w-[460px] w-full px-10 text-center">
    <div class="w-[60px] h-[56px] mx-auto bg-green-50 rounded-2xl flex items-center justify-center">
        <svg class="w-8 h-8 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
        </svg>
    </div>

    <h2 class="font-heading font-bold text-[24px] leading-[30px] text-ink mt-6">Verifikasi 2 Langkah</h2>
    <p class="text-text-muted mt-4 text-sm max-w-[233px] mx-auto">Masukkan kode 6 digit yang dikirim ke Telegram akunmu</p>

    <form method="POST" action="/verifikasi" class="mt-8">
        @csrf

        <div class="flex justify-center gap-3">
            @for ($i = 1; $i <= 6; $i++)
                <input
                    type="text"
                    name="otp-{{ $i }}"
                    id="otp-{{ $i }}"
                    maxlength="1"
                    inputmode="numeric"
                    pattern="[0-9]"
                    autocomplete="one-time-code"
                    required
                    class="w-12 h-14 border border-border rounded-lg text-center font-heading font-bold text-2xl text-ink focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-50 transition"
                >
            @endfor
        </div>

        <p class="text-sm text-text-muted mt-4">Kode berlaku 04:59</p>

        <button
            type="submit"
            class="w-full max-w-[378px] mx-auto mt-6 h-[47px] bg-green-700 text-white font-medium rounded-lg hover:bg-green-800 transition block cursor-pointer"
        >
            Verifikasi
        </button>
    </form>

    <div class="mt-6 space-y-3">
        <div>
            <button type="button" class="text-sm text-green-700 hover:text-green-800 font-medium transition cursor-pointer">
                Kirim ulang kode
            </button>
        </div>
        <div>
            <a href="/masuk" class="text-sm text-text-muted hover:text-ink transition inline-block">
                ← Kembali ke login
            </a>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('[name^="otp-"]').forEach((input, i, inputs) => {
        input.addEventListener('input', function() {
            if (this.value && i < inputs.length - 1) inputs[i + 1].focus();
        });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && i > 0) inputs[i - 1].focus();
        });
    });
</script>
@endsection
