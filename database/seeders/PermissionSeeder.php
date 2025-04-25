<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Enums\RolesEnum;

class PermissionSeeder extends Seeder
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
            // User management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'assign_roles',
            
            // Role management
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            
            // Car management
            'view_cars',
            'create_cars',
            'edit_cars',
            'delete_cars',
            'approve_cars',
            
            // Appointment management
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'delete_appointments',
            'approve_appointments',
            
            // Transaction management
            'view_transactions',
            'create_transactions',
            'edit_transactions',
            'delete_transactions',
            
            // Activity Logs
            'view_activity_logs',
            
            // Settings
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => RolesEnum::Admin->value]);
        $adminRole->syncPermissions(Permission::all());
        
        $userRole = Role::firstOrCreate(['name' => RolesEnum::User->value]);
        $userRole->syncPermissions([
            'view_cars',
            'create_cars', 
            'edit_cars',
            'delete_cars',
            'view_appointments',
            'create_appointments',
            'view_transactions',
        ]);
        
        // Assign admin role to the first user if it exists
        $user = User::first();
        if ($user) {
            $user->assignRole(RolesEnum::Admin->value);
        }
    }
} 