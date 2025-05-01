<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Private\OrderController;
use App\Http\Controllers\Private\UserController;
use App\Http\Controllers\Private\ProductController;
use App\Http\Controllers\Private\CustomerController;
use App\Http\Controllers\Private\DashboardController;

// Include authentication routes
require __DIR__.'/auth.php';

// Public routes
Route::get('/home', function () {
    return view('public.home');
})->name('home');

// Order search routes
Route::get('/order/search', function () {
    return view('public.orders.search');
})->name('order.search.form');

Route::get('/order/search/result', function (Request $request) {
    $customer_number = $request->input('customer_number');
    $invoice_number = $request->input('invoice_number');

    $order = Order::where('customer_number', $customer_number)
                  ->where('invoice_number', $invoice_number)
                  ->first();

    if ($order) {
        return view('public.orders.search', compact('order'));
    } else {
        return redirect()->route('order.search.form')->with('error', 'Order not found.');
    }
})->name('order.search');

// Private routes (authenticated users)
Route::middleware(['auth'])->prefix('private')->name('private.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Orders routes
    Route::resource('orders', OrderController::class);
    Route::get('orders-archived', [OrderController::class, 'archived'])->name('orders.archived');
    Route::post('orders/{order}/restore', [OrderController::class, 'restore'])->name('orders.restore');
    
    // Products routes
    Route::resource('products', ProductController::class);
    
    // Users routes
    Route::resource('users', UserController::class);
    Route::get('/users/{user}/profile', [UserController::class, 'profile'])->name('users.profile');
    
    // Customers routes
    Route::resource('customers', CustomerController::class);
});
