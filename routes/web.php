<?php

use App\Models\Business;
use App\Models\User;
use App\Models\Branch;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BusinessUserController;
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

        return view('dashboard.index', [
            'businessCount' => Business::count(),

            'userCount' => User::count(),

            'branchCount' => Branch::count(),

            'activeBusinessCount' => Business::where(
                'status',
                'active'
            )->count(),
        ]);
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

    Route::resource('users', UserController::class)
        ->except(['show', 'destroy']);

    Route::patch(
        '/users/{user}/toggle-status',
        [UserController::class, 'toggleStatus']
    )->name('users.toggle-status');

    Route::post(
        '/users/{user}/password-reset',
        [UserController::class, 'sendPasswordReset']
    )
        ->middleware('throttle:3,1')
        ->name('users.password-reset');

    Route::resource(
        'businesses',
        BusinessController::class
    )->except([
        'show',
        'destroy',
    ]);

    Route::patch(
        '/businesses/{business}/status',
        [BusinessController::class, 'updateStatus']
    )->name('businesses.status');

    Route::resource(
        'branches',
        BranchController::class
    )->except([
        'show',
        'destroy',
    ]);

    Route::patch(
        '/branches/{branch}/status',
        [BranchController::class, 'updateStatus']
    )->name('branches.status');

    Route::patch(
        '/branches/{branch}/make-main',
        [BranchController::class, 'makeMain']
    )->name('branches.make-main');

    Route::get(
        '/businesses/{business}/users',
        [BusinessUserController::class, 'index']
    )->name('businesses.users.index');

    Route::post(
        '/businesses/{business}/users',
        [BusinessUserController::class, 'store']
    )->name('businesses.users.store');

    Route::patch(
        '/businesses/{business}/users/{user}/status',
        [BusinessUserController::class, 'updateStatus']
    )->name('businesses.users.status');
});
