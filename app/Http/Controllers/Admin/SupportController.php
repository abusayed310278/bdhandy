<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupportTicket::with(['user', 'assignedTo']);

        // KPI Counts
        $stats = [
            'total'    => SupportTicket::count(),
            'open'     => SupportTicket::where('status', 'open')->count(),
            'pending'  => SupportTicket::where('status', 'pending')->count(),
            'resolved' => SupportTicket::whereIn('status', ['resolved', 'closed'])->count(),
        ];

        // Search filter
        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', $search)
                  ->orWhere('subject', 'like', $search)
                  ->orWhere('description', 'like', $search)
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search);
                  });
            });
        }

        // Dropdown filters
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('department')) {
            $query->where('department', $request->input('department'));
        }

        $tickets = $query->latest('last_reply_at')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.tickets.index', compact('tickets', 'stats'));
    }

    public function show(SupportTicket $ticket): View
    {
        $ticket->load(['user', 'messages.sender', 'assignedTo']);

        // Fetch agents that tickets can be assigned to (admin, super_admin, support, moderator)
        $agents = User::role(['admin', 'super_admin', 'support', 'moderator'])
            ->orderBy('name')
            ->get();

        return view('admin.tickets.show', compact('ticket', 'agents'));
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'status'  => ['nullable', 'in:open,pending,resolved,closed'],
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id'         => Auth::id(),
            'message'           => $request->message,
        ]);

        $updateData = ['last_reply_at' => now()];
        if ($request->filled('status')) {
            $updateData['status'] = $request->input('status');
        } else {
            // Default reply updates ticket status to pending
            $updateData['status'] = 'pending';
        }

        $ticket->update($updateData);

        return back()->with('success', 'Reply posted successfully.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:open,pending,resolved,closed'],
        ]);

        $ticket->update([
            'status' => $request->input('status')
        ]);

        return back()->with('success', 'Ticket status updated to ' . ucfirst($ticket->status) . '.');
    }

    public function assign(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $ticket->update([
            'assigned_to' => $request->input('assigned_to')
        ]);

        $agentName = $ticket->assignedTo ? $ticket->assignedTo->name : 'Unassigned';
        return back()->with('success', 'Ticket assigned to ' . $agentName . '.');
    }
}
