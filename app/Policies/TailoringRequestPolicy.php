<?php

namespace App\Policies;

use App\Models\TailoringRequest;
use App\Models\User;

class TailoringRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('tailoring.manage');
    }

    public function view(User $user, TailoringRequest $tailoringRequest): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, TailoringRequest $tailoringRequest): bool
    {
        return $this->viewAny($user);
    }
}
