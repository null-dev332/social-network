<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Models\Post;
use App\Domain\Models\PostLike;
use App\Domain\Models\User;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    protected function modelClass(): string
    {
        return Post::class;
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->newQuery()
            ->with('user:id,name')
            ->withCount('comments')
            ->latest()
            ->paginate($perPage);
    }

    public function feedFor(User $user, int $perPage = 10)
    {
        $ids = $user->following()->pluck('users.id');
        $ids->push($user->id);

        return $this->model->newQuery()
            ->whereIn('user_id', $ids)
            ->with('user:id,name')
            ->withCount(['likes', 'comments'])
            ->withExists(['likes as liked' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function byAuthor(User $author, User $viewer, int $perPage = 12)
    {
        return $this->model->newQuery()
            ->where('user_id', $author->id)
            ->with('user:id,name')
            ->withCount(['likes', 'comments'])
            ->withExists(['likes as liked' => function ($q) use ($viewer) {
                $q->where('user_id', $viewer->id);
            }])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findForShow(int|string $id, User $viewer): Post
    {
        return $this->model->newQuery()
            ->with([
                'user:id,name',
                'comments' => function ($q) {
                    $q->with('user:id,name')->orderBy('created_at');
                },
            ])
            ->withCount('likes')
            ->withExists(['likes as liked' => function ($q) use ($viewer) {
                $q->where('user_id', $viewer->id);
            }])
            ->findOrFail($id);
    }

    public function toggleLike(Post $post, User $user): bool
    {
        $existing = PostLike::query()
            ->where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        PostLike::query()->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        return true;
    }
}
