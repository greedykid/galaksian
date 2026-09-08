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

        $user = User::where('email', $login)->orWhere('phone', $login)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new BusinessException('Kredensial login admin tidak valid.', 401);
        }

        if (! $user->isAdmin() && ! $user->isCs()) {
            throw new BusinessException('Anda tidak memiliki hak akses administrator.', 403);
        }

        $user->update(['last_login_at' => now()]);
        $token = $user->createToken('admin-token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => new UserResource($user),
            'must_change_password' => (bool) $user->must_change_password,
        ], 'Login admin berhasil.');
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->password && $request->filled('current_password')) {
            if (! Hash::check($request->validated('current_password'), $user->password)) {
                throw new BusinessException('Kata sandi lama yang Anda masukkan tidak sesuai.', 422);
            }
        }

        $user->update([
            'password' => Hash::make($request->validated('password')),
            'must_change_password' => false,
            'last_login_at' => now(),
        ]);

        return $this->successResponse([
            'user' => new UserResource($user->fresh()),
        ], 'Kata sandi berhasil diperbarui.');
    }
}
