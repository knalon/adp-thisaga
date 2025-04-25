<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Only use one permissions/role seeder to avoid conflicts
            PermissionSeeder::class,
            // RoleSeeder::class, // Commented out to prevent duplicate role creation
            UserSeeder::class,
        ]);
    }
}
