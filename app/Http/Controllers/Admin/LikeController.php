<?php

namespace App\Http\Controllers\Admin;

use App\Application\Services\PostService;
use App\Domain\Models\PostLike;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LikeController extends Controller
{
    public function __construct(private PostService $posts)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Likes/Index', [
            'likes' => $this->posts->likesPage(),
        ]);
    }

    public function destroy(PostLike $like): RedirectResponse
    {
        $this->posts->deleteLike($like->id);

        return redirect()->route('admin.likes.index')->with('success', 'Like deleted.');
    }
}
