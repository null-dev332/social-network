<?php

namespace App\Http\Controllers;

use App\Application\Services\PostService;
use App\Domain\Models\Post;
use App\Http\Requests\StorePostRequest;
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

    public function show(Request $request, Post $post): Response
    {
        return Inertia::render('Posts/Show', [
            'post' => $this->posts->show($post->id, $request->user()),
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = $this->posts->create(
            $request->user(),
            $request->validated(),
            $request->file('image')
        );

        Log::channel('social')->info('Post created', [
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        return redirect()->route('dashboard')->with('success', 'Your post has been shared.');
    }

    public function edit(Request $request, Post $post): Response
    {
        $this->authorize('update', $post);

        return Inertia::render('Posts/Edit', [
            'post' => $post->only(['id', 'content', 'image']),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->posts->update($post, $request->validated(), $request->file('image'));

        Log::channel('social')->info('Post updated', [
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        return redirect()->route('posts.show', $post)->with('success', 'Post updated.');
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $this->posts->delete($post);

        Log::channel('social')->info('Post deleted', [
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        return redirect()->route('dashboard')->with('success', 'Post deleted.');
    }

    public function like(Request $request, Post $post): RedirectResponse
    {
        $liked = $this->posts->toggleLike($post, $request->user());

        Log::channel('social')->info($liked ? 'Like' : 'Unlike', [
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        return back();
    }
}
