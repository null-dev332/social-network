<?php

namespace App\Http\Controllers;

use App\Application\Services\MessageService;
use App\Domain\Models\User;
use App\Http\Requests\StoreMessageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function __construct(private MessageService $messages)
    {
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Messages/Index', [
            'conversations' => $this->messages->inbox($request->user()),
        ]);
    }

    public function show(Request $request, User $user): Response
    {
        return Inertia::render('Messages/Show', [
            'other' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            // open() marks the chat as read, so it has to run before inbox()
            'messages' => $this->messages->open($request->user(), $user),
            'conversations' => $this->messages->inbox($request->user()),
        ]);
    }

    public function store(StoreMessageRequest $request, User $user): RedirectResponse
    {
        $message = $this->messages->send(
            $request->user(),
            $user,
            $request->validated('content')
        );

        Log::channel('social')->info('Message sent', [
            'user_id' => $request->user()->id,
            'message_id' => $message->id,
        ]);

        return back();
    }

    public function read(Request $request, User $user): HttpResponse
    {
        $this->messages->markRead($request->user(), $user);

        return response()->noContent();
    }
}
