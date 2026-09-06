<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::active()->with(['brand', 'category', 'primaryImage', 'images']);

        // Search q across product name, brand name, category name
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('brand', fn ($bq) => $bq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter by brand slug or id
        if ($brand = $request->query('brand')) {
            $query->whereHas('brand', function ($q) use ($brand) {
                $q->where('slug', $brand)->orWhere('id', $brand);
            });
        }

        // Filter by category slug or id
        if ($category = $request->query('category')) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category)->orWhere('id', $category);
            });
        }

        // Filter by country (ID / JP)
        if ($country = $request->query('country')) {
            $query->where('origin_country', $country);
        }

        // Filter by availability (ready_stock / open_po)
        if ($availability = $request->query('availability')) {
            $query->where('availability_type', $availability);
        }

        // Sorting
        $sort = $request->query('sort', 'latest');
        match ($sort) {
            'cheapest' => $query->orderByRaw('COALESCE(discount_price, price) asc'),
            'expensive' => $query->orderByRaw('COALESCE(discount_price, price) desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->latest(),
        };

        $perPage = min((int) $request->query('per_page', 16), 50);
        $products = $query->paginate($perPage);

        return $this->successResponse(
            ProductResource::collection($products)->response()->getData(true),
            'Daftar produk berhasil diambil.'
        );
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['brand', 'category', 'images'])
            ->firstOrFail();

        return $this->successResponse(
            new ProductDetailResource($product),
            'Detail produk berhasil diambil.'
        );
    }

    public function brandProducts(string $slug, Request $request): JsonResponse
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();

        $query = Product::active()
            ->where('brand_id', $brand->id)
            ->with(['brand', 'category', 'primaryImage', 'images']);

        if ($search = $request->query('q')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = min((int) $request->query('per_page', 16), 50);
        $products = $query->latest()->paginate($perPage);

        return $this->successResponse([
            'brand' => new BrandResource($brand),
            'products' => ProductResource::collection($products)->response()->getData(true),
        ], "Daftar produk brand {$brand->name} berhasil diambil.");
    }

    public function categoryProducts(string $slug, Request $request): JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $query = Product::active()
            ->where('category_id', $category->id)
            ->with(['brand', 'category', 'primaryImage', 'images']);

        if ($search = $request->query('q')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = min((int) $request->query('per_page', 16), 50);
        $products = $query->latest()->paginate($perPage);

        return $this->successResponse([
            'category' => new CategoryResource($category),
            'products' => ProductResource::collection($products)->response()->getData(true),
        ], "Daftar produk kategori {$category->name} berhasil diambil.");
    }
}
