<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiShopTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $users = User::factory()->create([
            'username' => 'username',
            'password' => bcrypt('password'),
            'email' => 'email@mail.mail',
            'phone' => '081234567890',
            'name' => 'Test User',
        ]);

        $response = $this->post('/api/auth/login', [
            'username' => 'username',
            'password' => 'password',
        ], $this->headers);

        $this->headers['Authorization'] = 'Bearer ' . $response->json()['data']['access_token'];
    }

    private $headers = [
        'Accept' => 'application/json',
    ];

    public function test_create_shop()
    {

        $response = $this->post('/api/shops', [
            'name' => 'Test Shop',
            'address' => 'Test Address',
        ], $this->headers);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'name',
                'address',
                'owner' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);
    }

    public function test_create_shop_failed()
    {
        $response = $this->post('/api/shops', [
            'name' => 'Test Shop',
        ], $this->headers);

        $response->assertStatus(422);
    }

    public function test_get_shops()
    {
        $response = $this->post('/api/shops', [
            'name' => 'Test Shop',
            'address' => 'Test Address',
        ], $this->headers);

        $response->assertStatus(201);

        $response = $this->get('/api/shops', $this->headers);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'address',
                    'owner' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ],
        ]);
    }

    public function test_get_shop()
    {
        $responseNewShop = $this->post('/api/shops', [
            'name' => 'Test Shop',
            'address' => 'Test Address',
        ], $this->headers);

        $responseNewShop->assertStatus(201);

        $response = $this->get('/api/shops/' . $responseNewShop->json()['data']['id'], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'name',
                'address',
                'owner' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);
    }

    public function test_get_shop_failed()
    {
        $response = $this->get('/api/shops/1', $this->headers);

        $response->assertStatus(404);
    }

    public function test_update_shop()
    {
        $responseNewShop = $this->post('/api/shops', [
            'name' => 'Test Shop',
            'address' => 'Test Address',
        ], $this->headers);

        $responseNewShop->assertStatus(201);

        $response = $this->put('/api/shops/' . $responseNewShop->json()['data']['id'], [
            'name' => 'Test Shop Updated',
            'address' => 'Test Address Updated',
        ], $this->headers);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'name',
                'address',
                'owner' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);

        $response->assertJson([
            'data' => [
                'name' => 'Test Shop Updated',
                'address' => 'Test Address Updated',
            ],
        ]);
    }

    public function test_update_shop_failed()
    {
        $response = $this->put('/api/shops/1', [
            'name' => 'Test Shop Updated',
            'address' => 'Test Address Updated',
        ], $this->headers);

        $response->assertStatus(404);
    }
}
