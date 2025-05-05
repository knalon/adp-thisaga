<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign admin role to admin users
        $adminUsers = User::where('is_admin', true)->get();
        foreach ($adminUsers as $user) {
            $user->assignRole($adminRole);
        }

        // Assign user role to all other users
        $regularUsers = User::where('is_admin', false)->get();
        foreach ($regularUsers as $user) {
            $user->assignRole($userRole);
        }

        // Create and assign role to the default user if it doesn't exist
        $defaultUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Default User',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]
        );
        $defaultUser->assignRole($userRole);
    }
}
