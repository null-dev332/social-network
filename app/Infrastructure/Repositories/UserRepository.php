<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Models\User;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected function modelClass(): string
    {
        return User::class;
    }

    public function search(?string $term)
    {
        $query = $this->model->newQuery()
            ->withCount(['posts', 'followers']);

        if ($term) {
            $query->where('name', 'like', '%'.$term.'%');
        }

        return $query->orderBy('name')->paginate(12)->withQueryString();
    }

    public function isFollowing(User $follower, User $target): bool
    {
        return $follower->following()->where('users.id', $target->id)->exists();
    }

    public function follow(User $follower, User $target): void
    {
        $follower->following()->syncWithoutDetaching([$target->id]);
    }

    public function unfollow(User $follower, User $target): void
    {
        $follower->following()->detach($target->id);
    }

    // new remember token so "remember me" on other devices stops working
    public function resetPassword(User $user, string $password): void
    {
        $user->forceFill([
            'password' => $password,
            'remember_token' => Str::random(60),
        ])->save();
    }

    public function withCounts(User $user): User
    {
        return $user->loadCount(['posts', 'followers', 'following']);
    }

    // people the user doesn't follow yet, most followed first
    public function suggestionsFor(User $user, int $limit = 5)
    {
        $followingIds = $user->following()->pluck('users.id');

        return $this->model->newQuery()
            ->where('id', '!=', $user->id)
            ->whereNotIn('id', $followingIds)
            ->withCount('followers')
            ->orderByDesc('followers_count')
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name']);
    }
}
