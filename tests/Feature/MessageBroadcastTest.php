<?php

namespace Tests\Feature;

use App\Domain\Models\User;
use App\Events\MessageSent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MessageBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_sending_a_message_broadcasts_it(): void
    {
        Event::fake([MessageSent::class]);

        $me = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($me)->post(route('messages.store', $other), [
            'content' => 'Hey, are you there?',
        ])->assertRedirect();

        $this->assertDatabaseHas('messages', [
            'sender_id' => $me->id,
            'receiver_id' => $other->id,
            'content' => 'Hey, are you there?',
        ]);

        Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($me, $other) {
            return $event->message->sender_id === $me->id
                && $event->message->receiver_id === $other->id
                && $event->message->content === 'Hey, are you there?';
        });
    }

    public function test_user_can_listen_only_to_own_chat(): void
    {
        config([
            'broadcasting.default' => 'reverb',
            'broadcasting.connections.reverb.key' => 'test-key',
            'broadcasting.connections.reverb.secret' => 'test-secret',
            'broadcasting.connections.reverb.app_id' => 'test-app',
        ]);

        // tests use the null broadcaster so the channel has to be registered again
        Broadcast::channel('chat.{userId}', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });

        $me = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($me)->post('/broadcasting/auth', [
            'socket_id' => '1234.5678',
            'channel_name' => 'private-chat.'.$me->id,
        ])->assertOk();

        $this->actingAs($me)->post('/broadcasting/auth', [
            'socket_id' => '1234.5678',
            'channel_name' => 'private-chat.'.$other->id,
        ])->assertForbidden();
    }
}
