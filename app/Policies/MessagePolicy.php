<?php

namespace App\Policies;

use App\Domain\Models\Message;
use App\Domain\Models\User;

class MessagePolicy
{
    public function update(User $user, Message $message): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Message $message): bool
    {
        return $user->isAdmin();
    }
}
