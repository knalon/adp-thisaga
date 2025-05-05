<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignUserRole extends Command
{
    protected $signature = 'user:assign-role {email}';
    protected $description = 'Assign the user role to a specific user';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $role = Role::where('name', 'user')->first();
        if (!$role) {
            $this->error("User role not found!");
            return 1;
        }

        $user->assignRole($role);
        $this->info("Successfully assigned user role to {$email}");
        return 0;
    }
}
