<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
}
