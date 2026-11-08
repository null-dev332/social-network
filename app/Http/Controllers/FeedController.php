<?php

namespace App\Http\Controllers;

use App\Application\Services\PostService;
use App\Application\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedController extends Controller
{
    public function __construct(
        private PostService $posts,
        private UserService $users,
    ) {
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            'posts' => $this->posts->feed($request->user()),
            'suggestions' => $this->users->suggestions($request->user()),
        ]);
    }
}
