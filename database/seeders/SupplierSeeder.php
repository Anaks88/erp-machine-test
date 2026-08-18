<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;


class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create([
            'name' => 'ABC Traders',
            'email' => 'abc.traders@example.com',
            'phone' => '9876543210'
        ]);

        Supplier::create([
            'name' => 'Global Supplies',
            'email' => 'global.supplies@example.com',
            'phone' => '9876543211'
        ]);
    }
}
