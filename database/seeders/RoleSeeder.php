<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    private $roles = [
        [
            'name' => 'Owner',
            'description' => 'The owner of the shop',
        ],
        [
            'name' => 'Staff',
            'description' => 'The staff of the shop',
        ],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->roles as $role) {
            Role::create($role);
        }
    }
}
