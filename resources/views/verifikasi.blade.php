@extends('layouts.auth')

@section('title', 'Verifikasi Kode - Toko Firo')

@section('content')
<div class="max-w-[460px] w-full px-6 lg:px-10 text-center">
    <div class="w-[60px] h-[56px] mx-auto bg-green-50 rounded-2xl flex items-center justify-center">
        <svg class="w-8 h-8 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
        </svg>
    </div>

    <h2 class="font-heading font-bold text-[24px] leading-[30px] text-ink mt-6">Verifikasi Kode</h2>
    <p class="text-text-muted mt-4 text-sm max-w-[300px] mx-auto">Masukkan kode 6 digit yang dikirim ke grup Telegram untuk <span class="font-medium text-ink">@{{ $username }}</span></p>

    @if (session('telegram_warning'))
        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded-lg">
            {{ session('telegram_warning') }}
        </div>
    @endif

    @if (session('resent'))
        <div class="mt-4 p-3 bg-green-50 border border-green-700/20 text-green-800 text-sm rounded-lg">
            {{ session('resent') }}
        </div>
    @endif

    @if ($errors->has('code'))
        <div class="mt-4 p-3 bg-danger-50 border border-danger/20 text-danger text-sm rounded-lg">
            {{ $errors->first('code') }}
        </div>
    @endif

    <form method="POST" action="/verifikasi" class="mt-8" id="otp-form">
        @csrf
        <input type="hidden" name="code" id="otp-code">

        <div class="flex justify-center gap-2">
            @for ($i = 1; $i <= 6; $i++)
                <input
                    type="text"
                    data-otp="{{ $i }}"
                    maxlength="1"
                    inputmode="numeric"
                    pattern="[0-9]"
                    autocomplete="one-time-code"
                    required
                    class="w-12 h-14 border border-border rounded-lg text-center font-heading font-bold text-2xl text-ink focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-50 transition"
                >
            @endfor
        </div>

        <p class="text-sm text-text-muted mt-4" id="otp-timer">Kode berlaku 05:00</p>

        <button
            type="submit"
            class="w-full max-w-[378px] mx-auto mt-6 h-[47px] bg-green-700 text-white font-medium rounded-lg hover:bg-green-800 transition block cursor-pointer"
        >
            Verifikasi
        </button>
    </form>

    <div class="mt-6 space-y-3">
        <div>
            <form method="POST" action="{{ route('verifikasi.resend') }}" class="inline">
                @csrf
                <button type="submit" id="resend-btn" disabled class="text-sm text-text-disabled font-medium cursor-not-allowed">Kirim ulang (90s)</button>
            </form>
            @if ($errors->has('resend'))
                <p class="text-xs text-danger mt-1">{{ $errors->first('resend') }}</p>
            @endif
        </div>
        <div>
            <a href="/masuk" class="text-sm text-text-muted hover:text-ink transition inline-block">
                ← Kembali ke login
            </a>
        </div>
    </div>
</div>

<script>
    // OTP input auto-advance + paste support
    const otpInputs = document.querySelectorAll('[data-otp]');
    otpInputs.forEach((input, i) => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value && i < otpInputs.length - 1) otpInputs[i + 1].focus();
        });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && i > 0) otpInputs[i - 1].focus();
        });
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            for (let j = 0; j < paste.length && i + j < otpInputs.length; j++) {
                otpInputs[i + j].value = paste[j];
            }
            const lastIdx = Math.min(i + paste.length, otpInputs.length) - 1;
            otpInputs[lastIdx].focus();
        });
    });

    // Combine 6 inputs into hidden field on submit
    document.getElementById('otp-form').addEventListener('submit', function() {
        let code = '';
        otpInputs.forEach(input => code += input.value);
        document.getElementById('otp-code').value = code;
    });

    // Countdown timer (5 minutes)
    (function() {
        const timerEl = document.getElementById('otp-timer');
        const resendBtn = document.getElementById('resend-btn');
        if (!timerEl) return;
        let seconds = 300;
        function tick() {
            if (seconds <= 0) {
                timerEl.textContent = 'Kode kedaluwarsa';
                timerEl.classList.add('text-danger');
                return;
            }
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            timerEl.textContent = 'Kode berlaku ' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            seconds--;
            setTimeout(tick, 1000);
        }
        tick();

        // Resend cooldown (90s)
        let resendCooldown = 90;
        function resendTick() {
            if (resendCooldown <= 0) {
                resendBtn.disabled = false;
                resendBtn.textContent = 'Kirim ulang kode';
                resendBtn.classList.remove('text-text-disabled', 'cursor-not-allowed');
                resendBtn.classList.add('text-green-700', 'hover:text-green-800', 'cursor-pointer');
                return;
            }
            resendBtn.disabled = true;
            resendBtn.textContent = 'Kirim ulang (' + resendCooldown + 's)';
            resendBtn.classList.add('text-text-disabled', 'cursor-not-allowed');
            resendBtn.classList.remove('text-green-700', 'hover:text-green-800', 'cursor-pointer');
            resendCooldown--;
            setTimeout(resendTick, 1000);
        }
        resendTick();
    })();
</script>
@endsection
