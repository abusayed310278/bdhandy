<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'ticket_number' => 'TKT-' . strtoupper(uniqid()),
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'open',
            'priority' => 'normal',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Support ticket created successfully.',
            'data' => $ticket
        ]);
    }
}
