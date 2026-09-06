<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    public function index(): JsonResponse
    {
        $brands = Brand::where('is_active', true)
            ->withCount('products')
            ->get();

        return $this->successResponse(
            BrandResource::collection($brands),
            'Daftar brand berhasil diambil.'
        );
    }
}
