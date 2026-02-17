<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    /**
     * Submit a support ticket.
     * POST /api/support/ticket
     */
    public function submitTicket(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'category' => 'required|string|in:Technical,Billing,Consultation,Other',
            'message'  => 'required|string|min:10',
            'priority' => 'nullable|string|in:low,medium,high',
        ]);

        try {
            // Generate a unique Ticket ID
            $ticketId = 'TK-' . strtoupper(Str::random(6));

            $ticket = SupportTicket::create([
                'ticket_id' => $ticketId,
                'name'      => $request->name,
                'email'     => $request->email,
                'category'  => $request->category,
                'message'   => $request->message,
                'priority'  => $request->priority ?? 'medium',
                'status'    => 'open',
            ]);

            Log::info('Support Ticket Created', ['ticket_id' => $ticketId, 'email' => $request->email]);

            return response()->json([
                'success' => true,
                'message' => 'Your ticket has been submitted successfully.',
                'ticket_id' => $ticketId,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Support Ticket Creation Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Could not process your support request. Please try again later.',
            ], 500);
        }
    }
}
