<?php

namespace App\Application\Services;

use App\Domain\Models\Comment;
use App\Domain\Models\Post;
use App\Domain\Models\User;
use App\Infrastructure\Repositories\CommentRepositoryInterface;

class CommentService
{
    public function __construct(private CommentRepositoryInterface $comments)
    {
    }

    public function create(User $user, Post $post, string $content): Comment
    {
        return $this->comments->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => $content,
        ]);
    }

    public function update(Comment $comment, string $content): Comment
    {
        return $this->comments->update($comment->id, [
            'content' => $content,
        ]);
    }

    public function delete(Comment $comment): void
    {
        $this->comments->delete($comment->id);
    }

    public function adminEditData(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'content' => $comment->content,
            'author' => $comment->user?->name,
        ];
    }

    public function adminList()
    {
        return $this->comments->paginate(15);
    }
}
