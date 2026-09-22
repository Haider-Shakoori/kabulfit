<?php

namespace App\Policies;

use App\Models\TailorAssignment;
use App\Models\User;

class TailorAssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('tailoring.work') || $user->hasPermission('tailoring.manage');
    }

    public function view(User $user, TailorAssignment $assignment): bool
    {
        return $user->hasPermission('tailoring.manage')
            || ($user->hasPermission('tailoring.work') && $assignment->tailor_id === $user->id);
    }

    public function update(User $user, TailorAssignment $assignment): bool
    {
        return $this->view($user, $assignment);
    }
}
