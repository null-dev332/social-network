<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Models\Post;
use App\Domain\Models\User;

interface PostRepositoryInterface extends RepositoryInterface
{
    public function feedFor(User $user, int $perPage = 10);

    public function byAuthor(User $author, User $viewer, int $perPage = 12);

    public function findForShow(int|string $id, User $viewer): Post;

    public function toggleLike(Post $post, User $user): bool;
}
