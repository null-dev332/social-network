<?php

use App\Domain\Models\User;
use Illuminate\Support\Facades\Broadcast;

// users can only listen to their own messages
Broadcast::channel('chat.{userId}', function (User $user, $userId) {
    return (int) $user->id === (int) $userId;
});
