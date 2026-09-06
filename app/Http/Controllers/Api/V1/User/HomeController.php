<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TripResource;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $country = $request->query('country'); // 'ID' or 'JP'

        // Banners
        $banners = Banner::where('is_active', true)
            ->when($country, function ($q) use ($country) {
                $q->where(function ($sq) use ($country) {
                    $sq->where('country_filter', $country)
                        ->orWhere('country_filter', 'all')
                        ->orWhereNull('country_filter');
                });
            })
            ->orderBy('order')
            ->get();

        // Flash Sales
        $flashSales = Product::flashSale()
            ->when($country, fn ($q) => $q->where('origin_country', $country))
            ->with(['brand', 'primaryImage', 'images'])
            ->take(10)
            ->get();

        // Brands & Categories
        $brands = Brand::where('is_active', true)->take(12)->get();
        $categories = Category::where('is_active', true)->take(12)->get();

        // Base Product Query
        $baseProductQuery = Product::active()
            ->when($country, fn ($q) => $q->where('origin_country', $country))
            ->with(['brand', 'category', 'primaryImage', 'images']);

        // Special for you
        $specialForYou = (clone $baseProductQuery)->inRandomOrder()->take(6)->get();

        // Beli Lagi (History)
        $beliLagi = [];
        $user = $request->user('sanctum');
        if ($user) {
            $orderedProductIds = $user->orders()
                ->with('items')
                ->get()
                ->pluck('items')
                ->flatten()
                ->pluck('product_id')
                ->unique()
                ->values()
                ->all();

            if (! empty($orderedProductIds)) {
                $beliLagi = (clone $baseProductQuery)->whereIn('id', $orderedProductIds)->take(6)->get();
            }
        }

        // Best Sellers (bisa diambil dari order_items terbanyak atau fallback)
        $bestSellers = (clone $baseProductQuery)->take(6)->get();

        // Promo Products
        $promoProducts = (clone $baseProductQuery)
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->take(6)
            ->get();

        // Initial Product Grid
        $productGrid = (clone $baseProductQuery)->latest()->paginate(30);

        // Active Trip
        $activeTrip = Trip::active()->first();

        return $this->successResponse([
            'banners' => BannerResource::collection($banners),
            'flash_sales' => ProductResource::collection($flashSales),
            'brands' => BrandResource::collection($brands),
            'categories' => CategoryResource::collection($categories),
            'special_for_you' => ProductResource::collection($specialForYou),
            'beli_lagi' => ProductResource::collection($beliLagi),
            'best_sellers' => ProductResource::collection($bestSellers),
            'promo_products' => ProductResource::collection($promoProducts),
            'product_grid' => ProductResource::collection($productGrid)->response()->getData(true),
            'active_trip' => $activeTrip ? new TripResource($activeTrip) : null,
        ], 'Data home berhasil diambil.');
    }
}
