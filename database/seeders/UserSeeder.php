<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@abccars.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '1234567890',
            'address' => '123 Admin Street',
            'city' => 'Admin City',
            'state' => 'Admin State',
            'postal_code' => '12345',
            'country' => 'USA',
            'is_admin' => true,
        ]);
        $adminUser->assignRole(RolesEnum::Admin->value);

        // Create seller test user
        $sellerUser = User::create([
            'name' => 'Seller User',
            'email' => 'seller@abccars.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '9876543210',
            'address' => '456 Seller Avenue',
            'city' => 'Seller City',
            'state' => 'Seller State',
            'postal_code' => '54321',
            'country' => 'USA',
        ]);
        $sellerUser->assignRole(RolesEnum::User->value);
        
        // Create buyer test user
        $buyerUser = User::create([
            'name' => 'Buyer User',
            'email' => 'buyer@abccars.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '5555555555',
            'address' => '789 Buyer Blvd',
            'city' => 'Buyer City',
            'state' => 'Buyer State',
            'postal_code' => '98765',
            'country' => 'USA',
        ]);
        $buyerUser->assignRole(RolesEnum::User->value);
    }
}
