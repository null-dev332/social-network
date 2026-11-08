<?php

namespace App\Application\Services;

use App\Domain\Models\Post;
use App\Domain\Models\User;
use App\Infrastructure\Repositories\PostLikeRepositoryInterface;
use App\Infrastructure\Repositories\PostRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function __construct(
        private PostRepositoryInterface $posts,
        private PostLikeRepositoryInterface $likes,
    ) {
    }

    public function feed(User $user)
    {
        return $this->posts->feedFor($user);
    }

    public function byAuthor(User $author, User $viewer)
    {
        return $this->posts->byAuthor($author, $viewer);
    }

    public function show(int|string $id, User $viewer): Post
    {
        return $this->posts->findForShow($id, $viewer);
    }

    public function find(int|string $id): Post
    {
        return $this->posts->findOrFail($id);
    }

    public function adminList()
    {
        return $this->posts->paginate(15);
    }

    public function adminEditData(Post $post): array
    {
        return [
            'id' => $post->id,
            'content' => $post->content,
            'image' => $post->image,
            'author' => $post->user?->name,
        ];
    }

    public function create(User $user, array $data, ?UploadedFile $image = null): Post
    {
        $path = null;

        if ($image) {
            $path = $image->store('posts', 'public');
        }

        return $this->posts->create([
            'user_id' => $user->id,
            'content' => $data['content'],
            'image' => $path,
        ]);
    }

    public function update(Post $post, array $data, ?UploadedFile $image = null): Post
    {
        $payload = [
            'content' => $data['content'],
        ];

        if ($image) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $payload['image'] = $image->store('posts', 'public');
        } elseif (! empty($data['remove_image']) && $post->image) {
            Storage::disk('public')->delete($post->image);
            $payload['image'] = null;
        }

        return $this->posts->update($post->id, $payload);
    }

    public function delete(Post $post): void
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $this->posts->delete($post->id);
    }

    public function toggleLike(Post $post, User $user): bool
    {
        return $this->posts->toggleLike($post, $user);
    }

    public function likesPage()
    {
        return $this->likes->paginate(20);
    }

    public function deleteLike(int|string $id): void
    {
        $this->likes->delete($id);
    }
}
