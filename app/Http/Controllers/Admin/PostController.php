<?php

namespace App\Http\Controllers\Admin;

use App\Application\Services\PostService;
use App\Domain\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function __construct(private PostService $posts)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Posts/Index', [
            'posts' => $this->posts->adminList(),
        ]);
    }

    public function edit(Post $post): Response
    {
        $this->authorize('update', $post);

        return Inertia::render('Admin/Posts/Edit', [
            'post' => $this->posts->adminEditData($post),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->posts->update($post, $request->validated(), $request->file('image'));

        Log::channel('social')->info('Moderator updated post', [
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Post saved.');
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $this->posts->delete($post);

        Log::channel('social')->info('Moderator deleted post', [
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted.');
    }
}
