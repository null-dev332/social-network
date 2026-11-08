<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Models\User;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function search(?string $term);

    public function isFollowing(User $follower, User $target): bool;

    public function follow(User $follower, User $target): void;

    public function unfollow(User $follower, User $target): void;

    public function resetPassword(User $user, string $password): void;

    public function withCounts(User $user): User;

    public function suggestionsFor(User $user, int $limit = 5);
}
