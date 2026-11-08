<?php

namespace App\Http\Controllers\Admin;

use App\Application\Services\CommentService;
use App\Domain\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCommentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class CommentController extends Controller
{
    public function __construct(private CommentService $comments)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Comments/Index', [
            'comments' => $this->comments->adminList(),
        ]);
    }

    public function edit(Comment $comment): Response
    {
        $this->authorize('update', $comment);

        return Inertia::render('Admin/Comments/Edit', [
            'comment' => $this->comments->adminEditData($comment),
        ]);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): RedirectResponse
    {
        $this->comments->update($comment, $request->validated('content'));

        Log::channel('social')->info('Moderator updated comment', [
            'user_id' => $request->user()->id,
            'comment_id' => $comment->id,
        ]);

        return redirect()->route('admin.comments.index')->with('success', 'Comment saved.');
    }

    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $this->comments->delete($comment);

        Log::channel('social')->info('Moderator deleted comment', [
            'user_id' => $request->user()->id,
            'comment_id' => $comment->id,
        ]);

        return redirect()->route('admin.comments.index')->with('success', 'Comment deleted.');
    }
}
