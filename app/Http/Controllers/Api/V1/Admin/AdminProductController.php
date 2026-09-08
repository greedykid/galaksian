<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportProductRequest;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Requests\Admin\UploadProductImageRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['brand', 'category', 'primaryImage']);

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->whereLike('name', "%{$search}%")
                    ->orWhereLike('sku', "%{$search}%");
            });
        }

        if ($brandId = $request->query('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = min((int) $request->query('per_page', 20), 100);
        $products = $query->latest()->paginate($perPage);

        return $this->successResponse(
            ProductResource::collection($products)->response()->getData(true),
            'Daftar produk admin berhasil diambil.'
        );
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $data['slug'] = $slug;
        }

        $images = $data['images'] ?? [];
        unset($data['images']);

        $product = Product::create($data);

        foreach ($images as $index => $path) {
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'order' => $index,
                'is_primary' => $index === 0,
            ]);
        }

        return $this->successResponse(
            new ProductDetailResource($product->load(['brand', 'category', 'images'])),
            'Produk berhasil ditambahkan.',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::with(['brand', 'category', 'images'])->findOrFail($id);

        return $this->successResponse(
            new ProductDetailResource($product),
            'Detail produk admin berhasil diambil.'
        );
    }

    public function update(int $id, UpdateProductRequest $request): JsonResponse
    {
        $product = Product::findOrFail($id);
        $data = $request->validated();

        $images = $data['images'] ?? null;
        unset($data['images']);

        $product->update($data);

        if ($images !== null) {
            $product->images()->delete();
            foreach ($images as $index => $path) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'order' => $index,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return $this->successResponse(
            new ProductDetailResource($product->fresh(['brand', 'category', 'images'])),
            'Produk berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete(); // Soft delete per rule

        return $this->successResponse(null, 'Produk berhasil dihapus (soft delete).');
    }

    public function import(ImportProductRequest $request): JsonResponse
    {
        $items = $request->validated('items');

        if (! $items && $request->hasFile('file')) {
            $content = file_get_contents($request->file('file')->getRealPath());
            $items = json_decode($content, true);
        }

        $imported = 0;
        if (is_array($items)) {
            foreach ($items as $item) {
                if (empty($item['name']) || empty($item['brand_id']) || ! isset($item['price'])) {
                    continue;
                }

                $slug = Str::slug($item['name']).'-'.Str::random(4);

                Product::create([
                    'name' => $item['name'],
                    'slug' => $slug,
                    'sku' => $item['sku'] ?? null,
                    'brand_id' => $item['brand_id'],
                    'category_id' => $item['category_id'] ?? null,
                    'price' => $item['price'],
                    'discount_price' => $item['discount_price'] ?? null,
                    'stock' => $item['stock'] ?? 0,
                    'availability_type' => $item['availability_type'] ?? 'ready_stock',
                    'origin_country' => $item['origin_country'] ?? 'ID',
                    'currency' => $item['currency'] ?? 'IDR',
                    'is_active' => $item['is_active'] ?? true,
                ]);

                $imported++;
            }
        }

        return $this->successResponse([
            'imported_count' => $imported,
        ], "Berhasil mengimpor {$imported} produk.");
    }

    public function uploadImages(int $id, UploadProductImageRequest $request): JsonResponse
    {
        $product = Product::findOrFail($id);
        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        $currentMaxOrder = (int) $product->images()->max('order');

        foreach ($request->file('images') as $index => $file) {
            $filename = Str::slug($product->name).'-'.time().'-'.$index.'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('products/'.$product->slug, $filename, 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'path' => '/storage/'.$path,
                'order' => $currentMaxOrder + $index + 1,
                'is_primary' => ! $hasPrimary && $index === 0,
            ]);
        }

        return $this->successResponse(
            new ProductDetailResource($product->fresh(['brand', 'category', 'images'])),
            'Gambar produk berhasil diunggah.',
            201
        );
    }

    public function deleteImage(int $id, int $imageId): JsonResponse
    {
        $product = Product::findOrFail($id);
        $image = ProductImage::where('product_id', $product->id)->findOrFail($imageId);

        // Delete file from storage
        $storagePath = str_replace('/storage/', '', $image->path);
        Storage::disk('public')->delete($storagePath);

        $wasPrimary = $image->is_primary;
        $image->delete();

        // If deleted image was primary, promote the next one
        if ($wasPrimary) {
            $nextImage = $product->images()->orderBy('order')->first();
            $nextImage?->update(['is_primary' => true]);
        }

        return $this->successResponse(
            new ProductDetailResource($product->fresh(['brand', 'category', 'images'])),
            'Gambar produk berhasil dihapus.'
        );
    }
}
