<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Exceptions\BusinessException;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    public function requestOtp(string $phone, string $purpose = 'login', ?string $ipAddress = null): array
    {
        $cleanPhone = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        if (strlen($cleanPhone) < 10) {
            throw new BusinessException('Nomor handphone tidak valid.');
        }

        // Batalkan OTP aktif sebelumnya yang belum expired
        OtpCode::where('phone', $cleanPhone)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->update(['consumed_at' => now()]);

        // Generate 6 digit OTP (gunakan 123456 di local jika testing/demo)
        $code = app()->environment('local', 'testing') ? '123456' : (string) rand(100000, 999999);

        $otp = OtpCode::create([
            'phone' => $cleanPhone,
            'code_hash' => Hash::make($code),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'ip_address' => $ipAddress,
        ]);

        return [
            'phone' => $cleanPhone,
            'expires_at' => $otp->expires_at->toIso8601String(),
            'code' => app()->environment('local', 'testing') ? $code : null,
            'message' => 'Kode OTP berhasil dikirim ke nomor Anda.',
        ];
    }

    public function verifyOtp(string $phone, string $code): array
    {
        $cleanPhone = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        $otp = OtpCode::where('phone', $cleanPhone)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp) {
            throw new BusinessException('Kode OTP sudah kadaluarsa atau tidak ditemukan.');
        }

        if ($otp->attempts >= 3) {
            $otp->update(['consumed_at' => now()]);
            throw new BusinessException('Batas percobaan OTP telah terlampaui. Silakan minta kode baru.');
        }

        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');
            throw new BusinessException('Kode OTP yang Anda masukkan salah.');
        }

        $otp->update(['consumed_at' => now()]);

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

        $token = $user->createToken('mobile-web-auth')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }
}
