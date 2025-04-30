<?php

use Inertia\Inertia;
use App\Enums\RolesEnum;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\CarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;

// Guest Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');
Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::get('/cars/{car:slug}', [CarController::class, 'show'])->name('cars.show');

// Redirect /admin to admin dashboard
Route::get('/admin', function() {
    return redirect()->route('admin.dashboard');
});

// Auth routes
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User dashboard routes
    Route::prefix('user')->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('User/Dashboard');
        })->name('user.dashboard');

        Route::get('/cars', function () {
            return Inertia::render('User/Cars');
        })->name('user.cars');

        // Car routes
        Route::post('/cars', [CarController::class, 'store'])->name('user.cars.store');
        Route::get('/cars/create', function () {
            return Inertia::render('User/CarsCreate');
        })->name('user.cars.create');

        Route::get('/appointments', function () {
            return Inertia::render('User/Appointments');
        })->name('user.appointments');

        Route::get('/bids', function () {
            return Inertia::render('User/Bids');
        })->name('user.bids');

        Route::get('/sold-cars', function () {
            return Inertia::render('User/SoldCars');
        })->name('user.sold-cars');

        Route::get('/purchased-cars', function () {
            return Inertia::render('User/PurchasedCars');
        })->name('user.purchased-cars');

        Route::get('/saved', function () {
            return Inertia::render('User/Saved');
        })->name('user.saved');
    });

    // Legacy user routes - keep for compatibility
    Route::group([], function () {
        // User dashboard
        Route::get('/dashboard', function () {
            return Inertia::render('User/Dashboard');
        })->name('dashboard');

        // Car routes
        Route::post('/cars', [CarController::class, 'store'])->name('cars.store');
        Route::get('/cars/create', [CarController::class, 'create'])->name('cars.create');
        Route::put('/cars/{car}', [CarController::class, 'update'])->name('cars.update');
        Route::get('/cars/{car}/edit', [CarController::class, 'edit'])->name('cars.edit');
        Route::delete('/cars/{car}', [CarController::class, 'destroy'])->name('cars.destroy');
        Route::patch('/cars/{car}/deactivate', [CarController::class, 'deactivate'])->name('cars.deactivate');

        // Appointment routes
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/create/{car}', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments/{appointment}/submit-bid', [AppointmentController::class, 'submitBid'])->name('appointments.submitBid');
        Route::post('/appointments/{appointment}/cancel-bid', [AppointmentController::class, 'cancelBid'])->name('appointments.cancelBid');

        // Transaction routes
        Route::get('/transactions/{transaction}/invoice', [App\Http\Controllers\TransactionController::class, 'generateInvoice'])->name('transaction.invoice');
        Route::patch('/transactions/{transaction}/pay', [App\Http\Controllers\TransactionController::class, 'markAsPaid'])->name('transaction.pay');

        // Payment routes
        Route::get('/payment/{transaction}', [PaymentController::class, 'process'])->name('payment.process');
        Route::post('/payment/{transaction}/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::get('/payment/{transaction}/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    });

    // Admin routes
    Route::middleware(['role:' . RolesEnum::Admin->value])->prefix('admin')->group(function () {
        // Admin dashboard
        Route::get('/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.dashboard');

        // Admin pages
        Route::get('/users', function () {
            return Inertia::render('Admin/Users');
        })->name('admin.users');

        Route::get('/cars', function () {
            return Inertia::render('Admin/Cars');
        })->name('admin.cars');

        Route::get('/appointments', [AdminController::class, 'appointments'])->name('admin.appointments');

        Route::get('/transactions', function () {
            return Inertia::render('Admin/Transactions');
        })->name('admin.transactions');

        Route::get('/settings', function () {
            return Inertia::render('Admin/Settings');
        })->name('admin.settings');

        // Contact Messages
        Route::get('/contact-messages', [App\Http\Controllers\Admin\ContactMessageController::class, 'index'])
            ->name('admin.contact-messages.index');
        Route::get('/contact-messages/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'show'])
            ->name('admin.contact-messages.show');
        Route::patch('/contact-messages/{contactMessage}/mark-as-read', [App\Http\Controllers\Admin\ContactMessageController::class, 'markAsRead'])
            ->name('admin.contact-messages.mark-read');
        Route::post('/contact-messages/mark-multiple-as-read', [App\Http\Controllers\Admin\ContactMessageController::class, 'markMultipleAsRead'])
            ->name('admin.contact-messages.mark-multiple-read');
        Route::delete('/contact-messages/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])
            ->name('admin.contact-messages.destroy');

        // Legacy admin routes - keep for compatibility
        Route::patch('/users/{user}/assign-role', [AdminController::class, 'assignRole'])->name('admin.users.assignRole');

        // Car approvals
        Route::patch('/cars/{car}/approve', [AdminController::class, 'approveCar'])->name('admin.cars.approve');
        Route::patch('/cars/{car}/reject', [AdminController::class, 'rejectCar'])->name('admin.cars.reject');

        // Appointment approvals
        Route::patch('/appointments/{appointment}/approve', [AdminController::class, 'approveAppointment'])->name('admin.appointments.approve');
        Route::patch('/appointments/{appointment}/reject', [AdminController::class, 'rejectAppointment'])->name('admin.appointments.reject');

        // Bid approvals
        Route::patch('/appointments/{appointment}/approve-bid', [AdminController::class, 'approveBid'])->name('admin.appointments.approveBid');
        Route::patch('/appointments/{appointment}/reject-bid', [AdminController::class, 'rejectBid'])->name('admin.appointments.rejectBid');
        Route::get('/cars/{car}/alternative-bids', [AdminController::class, 'alternativeBids'])->name('admin.cars.alternativeBids');

        // Transactions
        Route::post('/transactions/finalize/{appointment}', [TransactionController::class, 'finalize'])->name('admin.transactions.finalize');

        // Invoice generation
        Route::get('/transactions/{transaction}/invoice', [InvoiceController::class, 'generateInvoice'])->name('transactions.invoice');
    });
});

require __DIR__.'/auth.php';
