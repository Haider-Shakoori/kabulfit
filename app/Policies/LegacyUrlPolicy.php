<?php

namespace App\Policies;

use App\Models\LegacyUrl;
use App\Models\User;

class LegacyUrlPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('redirects.manage');
    }

    public function update(User $user, LegacyUrl $legacyUrl): bool
    {
        return $this->viewAny($user);
    }
}
