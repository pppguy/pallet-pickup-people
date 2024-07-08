<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Admin routes
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/api/admin/customers/{customer}/reminder', [AdminController::class, 'sendReminder']);
    Route::get('/admin/customers/status', [AdminController::class, 'getCustomerStatusData'])->name('admin.customers.status');
    Route::post('/admin/customers/{customer}/update', [AdminController::class, 'updateCustomer'])->name('admin.customers.update');
    //Route::post('/admin/customers/{customer}/reminder', [AdminController::class, 'sendReminder'])->name('admin.customers.reminder');
    //Route::post('/admin/customers/{customerId}/reminder', [AdminController::class, 'sendReminder']);
    Route::get('/admin/customers/{customerId}/reminder', [AdminController::class, 'sendReminder']);
    Route::get('/customer/response/{response}/{id}', [AdminController::class, 'handleResponse'])->name('customer.response');
    // Route::post('/send-reminder', [AdminController::class, 'sendReminder']);


    // Driver routes
    Route::get('/driver', [DriverController::class, 'dashboard'])->name('driver.dashboard');
    Route::post('/driver/pickups/{pickupId}/claim', [DriverController::class, 'claimPickup']);
    Route::post('/driver/pickups/{pickupId}/complete', [DriverController::class, 'completePickup']);
});

require __DIR__ . '/auth.php';
