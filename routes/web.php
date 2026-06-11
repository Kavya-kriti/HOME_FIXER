<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Customer\ServiceRequestController;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboard;
use App\Http\Controllers\Provider\JobController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ServiceController;

// ── Smart Home Route (Moved OUT of the guest group) ──────────────
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'provider') return redirect()->route('provider.dashboard');
        return redirect()->route('customer.dashboard');
    }
    return redirect()->route('login');
});

// ── Guest only ────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
});

Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ... (Keep all your Customer, Provider, and Admin routes exactly the same below this line!)
// ── Customer ──────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboard::class, 'index'])->name('dashboard');
    Route::get('/request',   [ServiceRequestController::class, 'create'])->name('request.create');
    Route::post('/request',  [ServiceRequestController::class, 'store'])->name('request.store');
    Route::get('/requests',  [ServiceRequestController::class, 'index'])->name('requests');
    Route::get('/requests/{serviceRequest}/recommendation',
                             [ServiceRequestController::class, 'showRecommendation'])->name('recommendation');
    Route::post('/requests/{serviceRequest}/accept-provider',
                             [ServiceRequestController::class, 'acceptProvider'])->name('accept-provider');
});

// ── Provider ──────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:provider'])->prefix('provider')->name('provider.')->group(function () {
    Route::get('/dashboard',        [ProviderDashboard::class, 'index'])->name('dashboard');
    Route::get('/jobs/available',   [JobController::class, 'available'])->name('jobs.available');
    Route::get('/jobs',             [JobController::class, 'myJobs'])->name('my-jobs');
    Route::post('/jobs/{jobAssignment}/accept',   [JobController::class, 'accept'])->name('jobs.accept');
    Route::post('/jobs/{jobAssignment}/reject',   [JobController::class, 'reject'])->name('jobs.reject');
    Route::post('/jobs/{jobAssignment}/start',    [JobController::class, 'start'])->name('jobs.start');
    Route::post('/jobs/{jobAssignment}/complete', [JobController::class, 'complete'])->name('jobs.complete');
    Route::post('/jobs/{jobAssignment}/price',    [JobController::class, 'setPrice'])->name('jobs.price');
    Route::post('/toggle-availability',           [JobController::class, 'toggleAvailability'])->name('toggle-availability');
});

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Overview
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // User management
    Route::get('/users',                      [UserController::class, 'index'])->name('users');
    Route::post('/users/{user}/verify',       [UserController::class, 'verifyProvider'])->name('users.verify');
    Route::post('/users/{user}/revoke',       [UserController::class, 'revokeProvider'])->name('users.revoke');
    Route::post('/users/{user}/toggle',       [UserController::class, 'toggleStatus'])->name('users.toggle');
    Route::delete('/users/{user}',            [UserController::class, 'destroy'])->name('users.destroy');

    // Service requests
    Route::get('/requests',                                          [ServiceController::class, 'requests'])->name('requests');
    Route::post('/requests/{serviceRequest}/assign',                 [ServiceController::class, 'assignProvider'])->name('requests.assign');
    Route::post('/requests/{serviceRequest}/cancel',                 [ServiceController::class, 'cancelRequest'])->name('requests.cancel');

    // AI logs
    Route::get('/ai-logs', [ServiceController::class, 'aiLogs'])->name('ai-logs');
});
