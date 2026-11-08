<?php

namespace App\Policies;

use App\Domain\Models\Post;
use App\Domain\Models\User;

class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->isModerator();
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->isModerator();
    }
}
