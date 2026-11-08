<?php

namespace App\Http\Controllers;

use App\Application\Services\PostService;
use App\Application\Services\UserService;
use App\Domain\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        private UserService $users,
        private PostService $posts,
    ) {
    }

    public function index(Request $request): Response
    {
        $term = $request->string('q')->trim()->toString();
        $users = $this->users->search($term !== '' ? $term : null);

        $users->through(function (User $user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'bio' => $user->bio,
                'posts_count' => $user->posts_count,
                'followers_count' => $user->followers_count,
            ];
        });

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => ['q' => $term],
        ]);
    }

    public function show(Request $request, User $user): Response
    {
        return Inertia::render('Users/Show', [
            'profile' => $this->users->profileData($request->user(), $user),
            'posts' => $this->posts->byAuthor($user, $request->user()),
        ]);
    }

    public function follow(Request $request, User $user): RedirectResponse
    {
        $this->users->follow($request->user(), $user);

        return back();
    }

    public function unfollow(Request $request, User $user): RedirectResponse
    {
        $this->users->unfollow($request->user(), $user);

        return back();
    }
}
