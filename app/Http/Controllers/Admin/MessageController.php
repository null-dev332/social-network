<?php

namespace App\Http\Controllers\Admin;

use App\Application\Services\MessageService;
use App\Domain\Models\Message;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMessageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function __construct(private MessageService $messages)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Messages/Index', [
            'messages' => $this->messages->adminList(),
        ]);
    }

    public function edit(Message $message): Response
    {
        $this->authorize('update', $message);

        return Inertia::render('Admin/Messages/Edit', [
            'message' => $this->messages->adminEditData($message),
        ]);
    }

    public function update(UpdateMessageRequest $request, Message $message): RedirectResponse
    {
        $this->messages->update($message, $request->validated('content'));

        Log::channel('social')->info('Admin updated message', [
            'user_id' => $request->user()->id,
            'message_id' => $message->id,
        ]);

        return redirect()->route('admin.messages.index')->with('success', 'Message saved.');
    }

    public function destroy(Request $request, Message $message): RedirectResponse
    {
        $this->authorize('delete', $message);

        $this->messages->delete($message);

        Log::channel('social')->info('Admin deleted message', [
            'user_id' => $request->user()->id,
            'message_id' => $message->id,
        ]);

        return redirect()->route('admin.messages.index')->with('success', 'Message deleted.');
    }
}
