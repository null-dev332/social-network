<?php

namespace Tests\Feature;

use App\Domain\Models\Post;
use App\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// controller -> PostService -> PostRepository -> database
class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_does_not_see_the_feed(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_user_can_create_a_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'content' => 'Hi, this is my first post.',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'content' => 'Hi, this is my first post.',
        ]);
    }

    public function test_vulgar_text_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'content' => 'you are an idiot',
        ]);

        $response->assertSessionHasErrors('content');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_like_can_be_toggled(): void
    {
        $user = User::factory()->create();
        $post = Post::create([
            'user_id' => $user->id,
            'content' => 'Something to like.',
        ]);

        $this->actingAs($user)->post(route('posts.like', $post));
        $this->assertDatabaseHas('post_likes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->post(route('posts.like', $post));
        $this->assertDatabaseMissing('post_likes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_delete_someone_elses_post(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::create([
            'user_id' => $other->id,
            'content' => 'Someone else\'s post.',
        ]);

        $this->actingAs($user)
            ->delete(route('posts.destroy', $post))
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_user_can_delete_own_post(): void
    {
        $user = User::factory()->create();
        $post = Post::create([
            'user_id' => $user->id,
            'content' => 'My post.',
        ]);

        $this->actingAs($user)->delete(route('posts.destroy', $post));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_image_is_stored_with_the_post(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('posts.store'), [
            'content' => 'Photo from class.',
            'image' => UploadedFile::fake()->image('fotka.jpg'),
        ])->assertSessionHasNoErrors();

        $post = Post::first();
        $this->assertNotNull($post->image);
        Storage::disk('public')->assertExists($post->image);
    }

    public function test_admin_edit_does_not_change_password_hash(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $oldHash = $user->password;

        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'New name',
            'email' => $user->email,
            'bio' => 'hello',
            'role' => 'user',
            'password' => 'uplne-jine-heslo',
        ])->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame($oldHash, $user->password);
        $this->assertSame('New name', $user->name);
    }

    public function test_manager_moderates_posts_but_not_users(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $this->actingAs($manager)->get(route('admin.posts.index'))->assertOk();
        $this->actingAs($manager)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_user_cannot_follow_self(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('users.show', $user))
            ->post(route('users.follow', $user))
            ->assertSessionHasErrors('user');
    }

    public function test_user_can_comment(): void
    {
        $user = User::factory()->create();
        $post = Post::create([
            'user_id' => $user->id,
            'content' => 'Original post.',
        ]);

        $this->actingAs($user)
            ->post(route('comments.store', $post), [
                'content' => 'Agreed.',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'Agreed.',
        ]);
    }
}
