<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Create permissions
        $permissions = [
            'view_cars',
            'create_cars',
            'edit_cars',
            'delete_cars',
            'view_bids',
            'create_bids',
            'edit_bids',
            'delete_bids',
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'delete_appointments',
            'view_transactions',
            'create_transactions',
            'edit_transactions',
            'delete_transactions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign all permissions to admin role
        $adminRole->givePermissionTo($permissions);

        // Assign basic permissions to user role
        $userRole->givePermissionTo([
            'view_cars',
            'create_bids',
            'view_bids',
            'create_appointments',
            'view_appointments',
            'view_transactions',
        ]);

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
