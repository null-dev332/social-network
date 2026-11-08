<?php

namespace App\Http\Controllers\Admin;

use App\Application\Services\UserService;
use App\Domain\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(private UserService $users)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => $this->users->adminList(),
        ]);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->only(['id', 'name', 'email', 'bio', 'role']),
        ]);
    }

    public function update(AdminUserRequest $request, User $user): RedirectResponse
    {
        $this->users->updateByAdmin($request->user(), $user, $request->validated());

        Log::channel('social')->info('Admin updated user', [
            'admin_id' => $request->user()->id,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User saved.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return back()->withErrors([
                'user' => 'You can delete your own account in Settings.',
            ]);
        }

        $this->users->deleteAccount($user);

        Log::channel('social')->info('Admin deleted user', [
            'admin_id' => $request->user()->id,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
