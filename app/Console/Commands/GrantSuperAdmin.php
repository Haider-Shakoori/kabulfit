<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class GrantSuperAdmin extends Command
{
    protected $signature = 'admin:grant-super {email}';

    protected $description = 'Grant the system super-admin role to an existing KabulFit user';

    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No user exists with that email.');

            return self::FAILURE;
        }

        $role = Role::query()->where('slug', 'super-admin')->first();

        if (! $role) {
            $this->error('Admin roles are not seeded. Run the database seeders first.');

            return self::FAILURE;
        }

        $user->roles()->syncWithoutDetaching([$role->id]);

        $this->info('Super-admin access granted to '.$user->email.'.');

        return self::SUCCESS;
    }
}
