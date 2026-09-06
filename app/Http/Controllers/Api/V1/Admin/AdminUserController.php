<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($search = $request->query('q')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        $users = $query->latest()->paginate(20);

        return $this->successResponse(
            UserResource::collection($users)->response()->getData(true),
            'Daftar pengguna berhasil diambil.'
        );
    }

    public function show(int $id): JsonResponse
    {
        $user = User::with(['addresses', 'orders'])->findOrFail($id);

        return $this->successResponse(
            new UserResource($user),
            'Detail pengguna berhasil diambil.'
        );
    }

    public function update(int $id, UpdateUserRequest $request): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());

        return $this->successResponse(
            new UserResource($user->fresh()),
            'Data pengguna berhasil diperbarui.'
        );
    }
}
