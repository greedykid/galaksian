<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;

class AdminBannerController extends Controller
{
    public function index(): JsonResponse
    {
        $banners = Banner::orderBy('order')->get();

        return $this->successResponse(
            BannerResource::collection($banners),
            'Daftar banner berhasil diambil.'
        );
    }

    public function store(StoreBannerRequest $request): JsonResponse
    {
        $banner = Banner::create($request->validated());

        return $this->successResponse(
            new BannerResource($banner),
            'Banner berhasil dibuat.',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $banner = Banner::findOrFail($id);

        return $this->successResponse(
            new BannerResource($banner),
            'Detail banner berhasil diambil.'
        );
    }

    public function update(int $id, UpdateBannerRequest $request): JsonResponse
    {
        $banner = Banner::findOrFail($id);
        $banner->update($request->validated());

        return $this->successResponse(
            new BannerResource($banner->fresh()),
            'Banner berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return $this->successResponse(null, 'Banner berhasil dihapus.');
    }
}
