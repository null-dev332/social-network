<?php

namespace App\Providers;

use App\Domain\Models\Comment;
use App\Domain\Models\Message;
use App\Domain\Models\Post;
use App\Policies\CommentPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PostPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
    }
}
