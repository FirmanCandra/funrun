<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function processPayment(Request $request, $ticket_id)
    {
        $request->validate([
            'payment_method' => 'required',
            'proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $ticket = \App\Models\Ticket::findOrFail($ticket_id);
        $payment = \App\Models\Payment::where('ticket_id', $ticket_id)->firstOrFail();

        $path = $request->file('proof')->store('proofs', 'public');

        // Update payment status to waiting for verification
        $payment->update([
            'payment_method' => $request->payment_method,
            'payment_status' => 'waiting_verification',
            'proof_of_payment' => $path
        ]);

        // Ticket remains pending until admin verifies

        return redirect()->route('ticket.show', ['ticket_code' => $ticket->ticket_code])
            ->with('success', 'Proof of payment uploaded successfully! Please wait for admin verification.');
    }
}
