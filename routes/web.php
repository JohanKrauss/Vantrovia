<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Invitados
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [
        LoginController::class,
        'showLogin'
    ])->name('login');

    Route::post('/login', [
        LoginController::class,
        'login'
    ])
        ->middleware('throttle:login')
        ->name('login.authenticate');


    Route::get('/forgot-password', [
        ForgotPasswordController::class,
        'showLinkRequestForm'
    ])->name('password.request');

    Route::post('/forgot-password', [
        ForgotPasswordController::class,
        'sendResetLinkEmail'
    ])
        ->middleware('throttle:5,1')
        ->name('password.email');


    Route::get('/reset-password/{token}', [
        ResetPasswordController::class,
        'showResetForm'
    ])->name('password.reset');

    Route::post('/reset-password', [
        ResetPasswordController::class,
        'reset'
    ])->name('password.update');
});


/*
|--------------------------------------------------------------------------
| Usuarios autenticados
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard');


    Route::get('/profile/password', [
        PasswordController::class,
        'edit'
    ])->name('password.edit');

    Route::put('/profile/password', [
        PasswordController::class,
        'update'
    ])->name('password.change');


    Route::post('/logout', [
        LoginController::class,
        'logout'
    ])->name('logout');
});
