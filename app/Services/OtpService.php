<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Exceptions\BusinessException;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    private const MAX_REQUEST_PER_15_MIN = 5;

    private const RESEND_COOLDOWN_SECONDS = 60;

    private const MAX_VERIFY_ATTEMPTS = 3;

    private const VERIFY_LOCKOUT_MINUTES = 10;

    public function normalizePhone(string $phone): string
    {
        $cleanPhone = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        return $cleanPhone;
    }

    public function requestOtp(string $phone, string $purpose = 'login', ?string $ipAddress = null): array
    {
        $cleanPhone = $this->normalizePhone($phone);

        if (strlen($cleanPhone) < 10) {
            throw new BusinessException('Nomor handphone tidak valid.');
        }

        // Per-phone rate limit: max 5 request / 15 menit (anti brute-force rotasi IP).
        $requestKey = "otp:req:{$cleanPhone}";
        $requestCount = (int) Cache::get($requestKey, 0);
        if ($requestCount >= self::MAX_REQUEST_PER_15_MIN) {
            throw new BusinessException('Terlalu banyak permintaan OTP. Coba lagi 15 menit.', 429);
        }

        // Cooldown resend 60 detik per nomor.
        $cooldownKey = "otp:cooldown:{$cleanPhone}";
        if (Cache::has($cooldownKey)) {
            throw new BusinessException('Tunggu sebentar sebelum meminta kode baru.', 429);
        }

        // Batalkan OTP aktif sebelumnya yang belum expired
        OtpCode::where('phone', $cleanPhone)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->update(['consumed_at' => now()]);

        // Generate 6 digit OTP (gunakan 123456 di local jika testing/demo)
        $code = app()->environment('local', 'testing') ? '123456' : (string) random_int(100000, 999999);

        $otp = OtpCode::create([
            'phone' => $cleanPhone,
            'code_hash' => Hash::make($code),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'ip_address' => $ipAddress,
        ]);

        Cache::put($requestKey, $requestCount + 1, now()->addMinutes(15));
        Cache::put($cooldownKey, true, now()->addSeconds(self::RESEND_COOLDOWN_SECONDS));

        return [
            'phone' => $cleanPhone,
            'expires_at' => $otp->expires_at->toIso8601String(),
            'code' => app()->environment('local', 'testing') ? $code : null,
            'message' => 'Kode OTP berhasil dikirim ke nomor Anda.',
        ];
    }

    public function verifyOtp(string $phone, string $code): array
    {
        $cleanPhone = $this->normalizePhone($phone);

        // Lockout per-phone setelah 3x gagal beruntun (10 menit).
        $lockKey = "otp:lock:{$cleanPhone}";
        if (Cache::has($lockKey)) {
            throw new BusinessException('Terlalu banyak percobaan salah. Coba lagi 10 menit.', 429);
        }

        $otp = OtpCode::where('phone', $cleanPhone)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        // Pesan seragam agar tidak menjadi oracle ada/tidaknya OTP aktif.
        if (! $otp) {
            throw new BusinessException('Kode OTP tidak valid atau kadaluarsa.');
        }

        if ($otp->attempts >= self::MAX_VERIFY_ATTEMPTS) {
            $otp->update(['consumed_at' => now()]);
            Cache::put($lockKey, true, now()->addMinutes(self::VERIFY_LOCKOUT_MINUTES));
            throw new BusinessException('Batas percobaan OTP telah terlampaui. Silakan minta kode baru.');
        }

        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');
            if ($otp->fresh()->attempts >= self::MAX_VERIFY_ATTEMPTS) {
                Cache::put($lockKey, true, now()->addMinutes(self::VERIFY_LOCKOUT_MINUTES));
            }
            throw new BusinessException('Kode OTP tidak valid atau kadaluarsa.');
        }

        $otp->update(['consumed_at' => now()]);
        Cache::forget($lockKey);

        $user = User::firstOrCreate(
            ['phone' => $cleanPhone],
            [
                'name' => 'User '.substr($cleanPhone, -4),
                'role' => UserRole::USER,
                'language' => 'id',
                'is_new_user' => true,
            ]
        );

        $user->update(['last_login_at' => now()]);

        // Token user: ability user, expiry 30 hari (43200 menit).
        $token = $user->createToken('mobile-web-auth', ['user'], now()->addMinutes(43200))->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }
}
