<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Models\Comment;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    protected function modelClass(): string
    {
        return Comment::class;
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->newQuery()
            ->with(['user:id,name', 'post:id,content'])
            ->latest()
            ->paginate($perPage);
    }
}
