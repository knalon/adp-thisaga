<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage users',
            
            // Car permissions
            'view cars',
            'create cars',
            'edit cars',
            'delete cars',
            'approve cars',
            
            // Bid permissions
            'view bids',
            'create bids',
            'respond bids',
            
            // Appointment permissions
            'view appointments',
            'create appointments',
            'approve appointments',
            'reject appointments',
            
            // Transaction permissions
            'view transactions',
            'create transactions',
            'finalize transactions',
            
            // Admin dashboard
            'access admin dashboard',
            'access user dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        $userRole = Role::create(['name' => 'user', 'guard_name' => 'web']);
        $userRole->givePermissionTo([
            'view cars',
            'create cars',
            'edit cars',
            'delete cars',
            'view bids',
            'create bids',
            'view appointments',
            'create appointments',
            'view transactions',
            'access user dashboard',
        ]);
    }
} 