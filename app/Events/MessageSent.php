<?php

namespace App\Events;

use App\Domain\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// send right away, don't wait for the queue
class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.'.$this->message->sender_id),
            new PrivateChannel('chat.'.$this->message->receiver_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $this->message->loadMissing(['sender:id,name', 'receiver:id,name']);

        return [
            'message' => [
                'id' => $this->message->id,
                'sender_id' => $this->message->sender_id,
                'receiver_id' => $this->message->receiver_id,
                'sender_name' => $this->message->sender?->name,
                'receiver_name' => $this->message->receiver?->name,
                'content' => $this->message->content,
                'created_at' => $this->message->created_at?->toIso8601String(),
            ],
        ];
    }
}
