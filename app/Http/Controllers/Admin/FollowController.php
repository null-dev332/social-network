<?php

namespace App\Http\Controllers\Admin;

use App\Application\Services\UserService;
use App\Domain\Models\Follow;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FollowController extends Controller
{
    public function __construct(private UserService $users)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Follows/Index', [
            'follows' => $this->users->followsPage(),
        ]);
    }

    public function destroy(Follow $follow): RedirectResponse
    {
        $this->users->deleteFollow($follow->id);

        return redirect()->route('admin.follows.index')->with('success', 'Follow removed.');
    }
}
