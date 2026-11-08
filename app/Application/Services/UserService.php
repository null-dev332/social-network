<?php

namespace App\Application\Services;

use App\Domain\Models\User;
use App\Infrastructure\Repositories\FollowRepositoryInterface;
use App\Infrastructure\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $users,
        private FollowRepositoryInterface $follows,
    ) {
    }

    public function register(array $data): User
    {
        return $this->users->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'user',
        ]);
    }

    public function updateProfile(User $user, array $data): User
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (array_key_exists('bio', $data)) {
            $bio = trim((string) $data['bio']);
            $payload['bio'] = $bio === '' ? null : $bio;
        }

        if ($user->email !== $data['email']) {
            $payload['email_verified_at'] = null;
        }

        return $this->users->update($user->id, $payload);
    }

    public function changePassword(User $user, string $password): void
    {
        $this->users->update($user->id, [
            'password' => $password,
        ]);
    }

    public function resetPassword(User $user, string $password): void
    {
        $this->users->resetPassword($user, $password);
    }

    public function deleteAccount(User $user): void
    {
        foreach ($user->posts as $post) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
        }

        $this->users->delete($user->id);
    }

    public function search(?string $term)
    {
        return $this->users->search($term);
    }

    public function suggestions(User $user)
    {
        return $this->users->suggestionsFor($user);
    }

    public function profileData(User $viewer, User $profile): array
    {
        $profile = $this->users->withCounts($profile);

        return [
            'id' => $profile->id,
            'name' => $profile->name,
            'bio' => $profile->bio,
            'posts_count' => $profile->posts_count,
            'followers_count' => $profile->followers_count,
            'following_count' => $profile->following_count,
            'is_self' => $viewer->id === $profile->id,
            'is_following' => $viewer->id !== $profile->id && $this->users->isFollowing($viewer, $profile),
        ];
    }

    public function follow(User $follower, User $target): void
    {
        // can't follow yourself
        if ($follower->id === $target->id) {
            throw ValidationException::withMessages([
                'user' => "You can't follow yourself.",
            ]);
        }

        if ($this->users->isFollowing($follower, $target)) {
            return;
        }

        $this->users->follow($follower, $target);
    }

    public function unfollow(User $follower, User $target): void
    {
        $this->users->unfollow($follower, $target);
    }

    public function adminList()
    {
        return $this->users->paginate(15);
    }

    public function updateByAdmin(User $admin, User $user, array $data): User
    {
        // admin can't change the password hash
        unset($data['password'], $data['password_confirmation']);

        if ($admin->id === $user->id && ($data['role'] ?? '') !== 'admin') {
            throw ValidationException::withMessages([
                'role' => "You can't remove your own admin role.",
            ]);
        }

        return $this->users->update($user->id, [
            'name' => $data['name'],
            'email' => $data['email'],
            'bio' => ($data['bio'] ?? '') === '' ? null : $data['bio'],
            'role' => $data['role'],
        ]);
    }

    public function followsPage()
    {
        return $this->follows->paginate(20);
    }

    public function deleteFollow(int|string $id): void
    {
        $this->follows->delete($id);
    }
}
