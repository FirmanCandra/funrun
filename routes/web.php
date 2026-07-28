<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminAuthController;

// Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/event/{id}', [HomeController::class, 'showEvent'])->name('event.show');

// Registration Flow
Route::get('/register-event', [RegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('/register-event', [RegistrationController::class, 'submitRegistration']);
Route::get('/registration-success', [RegistrationController::class, 'success'])->name('registration.success');
Route::get('/checkout/{participant_id}', [RegistrationController::class, 'checkout'])->name('checkout');

// Payment Mock
Route::post('/pay/{ticket_id}', [PaymentController::class, 'processPayment'])->name('payment.process');

// E-Ticket
Route::get('/ticket/{ticket_code}', [TicketController::class, 'showTicket'])->name('ticket.show');
Route::get('/ticket/{ticket_code}/pdf', [TicketController::class, 'downloadPdf'])->name('ticket.pdf');

// Admin Panel
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected Routes
    Route::middleware([\App\Http\Middleware\IsAdmin::class])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/participants', [AdminController::class, 'participants'])->name('participants');
        Route::post('/participants/bulk-delete', [AdminController::class, 'bulkDestroyParticipant'])->name('participants.bulk-delete');
        Route::get('/participants/{id}/edit', [AdminController::class, 'editParticipant'])->name('participants.edit');
        Route::put('/participants/{id}', [AdminController::class, 'updateParticipant'])->name('participants.update');
        Route::delete('/participants/{id}', [AdminController::class, 'deleteParticipant'])->name('participants.delete');
        Route::get('/export-csv', [AdminController::class, 'exportCSV'])->name('export');
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
        Route::post('/payments/bulk-delete', [AdminController::class, 'bulkDestroyPayment'])->name('payments.bulk-delete');
        Route::post('/payments/{id}/approve', [AdminController::class, 'approvePayment'])->name('payments.approve');
        Route::get('/scanner', [AdminController::class, 'scanner'])->name('scanner');
        Route::post('/scan', [AdminController::class, 'scanTicket'])->name('scan');
        Route::get('/eticket/{ticket_code}/pdf', [AdminController::class, 'downloadEticket'])->name('eticket.pdf');
        // Event Management
        Route::get('/events', [AdminController::class, 'events'])->name('events');
        Route::post('/events', [AdminController::class, 'storeEvent'])->name('events.store');
        Route::post('/events/bulk-delete', [AdminController::class, 'bulkDestroyEvent'])->name('events.bulk-destroy');
        Route::put('/events/{id}', [AdminController::class, 'updateEvent'])->name('events.update');
        Route::delete('/events/{id}', [AdminController::class, 'destroyEvent'])->name('events.destroy');

        // Event Category Management
        Route::get('/events/{id}/categories', [AdminController::class, 'eventCategories'])->name('events.categories');
        Route::post('/events/{id}/categories', [AdminController::class, 'storeEventCategory'])->name('events.categories.store');
        Route::put('/events/categories/{category_id}', [AdminController::class, 'updateEventCategory'])->name('events.categories.update');
        Route::delete('/events/categories/{category_id}', [AdminController::class, 'destroyEventCategory'])->name('events.categories.destroy');

        // Admin Management
        Route::get('/admins', [AdminController::class, 'admins'])->name('admins');
        Route::post('/admins', [AdminController::class, 'storeAdmin'])->name('admins.store');
        Route::post('/admins/bulk-delete', [AdminController::class, 'bulkDestroyAdmin'])->name('admins.bulk-destroy');
        Route::put('/admins/{id}', [AdminController::class, 'updateAdmin'])->name('admins.update');
        Route::delete('/admins/{id}', [AdminController::class, 'destroyAdmin'])->name('admins.destroy');
    });
});

// Fallback route for storage files (helps on shared hosting or when storage link is missing)
Route::get('/storage/{path}', function ($path) {
    $filePath = 'public/' . $path;
    if (!\Illuminate\Support\Facades\Storage::exists($filePath)) {
        abort(404);
    }
    return response()->file(\Illuminate\Support\Facades\Storage::path($filePath));
})->where('path', '.*');
