<?php

use App\Http\Controllers\FoodController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Models\Food;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:user')->group(function () {
    Route::get('/', function () {
        $foods = Food::all();
        return view('landing.index', compact('foods'));
    })->name('view.landing');
    Route::get('/signup', function () {
        return view('auth.signup');
    })->name('view.signup');
    Route::get('/login', function () {
        return view('auth.login');
    })->name('view.login');
    Route::post('signup', [UserController::class, 'create'])->name('user.signup');
    Route::post('login', [UserController::class, 'authenticate'])->name('user.login');
});

// TODO implement admin
Route::prefix('admin')->group(function () {
    Route::get('');
})->middleware('auth');

Route::prefix('user')->group(function () {
    Route::middleware('auth:user')->group(function () {
        Route::get('home', [UserController::class, 'index'])->name('user.view.home');

        Route::get('order', [UserController::class, 'order'])->name('user.view.order');
        Route::post('order', [TransactionController::class, 'create'])->name('txn.create');

        Route::get('history', [UserController::class, 'history'])->name('user.view.history');
        Route::get('history/{transaction:invoice_id}', [UserController::class, 'historyDetail'])->name('user.view.historyDetail')->whereAlphaNumeric('transaction');

        Route::get('profile', [UserController::class, 'edit'])->name('user.view.profile');
        Route::put('profile', [UserController::class, 'update'])->name('user.update');

        Route::post('logout', [UserController::class, 'logout'])->name('user.logout');
    });
});
