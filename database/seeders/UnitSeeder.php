<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    private $units = [
        [
            'name' => 'Pieces',
            'symbol' => 'pcs',
            'quantity' => 'Jumlah',
            'description' => 'Pieces',
        ],
        [
            'name' => 'Kilogram',
            'symbol' => 'kg',
            'quantity' => 'Berat',
            'description' => 'Kilogram',
        ],
        [
            'name' => 'Gram',
            'symbol' => 'gr',
            'quantity' => 'Berat',
            'description' => 'Gram',
        ],
        [
            'name' => 'Liter',
            'symbol' => 'l',
            'quantity' => 'Volume',
            'description' => 'Liter',
        ],
        [
            'name' => 'Mililiter',
            'symbol' => 'ml',
            'quantity' => 'Volume',
            'description' => 'Mililiter',
        ],
        [
            'name' => 'Meter',
            'symbol' => 'm',
            'quantity' => 'Panjang',
            'description' => 'Meter',
        ],
        [
            'name' => 'Centimeter',
            'symbol' => 'cm',
            'quantity' => 'Panjang',
            'description' => 'Centimeter',
        ],
        [
            'name' => 'Milimeter',
            'symbol' => 'mm',
            'quantity' => 'Panjang',
            'description' => 'Milimeter',
        ],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->units as $unit) {
            Unit::create($unit);
        }
    }
}
