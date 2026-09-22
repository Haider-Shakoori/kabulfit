<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermission('customers.manage'); }
    public function view(User $user, User $customer): bool { return $this->viewAny($user); }
    public function update(User $user, User $customer): bool { return $this->viewAny($user); }
    public function manageRoles(User $user, User $customer): bool { return $user->hasPermission('roles.manage'); }
}
