<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/event/{id}', [HomeController::class, 'showEvent'])->name('event.show');

// Sitemap SEO
Route::get('/sitemap.xml', function () {
    $events = collect(\App\Http\Controllers\HomeController::loadEvents());

    return response()->view('sitemap', [
        'events' => $events,
    ])->header('Content-Type', 'text/xml');
})->name('sitemap');

// robots.txt dinamis supaya URL sitemap selalu sesuai APP_URL
Route::get('/robots.txt', function () {
    return response()->view('robots')
        ->header('Content-Type', 'text/plain');
})->name('robots');

// Alur Pembelian Tiket — wajib login sebagai peserta, supaya setiap pesanan
// terikat ke akun pembeli dan bisa dilacak lewat /dashboard.
// Route pembelian dinamai 'event.register' agar tidak bentrok dengan route
// 'register' milik Breeze (pendaftaran akun) yang di-load dari routes/auth.php.
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/register-event', [RegistrationController::class, 'showRegistrationForm'])->name('event.register');
    Route::post('/register-event', [RegistrationController::class, 'submitRegistration']);
    Route::get('/registration-success', [RegistrationController::class, 'success'])->name('registration.success');

    // Detail pesanan + unggah ulang bukti kalau ditolak admin
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/proof', [OrderController::class, 'updateProof'])->name('orders.proof');
});

// E-Ticket
Route::get('/ticket/{ticket_code}', [TicketController::class, 'showTicket'])->name('ticket.show');
Route::get('/ticket/{ticket_code}/pdf', [TicketController::class, 'downloadPdf'])->name('ticket.pdf');

// Area Peserta (role: user)
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Halaman tiket saja — hanya tiket yang sudah terbit
    Route::get('/tickets', [DashboardController::class, 'tickets'])->name('tickets');
});

// Profil — semua role yang sudah login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Panel — hanya role admin & super_admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/participants', [AdminController::class, 'participants'])->name('participants');
        Route::post('/participants/bulk-delete', [AdminController::class, 'bulkDestroyParticipant'])->name('participants.bulk-delete');
        Route::get('/participants/{id}/edit', [AdminController::class, 'editParticipant'])->name('participants.edit');
        Route::put('/participants/{id}', [AdminController::class, 'updateParticipant'])->name('participants.update');
        Route::delete('/participants/{id}', [AdminController::class, 'deleteParticipant'])->name('participants.delete');
        Route::get('/export-csv', [AdminController::class, 'exportCSV'])->name('export');
        // Antrian verifikasi pesanan (satu baris per pesanan, bukan per tiket)
        Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
        Route::post('/orders/bulk-delete', [AdminController::class, 'bulkDestroyOrder'])->name('orders.bulk-delete');
        Route::post('/orders/{id}/approve', [AdminController::class, 'approveOrder'])->name('orders.approve');
        Route::post('/orders/{id}/reject', [AdminController::class, 'rejectOrder'])->name('orders.reject');
        // Rekening tujuan transfer per event.
        // Admin event hanya bisa melihat; menambah/mengubah/menghapus dibatasi
        // super admin lewat checkSuperAdmin() di dalam controller.
        Route::get('/payment-accounts', [AdminController::class, 'paymentAccounts'])->name('payment-accounts');
        Route::post('/payment-accounts', [AdminController::class, 'storePaymentAccount'])->name('payment-accounts.store');
        Route::put('/payment-accounts/{id}', [AdminController::class, 'updatePaymentAccount'])->name('payment-accounts.update');
        Route::delete('/payment-accounts/{id}', [AdminController::class, 'destroyPaymentAccount'])->name('payment-accounts.destroy');

        // Formulir pendaftaran per event — admin mengatur event yang dia tangani
        Route::get('/form-fields', [AdminController::class, 'formFields'])->name('form-fields');
        Route::post('/form-fields', [AdminController::class, 'storeFormField'])->name('form-fields.store');
        Route::put('/form-fields/{id}', [AdminController::class, 'updateFormField'])->name('form-fields.update');
        Route::delete('/form-fields/{id}', [AdminController::class, 'destroyFormField'])->name('form-fields.destroy');

        // Gambar event — admin event boleh mengurus gambar event yang dia tangani,
        // pembatasan per-event ditangani resolveFormEvent() di dalam controller.
        Route::get('/event-image', [AdminController::class, 'eventImage'])->name('event-image');
        Route::post('/event-image', [AdminController::class, 'updateEventImage'])->name('event-image.update');
        Route::delete('/event-image', [AdminController::class, 'destroyEventImage'])->name('event-image.destroy');

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

require __DIR__ . '/auth.php';
