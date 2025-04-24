<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::create(['name' => RolesEnum::Admin->value]);
        $userRole = Role::create(['name' => RolesEnum::User->value]);

        // Create permissions
        $permissions = [
            // Car permissions
            'view cars',
            'create cars',
            'edit cars',
            'delete cars',
            'approve cars',

            // Appointment permissions
            'create appointments',
            'manage appointments',

            // User permissions
            'manage users',

            // Transaction permissions
            'create transactions',
            'view transactions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign specific permissions to user role
        $userRole->givePermissionTo([
            'view cars',
            'create cars',
            'edit cars',
            'delete cars',
            'create appointments',
        ]);
    }
}
