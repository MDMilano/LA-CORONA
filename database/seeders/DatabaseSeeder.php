<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\MixerTruck;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Mix;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'superadmin']);

        Product::create([
            'name' => '2500 G-1 28D',
            'price' => 2500,
        ]);
        Product::create([
            'name' => '3000 G-1 28D',
            'price' => 3000,
        ]);

        MixerTruck::create([
            'name' => 'Standard 9m³ Truck',
            'capacity' => 9.00,
        ]);
        
        MixerTruck::create([
            'name' => 'Large 12m³ Truck',
            'capacity' => 12.00,
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
