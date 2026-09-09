<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\OtpRequestRequest;
use App\Http\Requests\User\OtpVerifyRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UploadAvatarRequest;
use App\Http\Resources\UserResource;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $user = $request->user();

        // Delete old avatar file if it exists in storage
        if ($user->avatar_url) {
            $oldPath = str_replace('/storage/', '', $user->avatar_url);
            Storage::disk('public')->delete($oldPath);
        }

        $file = $request->file('avatar');
        // Gunakan ekstensi dari MIME yang telah divalidasi (bukan nama file client)
        $extension = $file->extension() ?: $file->guessExtension() ?: 'jpg';
        $filename = 'user-'.$user->id.'-'.time().'.'.$extension;
        $path = $file->storeAs('avatars', $filename, 'public');

        $user->update([
            'avatar_url' => '/storage/'.$path,
        ]);

        return $this->successResponse(
            new UserResource($user->fresh()),
            'Foto profil berhasil diperbarui.'
        );
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->password && ! Hash::check($request->validated('current_password'), $user->password)) {
            return $this->errorResponse('Password lama yang Anda masukkan tidak sesuai.', 422);
        }

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);
        $currentTokenId = $user->currentAccessToken()?->getKey();
        $user->tokens()->when($currentTokenId, fn ($query) => $query->where('id', '!=', $currentTokenId))->delete();

        return $this->successResponse(null, 'Password berhasil diperbarui.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse(null, 'Berhasil keluar (logout).');
    }
}
