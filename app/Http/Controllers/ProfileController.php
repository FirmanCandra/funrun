<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $ticketsCount = 0;
        $ordersCount = 0;
        $pendingOrdersCount = 0;
        $recentTickets = collect();
        $recentOrders = collect();

        if ($user->isUser()) {
            $ticketsQuery = \App\Models\Ticket::whereIn('status', ['valid', 'checked-in'])
                ->whereHas('participant', fn ($q) => $q->where('user_id', $user->id));
            $ticketsCount = $ticketsQuery->count();
            $recentTickets = $ticketsQuery->with(['participant.event', 'order'])->latest()->take(3)->get();

            $ordersQuery = \App\Models\Order::with(['event', 'tickets'])->where('user_id', $user->id);
            $ordersCount = $ordersQuery->count();
            $pendingOrdersCount = (clone $ordersQuery)->where('payment_status', \App\Models\Order::STATUS_WAITING)->count();
            $recentOrders = $ordersQuery->latest()->take(3)->get();
        }

        return view('profile.edit', compact(
            'user',
            'ticketsCount',
            'ordersCount',
            'pendingOrdersCount',
            'recentTickets',
            'recentOrders'
        ));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
