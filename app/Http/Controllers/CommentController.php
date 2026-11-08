<?php

namespace App\Http\Controllers;

use App\Application\Services\CommentService;
use App\Domain\Models\Comment;
use App\Domain\Models\Post;
use App\Http\Requests\StoreCommentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function __construct(private CommentService $comments)
    {
    }

    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        $comment = $this->comments->create(
            $request->user(),
            $post,
            $request->validated('content')
        );

        Log::channel('social')->info('Comment created', [
            'user_id' => $request->user()->id,
            'comment_id' => $comment->id,
        ]);

        return back();
    }

    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $this->comments->delete($comment);

        Log::channel('social')->info('Comment deleted', [
            'user_id' => $request->user()->id,
            'comment_id' => $comment->id,
        ]);

        return back();
    }
}
