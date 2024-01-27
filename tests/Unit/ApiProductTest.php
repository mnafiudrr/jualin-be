<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiProductTest extends TestCase
{

    use RefreshDatabase;

    private $headers = [
        'Accept' => 'application/json',
    ];

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

        $shop = Shop::create([
            'name' => 'Test Shop',
            'address' => 'Test Address',
            'owner_id' => $users->id,
        ]);

        $roles = Role::where('name', Role::OWNER)->first();
        $shop->users()->attach($users->id, ['role_id' => $roles->id]);

        $response = $this->post('/api/auth/login', [
            'username' => 'username',
            'password' => 'password',
        ], $this->headers);

        $this->headers['Authorization'] = 'Bearer ' . $response->json()['data']['access_token'];
        $this->headers['shop_id'] = $shop->id;

    }

    public function test_create_product()
    {
        $response = $this->post('/api/products', [
            "name"=> "Sayur Bayam",
            "is_show_in_transaction"=> 1,
            "is_using_stock"=> 1,
            "stock"=> 10,
            "unit_value"=> 1,
            "unit_id"=> 1,
            "base_price"=> 2800,
            "price"=> 3000,
        ], $this->headers);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'name',
                'is_show_in_transaction',
                'is_using_stock',
                'stock',
                'unit_value',
                'unit' => [
                    'id',
                    'name',
                    'symbol',
                    'quantity',
                ],
                'price',
            ],
        ]);
        
    }
}
