<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Enums\RolesEnum;
use App\Enums\VendorStatusEnum;
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
        ]);
        $adminUser->assignRole(RolesEnum::Admin->value);

        // Create test user
        $testUser = User::create([
            'name' => 'Test User',
            'email' => 'user@abccars.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $testUser->assignRole(RolesEnum::User->value);
    }
}
