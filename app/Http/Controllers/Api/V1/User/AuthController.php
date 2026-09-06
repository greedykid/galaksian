<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\OtpRequestRequest;
use App\Http\Requests\User\OtpVerifyRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        protected OtpService $otpService
    ) {}

    public function requestOtp(OtpRequestRequest $request): JsonResponse
    {
        $result = $this->otpService->requestOtp(
            $request->validated('phone'),
            $request->validated('purpose', 'login'),
            $request->ip()
        );

        return $this->successResponse($result, 'Kode OTP berhasil dikirim.');
    }

    public function verifyOtp(OtpVerifyRequest $request): JsonResponse
    {
        $result = $this->otpService->verifyOtp(
            $request->validated('phone'),
            $request->validated('code')
        );

        return $this->successResponse([
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
        ], 'Verifikasi OTP berhasil.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user()),
            'Profil user berhasil diambil.'
        );
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return $this->successResponse(
            new UserResource($user->fresh()),
            'Profil berhasil diperbarui.'
        );
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->password && $request->filled('current_password')) {
            if (! Hash::check($request->validated('current_password'), $user->password)) {
                return $this->errorResponse('Password lama yang Anda masukkan tidak sesuai.', 422);
            }
        }

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return $this->successResponse(null, 'Password berhasil diperbarui.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse(null, 'Berhasil keluar (logout).');
    }
}
