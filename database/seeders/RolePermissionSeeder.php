<?php

namespace Database\Seeders;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        foreach (PermissionsEnum::cases() as $permission) {
            Permission::create(['name' => $permission->value]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => RolesEnum::Admin->value]);
        $userRole = Role::create(['name' => RolesEnum::User->value]);

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign permissions to user role
        $userRole->givePermissionTo([
            // Public pages
            PermissionsEnum::ViewHome->value,
            PermissionsEnum::ViewAboutUs->value,
            PermissionsEnum::ViewContactUs->value,
            PermissionsEnum::ViewCarListings->value,
            PermissionsEnum::ViewCarDetails->value,
            PermissionsEnum::Register->value,
            PermissionsEnum::Login->value,

            // Authenticated user permissions
            PermissionsEnum::ViewUserDashboard->value,
            PermissionsEnum::ViewUserProfile->value,
            PermissionsEnum::EditUserProfile->value,
            PermissionsEnum::PlaceBids->value,
            PermissionsEnum::ScheduleAppointments->value,
        ]);
    }
}
