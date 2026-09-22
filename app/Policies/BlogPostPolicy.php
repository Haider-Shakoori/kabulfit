<?php

namespace App\Policies;

use App\Models\BlogPost;
use App\Models\User;

class BlogPostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('content.manage');
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, BlogPost $post): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, BlogPost $post): bool
    {
        return $this->viewAny($user);
    }
}
