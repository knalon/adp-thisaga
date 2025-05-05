<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create regular user
        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'phone_number' => '1234567890',
                'address' => '123 Test Street, Test City',
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole('user');

        // Create more users if in local environment
        if (app()->environment('local')) {
            User::factory(10)->create()->each(function ($user) {
                $user->assignRole('user');
            });
        }
    }
}
