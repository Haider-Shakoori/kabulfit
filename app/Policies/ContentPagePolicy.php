<?php

namespace App\Policies;

use App\Models\ContentPage;
use App\Models\User;

class ContentPagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('content.manage');
    }

    public function view(User $user, ContentPage $page): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, ContentPage $page): bool
    {
        return $this->viewAny($user);
    }
}
