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
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BusinessContextController;
use App\Http\Controllers\BusinessDashboardController;
use App\Http\Controllers\BusinessBranchController;
use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\BusinessUserBranchController;
use App\Http\Controllers\BranchContextController;
use App\Http\Controllers\BusinessCategoryController;
use App\Http\Controllers\BusinessUnitController;
use App\Http\Controllers\BusinessProductController;
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
    /*
---------------USUARIOS-----------------
*/
    Route::middleware('platform.admin')->group(function () {

        Route::resource(
            'users',
            UserController::class
        )->except([
            'show',
            'destroy',
        ]);

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
    });
    /*
-----------------USUARIOS DEL NEGOCIO-----------------
*/
    Route::get(
        '/businesses/{business}/users',
        [BusinessUserController::class, 'index']
    )
        ->middleware(
            'business.permission:users.view'
        )
        ->name('businesses.users.index');

    Route::post(
        '/businesses/{business}/users',
        [BusinessUserController::class, 'store']
    )
        ->middleware('platform.admin')
        ->name('businesses.users.store');

    Route::patch(
        '/businesses/{business}/users/{user}/status',
        [BusinessUserController::class, 'updateStatus']
    )
        ->middleware(
            'business.permission:users.status'
        )
        ->name('businesses.users.status');

    Route::patch(
        '/businesses/{business}/users/{user}/role',
        [BusinessUserController::class, 'updateRole']
    )
        ->middleware(
            'business.permission:roles.assign'
        )
        ->name('businesses.users.role');

    Route::get(
        '/businesses/{business}/users/{user}/branches',
        [BusinessUserBranchController::class, 'edit']
    )
        ->middleware(
            'business.permission:users.branches'
        )
        ->name(
            'businesses.users.branches.edit'
        );

    Route::put(
        '/businesses/{business}/users/{user}/branches',
        [BusinessUserBranchController::class, 'update']
    )
        ->middleware(
            'business.permission:users.branches'
        )
        ->name(
            'businesses.users.branches.update'
        );

    Route::get(
        '/businesses/{business}/branch/select',
        [BranchContextController::class, 'index']
    )
        ->name('business.branch.select');


    Route::post(
        '/businesses/{business}/branch/select',
        [BranchContextController::class, 'switch']
    )
        ->name('business.branch.switch');

    Route::get(
        '/businesses/{business}/categories',
        [BusinessCategoryController::class, 'index']
    )
        ->middleware(
            'business.permission:categories.view'
        )
        ->name('businesses.categories.index');


    Route::get(
        '/businesses/{business}/categories/create',
        [BusinessCategoryController::class, 'create']
    )
        ->middleware(
            'business.permission:categories.create'
        )
        ->name('businesses.categories.create');


    Route::post(
        '/businesses/{business}/categories',
        [BusinessCategoryController::class, 'store']
    )
        ->middleware(
            'business.permission:categories.create'
        )
        ->name('businesses.categories.store');


    Route::get(
        '/businesses/{business}/categories/{category}/edit',
        [BusinessCategoryController::class, 'edit']
    )
        ->middleware(
            'business.permission:categories.update'
        )
        ->name('businesses.categories.edit');


    Route::put(
        '/businesses/{business}/categories/{category}',
        [BusinessCategoryController::class, 'update']
    )
        ->middleware(
            'business.permission:categories.update'
        )
        ->name('businesses.categories.update');


    Route::patch(
        '/businesses/{business}/categories/{category}/status',
        [BusinessCategoryController::class, 'updateStatus']
    )
        ->middleware(
            'business.permission:categories.status'
        )
        ->name('businesses.categories.status');

    Route::get(
        '/businesses/{business}/units',
        [BusinessUnitController::class, 'index']
    )
        ->middleware('business.permission:units.view')
        ->name('businesses.units.index');


    Route::get(
        '/businesses/{business}/units/create',
        [BusinessUnitController::class, 'create']
    )
        ->middleware('business.permission:units.create')
        ->name('businesses.units.create');


    Route::post(
        '/businesses/{business}/units',
        [BusinessUnitController::class, 'store']
    )
        ->middleware('business.permission:units.create')
        ->name('businesses.units.store');


    Route::get(
        '/businesses/{business}/units/{unit}/edit',
        [BusinessUnitController::class, 'edit']
    )
        ->middleware('business.permission:units.update')
        ->name('businesses.units.edit');


    Route::put(
        '/businesses/{business}/units/{unit}',
        [BusinessUnitController::class, 'update']
    )
        ->middleware('business.permission:units.update')
        ->name('businesses.units.update');


    Route::patch(
        '/businesses/{business}/units/{unit}/status',
        [BusinessUnitController::class, 'updateStatus']
    )
        ->middleware('business.permission:units.status')
        ->name('businesses.units.status');

    Route::get(
        '/businesses/{business}/products',
        [BusinessProductController::class, 'index']
    )
        ->middleware('business.permission:products.view')
        ->name('businesses.products.index');


    Route::get(
        '/businesses/{business}/products/create',
        [BusinessProductController::class, 'create']
    )
        ->middleware('business.permission:products.create')
        ->name('businesses.products.create');


    Route::post(
        '/businesses/{business}/products',
        [BusinessProductController::class, 'store']
    )
        ->middleware('business.permission:products.create')
        ->name('businesses.products.store');


    Route::get(
        '/businesses/{business}/products/{product}/edit',
        [BusinessProductController::class, 'edit']
    )
        ->middleware('business.permission:products.update')
        ->name('businesses.products.edit');


    Route::put(
        '/businesses/{business}/products/{product}',
        [BusinessProductController::class, 'update']
    )
        ->middleware('business.permission:products.update')
        ->name('businesses.products.update');


    Route::patch(
        '/businesses/{business}/products/{product}/status',
        [BusinessProductController::class, 'updateStatus']
    )
        ->middleware('business.permission:products.status')
        ->name('businesses.products.status');
    /*
-------------------ROLES-------------------
*/
    Route::get(
        '/businesses/{business}/roles',
        [RoleController::class, 'index']
    )
        ->middleware(
            'business.permission:roles.view'
        )
        ->name('businesses.roles.index');

    Route::get(
        '/businesses/{business}/roles/create',
        [RoleController::class, 'create']
    )
        ->middleware(
            'business.permission:roles.create'
        )
        ->name('businesses.roles.create');


    Route::post(
        '/businesses/{business}/roles',
        [RoleController::class, 'store']
    )
        ->middleware(
            'business.permission:roles.create'
        )
        ->name('businesses.roles.store');

    Route::get(
        '/businesses/{business}/roles/{role}/edit',
        [RoleController::class, 'edit']
    )
        ->middleware(
            'business.permission:roles.update'
        )
        ->name('businesses.roles.edit');


    Route::put(
        '/businesses/{business}/roles/{role}',
        [RoleController::class, 'update']
    )
        ->middleware(
            'business.permission:roles.update'
        )
        ->name('businesses.roles.update');

    Route::delete(
        '/businesses/{business}/roles/{role}',
        [RoleController::class, 'destroy']
    )
        ->middleware(
            'business.permission:roles.delete'
        )
        ->name('businesses.roles.destroy');

    /*
-------------------NEGOCIO-------------------
*/
    Route::get(
        '/business/select',
        [BusinessContextController::class, 'index']
    )->name('business.select');


    Route::post(
        '/business/select',
        [BusinessContextController::class, 'switch']
    )->name('business.switch');


    Route::get(
        '/businesses/{business}/dashboard',
        [BusinessDashboardController::class, 'index']
    )
        ->middleware(
            'business.permission:dashboard.view'
        )
        ->name('business.dashboard');

    Route::get(
        '/businesses/{business}/branches',
        [BusinessBranchController::class, 'index']
    )
        ->middleware(
            'business.permission:branches.view'
        )
        ->name('businesses.branches.index');


    Route::get(
        '/businesses/{business}/branches/create',
        [BusinessBranchController::class, 'create']
    )
        ->middleware(
            'business.permission:branches.create'
        )
        ->name('businesses.branches.create');


    Route::post(
        '/businesses/{business}/branches',
        [BusinessBranchController::class, 'store']
    )
        ->middleware(
            'business.permission:branches.create'
        )
        ->name('businesses.branches.store');


    Route::get(
        '/businesses/{business}/branches/{branch}/edit',
        [BusinessBranchController::class, 'edit']
    )
        ->middleware(
            'business.permission:branches.update'
        )
        ->name('businesses.branches.edit');


    Route::put(
        '/businesses/{business}/branches/{branch}',
        [BusinessBranchController::class, 'update']
    )
        ->middleware(
            'business.permission:branches.update'
        )
        ->name('businesses.branches.update');


    Route::patch(
        '/businesses/{business}/branches/{branch}/status',
        [BusinessBranchController::class, 'updateStatus']
    )
        ->middleware(
            'business.permission:branches.status'
        )
        ->name('businesses.branches.status');


    Route::patch(
        '/businesses/{business}/branches/{branch}/make-main',
        [BusinessBranchController::class, 'makeMain']
    )
        ->middleware(
            'business.permission:branches.update'
        )
        ->name('businesses.branches.make-main');

    Route::get(
        '/businesses/{business}/settings',
        [BusinessSettingsController::class, 'edit']
    )
        ->middleware(
            'business.permission:business.view'
        )
        ->name('businesses.settings.edit');


    Route::put(
        '/businesses/{business}/settings',
        [BusinessSettingsController::class, 'update']
    )
        ->middleware(
            'business.permission:business.update'
        )
        ->name('businesses.settings.update');
});
