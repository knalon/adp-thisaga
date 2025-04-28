<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Enums\RolesEnum;
use Spatie\Permission\Models\Role;

class FixAdminRole extends Command
{
    protected $signature = 'fix:admin-role';
    protected $description = 'Fix admin role assignment';

    public function handle()
    {
        // Make sure the admin role exists
        $adminRole = Role::firstOrCreate(['name' => RolesEnum::Admin->value]);
        
        // Find admin users by email
        $adminUser = User::where('email', 'admin@abccars.com')->first();
        
        if ($adminUser) {
            // Clear cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            
            // Check if user already has the role
            if (!$adminUser->hasRole(RolesEnum::Admin->value)) {
                // Assign the role
                $adminUser->assignRole(RolesEnum::Admin->value);
                $this->info('Admin role assigned to admin@abccars.com successfully.');
            } else {
                $this->info('User already has admin role.');
            }
        } else {
            $this->error('Admin user not found!');
        }
        
        return Command::SUCCESS;
    }
} 