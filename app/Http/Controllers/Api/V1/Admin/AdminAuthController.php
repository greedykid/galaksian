<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $login = $request->validated('login');
        $password = $request->validated('password');

        // Normalisasi phone 0... -> 62... agar konsisten dengan OtpService.
        $normalizedPhone = preg_replace('/\D+/', '', (string) $login) ?? '';
        if (str_starts_with($normalizedPhone, '0')) {
            $normalizedPhone = '62'.substr($normalizedPhone, 1);
        }

        $user = User::where('email', $login)->orWhere('phone', $login)->orWhere('phone', $normalizedPhone)->first();

        // Pesan seragam untuk kredensial salah MAUPUN role non-admin:
        // mencegah oracle 401 vs 403 untuk enumerasi kredensial + role.
        if (! $user || ! Hash::check($password, $user->password)) {
            throw new BusinessException('Kredensial login admin tidak valid.', 401);
        }

        if (! $user->isAdmin() && ! $user->isCs()) {
            throw new BusinessException('Kredensial login admin tidak valid.', 401);
        }

        $user->update(['last_login_at' => now()]);
        // Token admin: ability admin, expiry 12 jam.
        $token = $user->createToken('admin-token', ['admin'], now()->addHours(12))->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => new UserResource($user),
            'must_change_password' => (bool) $user->must_change_password,
        ], 'Login admin berhasil.');
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->password && ! Hash::check($request->validated('current_password'), $user->password)) {
            throw new BusinessException('Kata sandi lama yang Anda masukkan tidak sesuai.', 422);
        }

        $user->update([
            'password' => Hash::make($request->validated('password')),
            'must_change_password' => false,
            'last_login_at' => now(),
        ]);
        $currentTokenId = $user->currentAccessToken()?->getKey();
        $user->tokens()->when($currentTokenId, fn ($query) => $query->where('id', '!=', $currentTokenId))->delete();

        return $this->successResponse([
            'user' => new UserResource($user->fresh()),
        ], 'Kata sandi berhasil diperbarui.');
    }
}
