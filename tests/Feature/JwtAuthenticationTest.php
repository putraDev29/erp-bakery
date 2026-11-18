<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JwtAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_returns_jwt_token_and_authenticates_me_endpoint(): void
    {
        $this->seed();

        // 1. Valid Login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'owner@bakeryerp.local',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'token',
            'token_type',
            'expires_in',
            'user',
        ]);

        $token = $response->json('token');
        $this->assertNotEmpty($token);

        // 2. Access /api/auth/me with Bearer token
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');

        $meResponse->assertStatus(200);
        $meResponse->assertJson([
            'success' => true,
            'user' => [
                'email' => 'owner@bakeryerp.local',
            ],
        ]);

        // 3. Access without token fails with 401
        $this->flushHeaders();
        auth()->logout();
        $failResponse = $this->getJson('/api/auth/me');
        $failResponse->assertStatus(401);
    }
}
