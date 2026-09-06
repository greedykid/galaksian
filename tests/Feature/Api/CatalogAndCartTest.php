<?php

namespace Tests\Feature\Api;

use App\Enums\ProductAvailability;
use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogAndCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_home_and_products(): void
    {
        $brand = Brand::create(['name' => 'Indofood', 'slug' => 'indofood']);
        $category = Category::create(['name' => 'Mie', 'slug' => 'mie']);

        Product::create([
            'name' => 'Indomie Goreng',
            'slug' => 'indomie-goreng',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => 3500,
            'stock' => 100,
            'availability_type' => ProductAvailability::READY_STOCK,
            'is_active' => true,
        ]);

        $homeResponse = $this->getJson('/api/v1/home');
        $homeResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'banners',
                    'flash_sales',
                    'brands',
                    'categories',
                    'product_grid',
                ],
            ]);

        $productsResponse = $this->getJson('/api/v1/products?q=indomie');
        $productsResponse->assertStatus(200)
            ->assertJsonPath('data.data.0.slug', 'indomie-goreng');
    }

    public function test_cart_operations_and_stock_validation(): void
    {
        $user = User::create([
            'name' => 'Cart User',
            'phone' => '628888888888',
            'role' => UserRole::USER,
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $brand = Brand::create(['name' => 'Brand Test', 'slug' => 'brand-test']);
        $product = Product::create([
            'name' => 'Limited Stock Item',
            'slug' => 'limited-stock-item',
            'brand_id' => $brand->id,
            'price' => 20000,
            'stock' => 3,
            'availability_type' => ProductAvailability::READY_STOCK,
            'is_active' => true,
        ]);

        // Add to cart with valid qty
        $addResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/cart/items', [
                'product_id' => $product->id,
                'qty' => 2,
            ]);

        $addResponse->assertStatus(200)
            ->assertJsonPath('data.total_qty', 2);

        // Try to add exceeding stock -> 422
        $excessResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/cart/items', [
                'product_id' => $product->id,
                'qty' => 2,
            ]);

        $excessResponse->assertStatus(422)
            ->assertJson(['success' => false]);
    }
}
