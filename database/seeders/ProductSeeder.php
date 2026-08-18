<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop',
            'sku' => 'PROD-001',
            'current_stock' => 10,
            'unit_price' => 55000,
        ]);

        Product::create([
            'name' => 'Wireless Mouse',
            'sku' => 'PROD-002',
            'current_stock' => 50,
            'unit_price' => 750,
        ]);

        Product::create([
            'name' => 'Keyboard',
            'sku' => 'PROD-003',
            'current_stock' => 30,
            'unit_price' => 1200,
        ]);

        Product::create([
            'name' => 'Monitor',
            'sku' => 'PROD-004',
            'current_stock' => 15,
            'unit_price' => 15000,
        ]);

        Product::create([
            'name' => 'USB Cable',
            'sku' => 'PROD-005',
            'current_stock' => 100,
            'unit_price' => 300,
        ]);
    }
}
