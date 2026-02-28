<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'),
        ]);

        Product::create([
            'name' => 'Large Ready Mix',
            'description' => 'Description for Large Ready Mix',
            'price' => 50000,
        ]);
        Product::create([
            'name' => 'Medium Ready Mix',
            'description' => 'Description for Medium Ready Mix',
            'price' => 30000,
        ]);

        RawMaterial::create([
            'name' => 'Cement',
            'current_stock' => 100,
        ]);
        RawMaterial::create([
            'name' => 'Gravel',
            'current_stock' => 100,
        ]);
        RawMaterial::create([
            'name' => 'Sand',
            'current_stock' => 100,
        ]);

        Customer::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St, City, Country',
        ]);
        Customer::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '0987654321',
            'address' => '456 Oak Ave, City, Country',
        ]);
    }
}
