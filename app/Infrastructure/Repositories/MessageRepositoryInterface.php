<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Models\Message;
use App\Domain\Models\User;

interface MessageRepositoryInterface extends RepositoryInterface
{
    public function conversation(User $a, User $b);

    public function partnersFor(User $user);

    public function lastBetween(User $a, User $b): ?Message;

    public function unreadCount(User $user): int;

    public function unreadFrom(User $sender, User $reader): int;

    public function markRead(User $reader, User $sender): void;
}
