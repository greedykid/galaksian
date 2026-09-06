<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AdminBrandController extends Controller
{
    public function index(): JsonResponse
    {
        $brands = Brand::withCount('products')->latest()->get();

        return $this->successResponse(
            BrandResource::collection($brands),
            'Daftar brand berhasil diambil.'
        );
    }

    public function store(StoreBrandRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $brand = Brand::create($data);

        return $this->successResponse(
            new BrandResource($brand),
            'Brand berhasil dibuat.',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $brand = Brand::withCount('products')->findOrFail($id);

        return $this->successResponse(
            new BrandResource($brand),
            'Detail brand berhasil diambil.'
        );
    }

    public function update(int $id, UpdateBrandRequest $request): JsonResponse
    {
        $brand = Brand::findOrFail($id);
        $brand->update($request->validated());

        return $this->successResponse(
            new BrandResource($brand->fresh()),
            'Brand berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return $this->successResponse(null, 'Brand berhasil dihapus.');
    }
}
