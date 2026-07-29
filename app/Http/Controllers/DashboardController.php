<?php

namespace App\Http\Controllers;

use App\Models\EventCategory;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard peserta: daftar pesanan milik user yang sedang login.
     */
    public function index(Request $request): View
    {
        $orders = Order::with(['event', 'tickets.participant'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $categories = $this->categoriesFor($orders->pluck('event_id'));

        return view('dashboard', compact('orders', 'categories'));
    }

    /**
     * Halaman tiket saja — hanya tiket yang sudah terbit, tanpa info pesanan
     * atau status pembayaran. Dipakai saat check-in di lokasi acara.
     */
    public function tickets(Request $request): View
    {
        $tickets = Ticket::with(['participant.event', 'order'])
            ->whereIn('status', ['valid', 'checked-in'])
            ->whereHas('participant', fn ($q) => $q->where('user_id', $request->user()->id))
            ->get()
            ->sortBy(fn ($ticket) => $ticket->participant->event->date ?? '')
            ->values();

        $categories = $this->categoriesFor(
            $tickets->pluck('participant.event_id')->unique()
        );

        return view('tickets', compact('tickets', 'categories'));
    }

    /**
     * Kategori dipakai untuk menampilkan nama lomba (mis. "5K Night Run"),
     * karena participants.category hanya menyimpan kode seperti "5K".
     */
    private function categoriesFor($eventIds)
    {
        return EventCategory::whereIn('event_id', $eventIds)
            ->get()
            ->keyBy(fn ($c) => $c->event_id.'|'.$c->code);
    }
}
