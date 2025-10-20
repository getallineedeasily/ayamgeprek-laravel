<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Models\Food;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:user,admin')->group(function () {
    Route::get('/', function () {
        $foods = Food::all();
        return view('landing.index', compact('foods'));
    })->name('view.landing');

    Route::get('/signup', function () {
        return view('auth.signup');
    })->name('view.signup');
    Route::post('signup', [UserController::class, 'create'])->name('user.signup');

    Route::get('/login', function () {
        return view('auth.login');
    })->name('view.login');
    Route::post('login', [UserController::class, 'authenticate'])->name('user.login');
});


Route::prefix('admin')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', function () {
            return view('auth.login-admin');
        })->name('view.admin.login');
        Route::post('login', [AdminController::class, 'authenticate'])->name('admin.login');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::get('home', [AdminController::class, 'index'])->name('admin.view.home');

        Route::get('transaction', [TransactionController::class, 'index'])->name('admin.view.txn');

        Route::get('food', [FoodController::class, 'index'])->name('admin.view.food');
        Route::get('food/create', [FoodController::class, 'create'])->name('admin.create.food');
        Route::post('food/create', [FoodController::class, 'store'])->name('admin.store.food');
        Route::get('food/{food}/edit', [FoodController::class, 'edit'])->name('admin.edit.food');
        Route::patch('food/{food}/edit', [FoodController::class, 'update'])->name('admin.update.food');
        Route::delete('food/{food}/delete', [FoodController::class, 'destroy'])->name('admin.destroy.food');
        
        Route::get('customer', [AdminController::class, 'customer'])->name('admin.view.customer');
        Route::patch('customer/{user}', [AdminController::class, 'resetCustomerPassword'])->name('admin.reset.customer.password');
        
        Route::get('report', [AdminController::class, 'report'])->name('admin.view.report');

        Route::post('logout', [AdminController::class, 'logout'])->name('admin.logout');
    });
});

Route::prefix('user')->group(function () {
    Route::middleware('auth:user')->group(function () {
        Route::get('home', [UserController::class, 'index'])->name('user.view.home');

        Route::get('order', [UserController::class, 'order'])->name('user.view.order');
        Route::post('order', [TransactionController::class, 'create'])->name('user.create.txn');

        Route::get('history', [UserController::class, 'history'])->name('user.view.history');
        Route::get('history/{transaction:invoice_id}', [UserController::class, 'historyDetail'])->whereAlphaNumeric('transaction')->name('user.view.history.detail');
        Route::patch('history/{transaction:invoice_id}', [UserController::class, 'uploadPaymentProof'])->whereAlphaNumeric('transaction')->name('user.upload.payment.proof');
        Route::get('history/{transaction:invoice_id}/payment-proof', [UserController::class, 'paymentProof'])->whereAlphaNumeric('transaction')->name('user.view.payment.proof');
        Route::patch('history/{transaction:invoice_id}/cancel', [UserController::class, 'cancelOrder'])->whereAlphaNumeric('transaction')->name('user.cancel.order');

        Route::get('profile', [UserController::class, 'edit'])->name('user.view.profile');
        Route::put('profile', [UserController::class, 'update'])->name('user.update');

        Route::post('logout', [UserController::class, 'logout'])->name('user.logout');
    });
});
