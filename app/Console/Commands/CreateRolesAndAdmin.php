<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CreateRolesAndAdmin extends Command
{
    protected $signature = 'app:create-roles-and-admin';

    protected $description = 'Create default roles and admin user';

    public function handle(): void
    {
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('admin');

        $this->info('Roles and admin user created successfully!');
        $this->info('Admin email: admin@example.com');
        $this->info('Admin password: password');
    }
}
