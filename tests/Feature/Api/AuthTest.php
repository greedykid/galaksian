<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'John Updated')
            ->assertJsonPath('data.language', 'en');
    }
}
