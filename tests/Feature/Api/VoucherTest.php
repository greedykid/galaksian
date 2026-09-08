<?php

namespace Tests\Feature\Api;

use App\Enums\VoucherScope;
use App\Enums\VoucherType;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherTest extends TestCase
{
    use RefreshDatabase;

    public function test_available_vouchers_returns_only_active_and_valid(): void
    {
        Voucher::create([
            'code' => 'AKTIF10',
            'type' => VoucherType::PERCENT,
            'value' => 10,
            'min_order_amount' => 50000,
            'applicable_scope' => VoucherScope::PRODUCT,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addYear(),
            'is_active' => true,
        ]);

        Voucher::create([
            'code' => 'KADALUARSA',
            'type' => VoucherType::FIXED,
            'value' => 15000,
            'applicable_scope' => VoucherScope::PRODUCT,
            'starts_at' => now()->subYear(),
            'ends_at' => now()->subDay(),
            'is_active' => true,
        ]);

        Voucher::create([
            'code' => 'NONAKTIF',
            'type' => VoucherType::FIXED,
            'value' => 15000,
            'applicable_scope' => VoucherScope::PRODUCT,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addYear(),
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/v1/cart/vouchers');

        $response->assertOk()
            ->assertJson(['success' => true]);

        $codes = collect($response->json('data'))->pluck('code')->all();

        $this->assertContains('AKTIF10', $codes);
        $this->assertNotContains('KADALUARSA', $codes);
        $this->assertNotContains('NONAKTIF', $codes);
    }

    public function test_voucher_payload_contains_display_fields(): void
    {
        Voucher::create([
            'code' => 'TESTV10',
            'type' => VoucherType::PERCENT,
            'value' => 10,
            'max_discount' => 20000,
            'min_order_amount' => 200000,
            'applicable_scope' => VoucherScope::PRODUCT,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addYear(),
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/cart/vouchers');

        $response->assertOk();

        $voucher = collect($response->json('data'))->firstWhere('code', 'TESTV10');

        $this->assertNotNull($voucher);
        $this->assertEquals('Diskon Jastip 10%', $voucher['title']);
        $this->assertStringContainsString('200rb', $voucher['description']);
        $this->assertEquals('percent', $voucher['type']);
        $this->assertEquals(20000, $voucher['max_discount']);
        $this->assertEquals('product', $voucher['applicable_scope']);
    }
}
