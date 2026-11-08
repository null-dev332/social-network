<?php

namespace App\Infrastructure\Providers;

use App\Infrastructure\Repositories\CommentRepository;
use App\Infrastructure\Repositories\CommentRepositoryInterface;
use App\Infrastructure\Repositories\FollowRepository;
use App\Infrastructure\Repositories\FollowRepositoryInterface;
use App\Infrastructure\Repositories\MessageRepository;
use App\Infrastructure\Repositories\MessageRepositoryInterface;
use App\Infrastructure\Repositories\PostLikeRepository;
use App\Infrastructure\Repositories\PostLikeRepositoryInterface;
use App\Infrastructure\Repositories\PostRepository;
use App\Infrastructure\Repositories\PostRepositoryInterface;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

// bind repository interfaces to the implementations
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->bind(PostLikeRepositoryInterface::class, PostLikeRepository::class);
        $this->app->bind(FollowRepositoryInterface::class, FollowRepository::class);
    }
}
