<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $users = User::factory()->create([
            'username' => 'username',
            'password' => bcrypt('password'),
            'email' => 'email@mail.mail',
            'phone' => '081234567890',
            'name' => 'Test User',
        ]);
    }

    private $headers = [
        'Accept' => 'application/json',
    ];

    public function test_register_success()
    {
        $response = $this->post('/api/auth/register', [
            'username' => 'usernamenya',
            'name' => 'Test User',
            'email' => 'user@email.com',
            'phone' => '081234567891',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'username',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_register_failed()
    {
        $response = $this->post('/api/auth/register', [
            'username' => 'username',
            'name' => 'Test User',
            'email' => 'user@email.com',
            'phone' => '081234567891',
            'password' => 'password',
            'password_confirmation' => 'password',
        ], $this->headers);
        
        $response->assertStatus(422);
    }

    public function test_login_success()
    {
        $response = $this->post('/api/auth/login', [
            'username' => 'username',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'username',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        $this->headers['Authorization'] = 'Bearer ' . $response->json('data.access_token');
    }

    public function test_login_failed()
    {
        $response = $this->post('/api/auth/login', [
            'username' => 'username',
            'password' => 'passwordsalah',
        ]);
        
        $response->assertStatus(401);
    }

    public function test_token()
    {
        $response = $this->post('/api/auth/login', [
            'username' => 'username',
            'password' => 'password',
        ]);
        
        $response->assertStatus(200);

        $response = $this->get('/api/auth', [
            'Authorization' => 'Bearer ' . $response->json('data.access_token'),
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
    }

    public function test_invalid_token()
    {
        $response = $this->get('/api/auth', [
            'Authorization' => 'Bearer ' . str_repeat('a', 32),
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(401);
    }

    public function test_logout()
    {
        $response = $this->post('/api/auth/login', [
            'username' => 'username',
            'password' => 'password',
        ]);
        
        $response->assertStatus(200);

        $response = $this->post('/api/auth/logout', [
            'Authorization' => 'Bearer ' . $response->json('data.access_token'),
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
    }

    public function test_logout_without_token()
    {
        $response = $this->post('/api/auth/logout', [], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(401);
    }
}
