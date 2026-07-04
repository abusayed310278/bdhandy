<?php

namespace App\Http\Controllers\Customer;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $conversations = Conversation::with([
                'messages' => fn($q) => $q->latest()->take(1),
                'provider.providerProfile',
            ])
            ->where('customer_id', $user->id)
            ->latest()
            ->get();

        return view('customer.conversations.index', compact('conversations'));
    }

    public function show(Conversation $conversation): View|RedirectResponse
    {
        $user = Auth::user();

        // Access check
        if ($conversation->customer_id !== $user->id) {
            abort(403);
        }

        $conversation->load(['provider.providerProfile']);

        $messages = $conversation->messages()
            ->with('sender')
            ->oldest()
            ->get();

        // Mark unread as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('customer.conversations.show', compact('conversation', 'messages'));
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        if ($conversation->customer_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'message'         => $request->message,
            'message_type'    => 'text',
            'is_read'         => false,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back();
    }
}
