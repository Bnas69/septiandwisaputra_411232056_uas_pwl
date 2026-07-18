<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', fn () => redirect()->route('login'));

// API: check availability (guest, throttled)
Route::middleware('guest')->group(function () {
    Route::get('/api/check-username', function (\Illuminate\Http\Request $request) {
        $username = $request->validate(['username' => 'required|string|min:3|max:50']);
        $exists = DB::table('users')->where('username', $username['username'])->exists();
        return response()->json(['available' => !$exists]);
    })->middleware('throttle:10,1')->name('api.check-username');

    Route::get('/api/check-email', function (\Illuminate\Http\Request $request) {
        $email = $request->validate(['email' => 'required|email']);
        $exists = DB::table('users')->where('email', $email['email'])->exists();
        return response()->json(['available' => !$exists]);
    })->middleware('throttle:10,1')->name('api.check-email');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset')->where('token', '[a-zA-Z0-9]+');
    Route::put('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated routes
Route::middleware(['auth', 'ensure.user.active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile & Password
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [ChangePasswordController::class, 'showForm'])->name('change.password.form');
    Route::put('/change-password', [ChangePasswordController::class, 'change'])->name('change.password');

    // Products
    Route::middleware('role:developer,owner,pegawai')->group(function () {
        Route::resource('products', ProductController::class)->except(['show']);
    });

    // Sales
    Route::middleware('role:developer,owner,pegawai')->group(function () {
        Route::resource('sales', SalesController::class)->only(['index', 'create', 'store', 'show']);
        Route::get('/sales/{sale}/receipt', [SalesController::class, 'receipt'])->name('sales.receipt');
    });

    // Stock
    Route::middleware('role:developer,owner,pegawai')->group(function () {
        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::get('/create', [StockController::class, 'create'])->name('create');
            Route::post('/', [StockController::class, 'store'])->name('store');
        });
    });

    // Reports
    Route::middleware('role:developer,owner')->group(function () {
        Route::prefix('report')->name('report.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('excel');
            Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('pdf');
        });
    });

    // Merchants
    Route::middleware('role:developer,owner,pegawai')->group(function () {
        Route::get('/merchants', [MerchantController::class, 'index'])->name('merchants.index');
        Route::get('/merchants/{code}', [MerchantController::class, 'show'])->name('merchants.show');
    });

    // User Management - Developer only
    Route::middleware('role:developer')->group(function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });
    });
});
