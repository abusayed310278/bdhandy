<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(): View
    {
        $tickets = Auth::user()->supportTickets()
            ->latest()
            ->paginate(15);

        return view('provider.tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        return view('provider.tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'subject'     => ['required', 'string', 'max:255'],
            'department'  => ['required', 'in:technical,billing,verification,general'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $user = Auth::user();

        do {
            $number = 'TKT-' . now()->format('Y') . '-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (SupportTicket::where('ticket_number', $number)->exists());

        $ticket = SupportTicket::create([
            'ticket_number' => $number,
            'user_id'       => $user->id,
            'subject'       => $request->subject,
            'description'   => $request->description,
            'priority'      => $request->priority,
            'department'    => $request->department,
            'status'        => 'open',
        ]);

        return redirect()->route('provider.tickets.show', $ticket)->with('success', 'Support ticket opened.');
    }

    public function show(SupportTicket $ticket): View|RedirectResponse
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->load(['messages.sender', 'assignedTo']);

        return view('provider.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['message' => ['required', 'string', 'max:5000']]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id'         => Auth::id(),
            'message'           => $request->message,
        ]);

        $ticket->update(['last_reply_at' => now(), 'status' => 'pending']);

        return back()->with('success', 'Reply sent.');
    }
}
