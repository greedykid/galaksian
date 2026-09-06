<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')->latest()->get();

        return $this->successResponse(
            CategoryResource::collection($categories),
            'Daftar kategori berhasil diambil.'
        );
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category = Category::create($data);

        return $this->successResponse(
            new CategoryResource($category),
            'Kategori berhasil dibuat.',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $category = Category::withCount('products')->findOrFail($id);

        return $this->successResponse(
            new CategoryResource($category),
            'Detail kategori berhasil diambil.'
        );
    }

    public function update(int $id, UpdateCategoryRequest $request): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());

        return $this->successResponse(
            new CategoryResource($category->fresh()),
            'Kategori berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return $this->successResponse(null, 'Kategori berhasil dihapus.');
    }
}
