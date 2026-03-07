<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\sellerModel;
use App\Models\customersModel;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- Create Admin ---
        $this->call(AdminSeeder::class);

        // --- Create a Test Seller ---
        $seller = new sellerModel();
        $seller->full_name = 'Test Seller';
        $seller->seller_email = 'seller@example.com';
        $seller->password = Hash::make('password');
        $seller->store_name = 'The Test Store';
        $seller->save();

        // --- Create a Test Customer ---
        $customer = new customersModel();
        $customer->full_name = 'Test Customer';
        $customer->customers_email = 'customer@example.com';
        $customer->password = Hash::make('password');
        $customer->save();
    }
}
