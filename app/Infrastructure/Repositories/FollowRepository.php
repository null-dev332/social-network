<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Models\Follow;

class FollowRepository extends BaseRepository implements FollowRepositoryInterface
{
    protected function modelClass(): string
    {
        return Follow::class;
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->newQuery()
            ->with(['follower:id,name', 'followed:id,name'])
            ->latest()
            ->paginate($perPage);
    }
}
