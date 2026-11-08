<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Models\PostLike;

class PostLikeRepository extends BaseRepository implements PostLikeRepositoryInterface
{
    protected function modelClass(): string
    {
        return PostLike::class;
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->newQuery()
            ->with(['user:id,name', 'post:id,content'])
            ->latest()
            ->paginate($perPage);
    }
}
