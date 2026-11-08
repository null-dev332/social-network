<?php

namespace App\Policies;

use App\Domain\Models\Comment;
use App\Domain\Models\User;

class CommentPolicy
{
    public function update(User $user, Comment $comment): bool
    {
        return $user->isModerator();
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isModerator();
    }
}
