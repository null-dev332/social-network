<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Models\Message;
use App\Domain\Models\User;

class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{
    protected function modelClass(): string
    {
        return Message::class;
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->newQuery()
            ->with(['sender:id,name', 'receiver:id,name'])
            ->latest()
            ->paginate($perPage);
    }

    public function conversation(User $a, User $b)
    {
        return $this->model->newQuery()
            ->where(function ($q) use ($a, $b) {
                $q->where(function ($inner) use ($a, $b) {
                    $inner->where('sender_id', $a->id)->where('receiver_id', $b->id);
                })->orWhere(function ($inner) use ($a, $b) {
                    $inner->where('sender_id', $b->id)->where('receiver_id', $a->id);
                });
            })
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }

    public function partnersFor(User $user)
    {
        $sent = $this->model->newQuery()->where('sender_id', $user->id)->pluck('receiver_id');
        $received = $this->model->newQuery()->where('receiver_id', $user->id)->pluck('sender_id');
        $ids = $sent->merge($received)->unique()->values();

        return User::query()->whereIn('id', $ids)->orderBy('name')->get();
    }

    public function lastBetween(User $a, User $b): ?Message
    {
        return $this->model->newQuery()
            ->where(function ($q) use ($a, $b) {
                $q->where(function ($inner) use ($a, $b) {
                    $inner->where('sender_id', $a->id)->where('receiver_id', $b->id);
                })->orWhere(function ($inner) use ($a, $b) {
                    $inner->where('sender_id', $b->id)->where('receiver_id', $a->id);
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();
    }

    public function unreadCount(User $user): int
    {
        return $this->model->newQuery()
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    public function unreadFrom(User $sender, User $reader): int
    {
        return $this->model->newQuery()
            ->where('sender_id', $sender->id)
            ->where('receiver_id', $reader->id)
            ->whereNull('read_at')
            ->count();
    }

    public function markRead(User $reader, User $sender): void
    {
        $this->model->newQuery()
            ->where('sender_id', $sender->id)
            ->where('receiver_id', $reader->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
