<?php

namespace App\Http\Controllers;

use App\Models\EventCategory;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Detail satu pesanan milik peserta yang sedang login.
     */
    public function show(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);

        $order->load(['event', 'tickets.participant']);

        $categories = EventCategory::where('event_id', $order->event_id)
            ->get()
            ->keyBy('code');

        return view('orders.show', compact('order', 'categories'));
    }

    /**
     * Unggah ulang bukti transfer — dipakai kalau bukti sebelumnya ditolak admin.
     */
    public function updateProof(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);

        if ($order->isPaid()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Pesanan ini sudah lunas, bukti tidak perlu diunggah lagi.');
        }

        $request->validate([
            'proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Bukti lama dibuang supaya tidak menumpuk di storage.
        if ($order->proof_of_payment) {
            Storage::disk('public')->delete($order->proof_of_payment);
        }

        $order->update([
            'proof_of_payment' => $request->file('proof')->store('proofs', 'public'),
            'payment_status' => Order::STATUS_WAITING,
            'rejection_reason' => null,
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Bukti pembayaran berhasil diunggah ulang. Menunggu verifikasi admin.');
    }

    private function authorizeOwner(Request $request, Order $order): void
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Pesanan ini bukan milik Anda.');
        }
    }
}
