<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class OtpService
{
    private const TTL = 300; // 5 menit
    private const RESEND_COOLDOWN = 90; // detik
    private const MAX_ATTEMPTS = 5;

    public function generate(int $userId): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put("otp:login:{$userId}", $code, self::TTL);
        Cache::put("otp:attempts:{$userId}", 0, self::TTL);
        Cache::put("otp:resent:{$userId}", now()->timestamp, self::RESEND_COOLDOWN);
        return $code;
    }

    public function verify(int $userId, string $inputCode): bool|string
    {
        $attempts = (int) Cache::get("otp:attempts:{$userId}", 0);
        if ($attempts >= self::MAX_ATTEMPTS) {
            $this->invalidate($userId);
            return 'max_attempts';
        }

        $storedCode = Cache::get("otp:login:{$userId}");
        if (! $storedCode) {
            return 'expired';
        }

        if (! hash_equals($storedCode, $inputCode)) {
            Cache::put("otp:attempts:{$userId}", $attempts + 1, self::TTL);
            return 'invalid';
        }

        $this->invalidate($userId);
        return true;
    }

    public function invalidate(int $userId): void
    {
        Cache::forget("otp:login:{$userId}");
        Cache::forget("otp:attempts:{$userId}");
        Cache::forget("otp:resent:{$userId}");
    }

    public function canResend(int $userId): bool
    {
        $lastResent = Cache::get("otp:resent:{$userId}");
        if (! $lastResent) {
            return true;
        }
        return now()->timestamp - $lastResent >= self::RESEND_COOLDOWN;
    }
}
