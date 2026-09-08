<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_otp_succeeds_and_returns_code_in_test_env(): void
    {
        $response = $this->postJson('/api/v1/auth/otp/request', [
            'phone' => '081234567890',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'phone' => '6281234567890',
                    'code' => '123456',
                ],
            ]);

        $this->assertDatabaseHas('otp_codes', [
            'phone' => '6281234567890',
        ]);
    }

    public function test_verify_otp_creates_user_and_returns_token(): void
    {
        $this->postJson('/api/v1/auth/otp/request', [
            'phone' => '081234567890',
        ]);

        $response = $this->postJson('/api/v1/auth/otp/verify', [
            'phone' => '081234567890',
            'code' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user' => ['id', 'phone', 'is_new_user'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'phone' => '6281234567890',
            'is_new_user' => true,
        ]);
    }

    public function test_authenticated_user_can_access_me_and_update_profile(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'phone' => '628999999999',
            'role' => UserRole::USER,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $meResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/me');

        $meResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'John Doe');

        $updateResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/me', [
                'name' => 'John Updated',
                'language' => 'en',
                'identity_number' => '3171012505870003',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'John Updated')
            ->assertJsonPath('data.language', 'en')
            ->assertJsonPath('data.identity_number', '3171012505870003');
    }

    public function test_authenticated_user_can_upload_avatar(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Avatar User',
            'phone' => '628888888888',
            'role' => UserRole::USER,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/me/avatar', [
                'avatar' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $avatarUrl = $response->json('data.avatar_url');
        $this->assertNotNull($avatarUrl);
        $this->assertStringContainsString('/storage/avatars/', $avatarUrl);

        // Verify file exists in storage
        $storagePath = str_replace('/storage/', '', $avatarUrl);
        Storage::disk('public')->assertExists($storagePath);
    }

    public function test_change_password_requires_min_8_characters_and_confirmation(): void
    {
        $user = User::create([
            'name' => 'Jane Doe',
            'phone' => '628111111111',
            'role' => UserRole::USER,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        // Less than 8 chars
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/change-password', [
                'password' => 'short',
                'password_confirmation' => 'short',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Confirmation mismatch
        $responseMismatch = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/change-password', [
                'password' => 'newpassword123',
                'password_confirmation' => 'mismatched123',
            ]);

        $responseMismatch->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Success with valid 8+ chars confirmed
        $responseOk = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/change-password', [
                'password' => 'secret12345',
                'password_confirmation' => 'secret12345',
            ]);

        $responseOk->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Rate limit OTP tidak boleh dapat di-bypass dengan memalsukan X-Forwarded-For.
     * Karena trusted proxies default kosong, header X-Forwarded-For diabaikan sehingga
     * semua request dianggap berasal dari IP yang sama -> limit 10/menit berlaku global.
     */
    public function test_otp_rate_limit_cannot_be_bypassed_by_spoofing_forwarded_for(): void
    {
        $this->refreshRateLimiter();

        $this->withoutMiddleware('localize'); // Tidak relevan, namun memastikan fokus throttle

        // Kirim 11 request ke endpoint OTP, tiap request memakai X-Forwarded-For berbeda.
        // Karena proxy TIDAK dipercaya (trusted_proxies kosong), semua IP spoof diabaikan
        // dan request dianggap dari IP yang sama -> melewati limit 10 -> request ke-11 = 429.
        $lastStatus = null;
        for ($i = 0; $i < 11; $i++) {
            $response = $this->postJson('/api/v1/auth/otp/request', [
                'phone' => '08'.str_pad((string) (1000000000 + $i), 10, '0', STR_PAD_LEFT),
            ], [
                'X-Forwarded-For' => '203.0.113.'.($i + 1),
            ]);
            $lastStatus = $response->status();
        }

        $this->assertSame(429, $lastStatus, 'Request ke-11 harus ditolak oleh rate limit.');
    }

    /**
     * Dengan trusted proxy yang TIDAK dipercaya, spoof X-Forwarded-For tidak berpengaruh
     * pada identitas IP, sehingga 10 request pertama masih diizinkan (batas throttle).
     */
    public function test_otp_rate_limit_allows_ten_requests(): void
    {
        $this->refreshRateLimiter();

        $lastStatus = null;
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/v1/auth/otp/request', [
                'phone' => '08'.str_pad((string) (2000000000 + $i), 10, '0', STR_PAD_LEFT),
            ], [
                'X-Forwarded-For' => '198.51.100.'.($i + 1),
            ]);
            $lastStatus = $response->status();
            $this->assertNotSame(429, $response->status(), "Request ke-{$i} seharusnya diizinkan.");
        }

        $this->assertNotSame(429, $lastStatus);
    }

    /**
     * Reset rate limiter agar counter throttle bersih antar test.
     * Laravel menyimpan hit rate limit di cache dengan prefix 'laravel:throttle:'.
     */
    protected function refreshRateLimiter(): void
    {
        if (Cache::getStore() instanceof Store) {
            Cache::getStore()->flush();
        }
        Cache::flush();
    }
}
