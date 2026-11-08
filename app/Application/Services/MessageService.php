<?php

namespace App\Application\Services;

use App\Domain\Models\Message;
use App\Domain\Models\User;
use App\Events\MessageSent;
use App\Infrastructure\Repositories\MessageRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class MessageService
{
    public function __construct(private MessageRepositoryInterface $messages)
    {
    }

    public function unreadCount(User $user): int
    {
        return $this->messages->unreadCount($user);
    }

    public function inbox(User $user): array
    {
        $rows = [];

        foreach ($this->messages->partnersFor($user) as $partner) {
            $last = $this->messages->lastBetween($user, $partner);

            if (! $last) {
                continue;
            }

            $rows[] = [
                'user' => [
                    'id' => $partner->id,
                    'name' => $partner->name,
                ],
                'last_message' => $last->content,
                'last_at' => $last->created_at?->toIso8601String(),
                'unread' => $this->messages->unreadFrom($partner, $user),
                'mine' => $last->sender_id === $user->id,
            ];
        }

        usort($rows, function ($a, $b) {
            return strcmp((string) $b['last_at'], (string) $a['last_at']);
        });

        return $rows;
    }

    public function open(User $me, User $other)
    {
        if ($me->id === $other->id) {
            throw ValidationException::withMessages([
                'user' => "You can't message yourself.",
            ]);
        }

        $this->messages->markRead($me, $other);

        return $this->messages->conversation($me, $other);
    }

    public function send(User $sender, User $receiver, string $content): Message
    {
        if ($sender->id === $receiver->id) {
            throw ValidationException::withMessages([
                'content' => "You can't message yourself.",
            ]);
        }

        $message = $this->messages->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => $content,
        ]);

        // if reverb is not running the message is still saved
        try {
            broadcast(new MessageSent($message));
        } catch (Throwable $e) {
            Log::channel('social')->warning('Message broadcast failed', [
                'message_id' => $message->id,
            ]);
        }

        return $message;
    }

    public function markRead(User $reader, User $sender): void
    {
        if ($reader->id === $sender->id) {
            return;
        }

        $this->messages->markRead($reader, $sender);
    }

    public function adminEditData(Message $message): array
    {
        return [
            'id' => $message->id,
            'content' => $message->content,
            'sender' => $message->sender?->name,
            'receiver' => $message->receiver?->name,
        ];
    }

    public function adminList()
    {
        return $this->messages->paginate(15);
    }

    public function update(Message $message, string $content): Message
    {
        return $this->messages->update($message->id, [
            'content' => $content,
        ]);
    }

    public function delete(Message $message): void
    {
        $this->messages->delete($message->id);
    }
}
