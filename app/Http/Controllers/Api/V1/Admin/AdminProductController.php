<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ProductAvailability;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportProductRequest;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Requests\Admin\UploadProductImageRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\AdminActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function __construct(
        protected AdminActivityLogService $activityLogService
    ) {}

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

        $this->activityLogService->log(
            $request->user(),
            'create_product',
            "Buat produk #{$product->id} ({$product->name})",
            $product,
            ['price' => $product->price, 'stock' => $product->stock],
            RequestFacade::ip()
        );

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

        $this->activityLogService->log(
            $request->user(),
            'update_product',
            "Ubah produk #{$product->id} ({$product->name})",
            $product,
            ['price' => $product->price, 'stock' => $product->stock],
            RequestFacade::ip()
        );

        return $this->successResponse(
            new ProductDetailResource($product->fresh(['brand', 'category', 'images'])),
            'Produk berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete(); // Soft delete per rule

        $this->activityLogService->log(
            request()->user(),
            'delete_product',
            "Hapus produk #{$product->id} ({$product->name})",
            $product,
            ['cascade' => true],
            request()->ip()
        );

        return $this->successResponse(null, 'Produk berhasil dihapus (soft delete).');
    }

    public function import(ImportProductRequest $request): JsonResponse
    {
        $items = $request->validated('items');

        if (! $items && $request->hasFile('file')) {
            $content = file_get_contents($request->file('file')->getRealPath());
            $items = json_decode($content, true);
        }

        if (! is_array($items)) {
            throw new BusinessException('Format import tidak valid. Harus berupa array.');
        }

        $imported = 0;
        $failures = [];
        foreach ($items as $index => $item) {
            $row = $index + 1;

            // Validasi ketat per item: tolak item invalid (bukan 500 / data korup).
            if (empty($item['name']) || ! is_string($item['name'])) {
                $failures[] = "Baris {$row}: nama produk wajib diisi.";

                continue;
            }
            if (empty($item['brand_id']) || ! is_numeric($item['brand_id']) || ! Brand::whereKey((int) $item['brand_id'])->exists()) {
                $failures[] = "Baris {$row}: brand_id tidak valid.";

                continue;
            }
            $price = $item['price'] ?? null;
            if (! is_numeric($price) || (int) $price < 0) {
                $failures[] = "Baris {$row}: harga harus bilangan >= 0.";

                continue;
            }
            $stock = $item['stock'] ?? 0;
            if (! is_numeric($stock) || (int) $stock < 0) {
                $failures[] = "Baris {$row}: stok harus bilangan >= 0.";

                continue;
            }
            $availability = $item['availability_type'] ?? 'ready_stock';
            if (! ProductAvailability::tryFrom($availability)) {
                $failures[] = "Baris {$row}: availability_type tidak valid (ready_stock|open_po).";

                continue;
            }

            $slug = Str::slug($item['name']).'-'.Str::random(4);

            Product::create([
                'name' => $item['name'],
                'slug' => $slug,
                'sku' => $item['sku'] ?? null,
                'brand_id' => (int) $item['brand_id'],
                'category_id' => $item['category_id'] ?? null,
                'price' => (int) $price,
                'discount_price' => isset($item['discount_price']) && $item['discount_price'] !== '' ? (int) $item['discount_price'] : null,
                'stock' => (int) $stock,
                'availability_type' => $availability,
                'origin_country' => $item['origin_country'] ?? 'ID',
                'currency' => $item['currency'] ?? 'IDR',
                'is_active' => filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ]);

            $imported++;
        }

        $message = "Berhasil mengimpor {$imported} produk.";
        if ($failures) {
            $message .= ' '.count($failures).' baris dilewati.';
        }

        return $this->successResponse([
            'imported_count' => $imported,
            'skipped_count' => count($failures),
            'failures' => $failures,
        ], $message);
    }

    public function uploadImages(int $id, UploadProductImageRequest $request): JsonResponse
    {
        $product = Product::findOrFail($id);
        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        $currentMaxOrder = (int) $product->images()->max('order');

        foreach ($request->file('images') as $index => $file) {
            $extension = $file->extension() ?: $file->guessExtension() ?: 'jpg';
            $filename = Str::slug($product->name).'-'.Str::random(12).'-'.$index.'.'.$extension;
            $path = $file->storeAs('products/'.$product->slug, $filename, 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'path' => '/storage/'.$path,
                'order' => $currentMaxOrder + $index + 1,
                'is_primary' => ! $hasPrimary && $index === 0,
            ]);
        }

        $this->activityLogService->log(
            $request->user(),
            'upload_product_image',
            "Upload gambar ke produk #{$product->id} ({$product->name})",
            $product,
            ['count' => count($request->file('images'))],
            RequestFacade::ip()
        );

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

        $this->activityLogService->log(
            request()->user(),
            'delete_product_image',
            "Hapus gambar #{$image->id} dari produk #{$product->id}",
            $product,
            ['image_id' => $image->id, 'was_primary' => $wasPrimary],
            request()->ip()
        );

        return $this->successResponse(
            new ProductDetailResource($product->fresh(['brand', 'category', 'images'])),
            'Gambar produk berhasil dihapus.'
        );
    }
}
