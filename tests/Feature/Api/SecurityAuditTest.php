<?php

namespace Tests\Feature\Api;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_rejects_amount_mismatch(): void
    {
        $user = User::create(['name' => 'Buyer', 'phone' => '628100000021', 'role' => UserRole::USER]);
        $order = Order::create([
            'order_number' => 'ORD-SEC-AMT-01',
            'user_id' => $user->id,
            'status' => OrderStatus::PENDING_PAYMENT_PRODUCT,
            'address_snapshot' => ['recipient_name' => 'Buyer'],
            'product_subtotal' => 50000,
            'product_total' => 50000,
        ]);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-SEC-AMT-01',
            'order_id' => $order->id,
            'type' => InvoiceType::PRODUCT,
            'status' => InvoiceStatus::PENDING,
            'amount' => 50000,
            'payment_method' => PaymentMethod::QRIS,
        ]);
        Payment::create([
            'invoice_id' => $invoice->id,
            'method' => PaymentMethod::QRIS,
            'status' => PaymentStatus::PENDING,
            'amount' => 50000,
        ]);

        $response = $this->postJson('/api/v1/webhooks/payment', [
            'event_id' => 'evt_amt_mismatch_1',
            'invoice_number' => 'INV-SEC-AMT-01',
            'status' => 'settlement',
            'amount' => 1000,
        ]);

        $response->assertStatus(422);
        $this->assertEquals(InvoiceStatus::PENDING, $invoice->fresh()->status);
    }

    public function test_webhook_without_secret_rejected_in_production(): void
    {
        config(['services.payment.signing_secret' => null]);
        config(['services.payment.callback_token' => null]);
        config(['services.payment.require_signature' => true]);
        app()['env'] = 'production';

        $user = User::create(['name' => 'Buyer', 'phone' => '628100000022', 'role' => UserRole::USER]);
        $order = Order::create([
            'order_number' => 'ORD-SEC-SIG-01',
            'user_id' => $user->id,
            'status' => OrderStatus::PENDING_PAYMENT_PRODUCT,
            'address_snapshot' => ['recipient_name' => 'Buyer'],
            'product_subtotal' => 50000,
            'product_total' => 50000,
        ]);
        Invoice::create([
            'invoice_number' => 'INV-SEC-SIG-01',
            'order_id' => $order->id,
            'type' => InvoiceType::PRODUCT,
            'status' => InvoiceStatus::PENDING,
            'amount' => 50000,
            'payment_method' => PaymentMethod::QRIS,
        ]);

        $response = $this->postJson('/api/v1/webhooks/payment', [
            'event_id' => 'evt_nosig_prod',
            'invoice_number' => 'INV-SEC-SIG-01',
            'status' => 'settlement',
            'amount' => 50000,
        ]);

        $response->assertStatus(401);
    }

    public function test_otp_request_per_phone_limit_and_cooldown(): void
    {
        Cache::flush();

        $phone = '081234560001';

        // Request pertama sukses, kedua langsung kena cooldown 60 detik.
        $this->postJson('/api/v1/auth/otp/request', ['phone' => $phone])->assertStatus(200);
        $this->postJson('/api/v1/auth/otp/request', ['phone' => $phone])->assertStatus(429);

        // Lewati cooldown 5x untuk mencapai limit 5/15 menit, request ke-6 ditolak.
        for ($i = 0; $i < 4; $i++) {
            Cache::forget('otp:cooldown:6281234560001');
            $this->postJson('/api/v1/auth/otp/request', ['phone' => $phone])->assertStatus(200);
        }
        Cache::forget('otp:cooldown:6281234560001');
        $this->postJson('/api/v1/auth/otp/request', ['phone' => $phone])->assertStatus(429);
    }

    public function test_otp_verify_error_message_is_uniform(): void
    {
        Cache::flush();

        $this->postJson('/api/v1/auth/otp/request', ['phone' => '081234560002']);
        Cache::forget('otp:cooldown:6281234560002');

        // Kode salah -> pesan seragam (tidak membocorkan salah vs tidak ada).
        $wrong = $this->postJson('/api/v1/auth/otp/verify', [
            'phone' => '081234560002',
            'code' => '000000',
        ]);
        $wrong->assertStatus(422)->assertJsonPath('message', 'Kode OTP tidak valid atau kadaluarsa.');

        // Nomor tanpa OTP aktif -> pesan yang sama.
        $missing = $this->postJson('/api/v1/auth/otp/verify', [
            'phone' => '081234560099',
            'code' => '000000',
        ]);
        $missing->assertStatus(422)->assertJsonPath('message', 'Kode OTP tidak valid atau kadaluarsa.');
    }

    public function test_admin_login_with_user_role_returns_generic_401(): void
    {
        User::create([
            'name' => 'Regular',
            'phone' => '628100000031',
            'email' => 'regular@galaksian.test',
            'password' => Hash::make('secret123'),
            'role' => UserRole::USER,
        ]);

        $response = $this->postJson('/api/v1/admin/auth/login', [
            'login' => 'regular@galaksian.test',
            'password' => 'secret123',
        ]);

        // Tidak lagi 403 khas role (oracle), disamakan ke 401 generik.
        $response->assertStatus(401)
            ->assertJsonPath('message', 'Kredensial login admin tidak valid.');
    }

    public function test_user_and_admin_tokens_have_expiry_and_abilities(): void
    {
        Cache::flush();

        $this->postJson('/api/v1/auth/otp/request', ['phone' => '081234560003']);
        $verify = $this->postJson('/api/v1/auth/otp/verify', [
            'phone' => '081234560003',
            'code' => '123456',
        ]);
        $verify->assertStatus(200);

        $userToken = PersonalAccessToken::latest('id')->first();
        $this->assertNotNull($userToken->expires_at);
        $this->assertContains('user', $userToken->abilities);

        $admin = User::create([
            'name' => 'Admin Exp',
            'phone' => '628100000032',
            'email' => 'admexp@galaksian.test',
            'password' => Hash::make('secret123'),
            'role' => UserRole::ADMIN,
        ]);

        $login = $this->postJson('/api/v1/admin/auth/login', [
            'login' => 'admexp@galaksian.test',
            'password' => 'secret123',
        ]);
        $login->assertStatus(200);

        $adminToken = PersonalAccessToken::where('tokenable_id', $admin->id)
            ->latest('id')->first();
        $this->assertNotNull($adminToken->expires_at);
        $this->assertContains('admin', $adminToken->abilities);

        // Selisih expiry admin ~12 jam dari sekarang (toleransi 15 menit).
        $diffHours = now()->diffInMinutes($adminToken->expires_at) / 60;
        $this->assertTrue($diffHours > 11 && $diffHours <= 12.5, "Admin expiry harus ~12 jam, dapat {$diffHours} jam");
    }
}
