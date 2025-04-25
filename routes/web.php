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

// Guest Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');
Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::get('/cars/{car:slug}', [CarController::class, 'show'])->name('cars.show');

// Auth routes
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User routes
    Route::middleware(['verified'])->group(function () {
        // User dashboard
        Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

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
        
        // Transaction routes
        Route::get('/transactions/{transaction}/invoice', [App\Http\Controllers\TransactionController::class, 'generateInvoice'])->name('transaction.invoice');
        Route::patch('/transactions/{transaction}/pay', [App\Http\Controllers\TransactionController::class, 'markAsPaid'])->name('transaction.pay');
    });

    // Admin routes
    Route::middleware(['role:' . RolesEnum::Admin->value])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // User management
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::patch('/users/{user}/assign-role', [AdminController::class, 'assignRole'])->name('admin.users.assignRole');

        // Car approvals
        Route::get('/cars', [AdminController::class, 'cars'])->name('admin.cars');
        Route::patch('/cars/{car}/approve', [AdminController::class, 'approveCar'])->name('admin.cars.approve');
        Route::patch('/cars/{car}/reject', [AdminController::class, 'rejectCar'])->name('admin.cars.reject');

        // Appointment approvals
        Route::get('/appointments', [AdminController::class, 'appointments'])->name('admin.appointments');
        Route::patch('/appointments/{appointment}/approve', [AdminController::class, 'approveAppointment'])->name('admin.appointments.approve');
        Route::patch('/appointments/{appointment}/reject', [AdminController::class, 'rejectAppointment'])->name('admin.appointments.reject');

        // Transactions
        Route::get('/transactions', [AdminController::class, 'transactions'])->name('admin.transactions');
        Route::post('/transactions/finalize/{appointment}', [TransactionController::class, 'finalize'])->name('admin.transactions.finalize');
        
        // Invoice generation
        Route::get('/transactions/{transaction}/invoice', [InvoiceController::class, 'generateInvoice'])->name('transactions.invoice');
    });
});

require __DIR__.'/auth.php';
