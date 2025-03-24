<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{UserController, AuthController, KlaimBBMController, DashboardController, BBMController, DepartmentController, KendaraanController};
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

// User registration routes
Route::get('/register', [UserController::class, 'register'])->name('register');
Route::post('/register', [UserController::class, 'store'])->name('register.store');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['role:Admin'])->group(function () {
    Route::get('/pending-approvals', [UserController::class, 'pendingApprovals'])->name('users.pending');
    Route::post('/users/{id}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{id}/reject', [UserController::class, 'reject'])->name('users.reject');
    Route::post('/users/destroy', [UserController::class, 'destroy'])->name('users.destroy');



    // Department Routes
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    // BBM Routes
    Route::get('/bbm', [BBMController::class, 'index'])->name('bbm.index');
    Route::get('/bbm/create', [BBMController::class, 'create'])->name('bbm.create');
    Route::post('/bbm', [BBMController::class, 'store'])->name('bbm.store');
    Route::get('/bbm/{bbm}/edit', [BBMController::class, 'edit'])->name('bbm.edit');
    Route::put('/bbm/{bbm}', [BBMController::class, 'update'])->name('bbm.update');
    Route::delete('/bbm/{bbm}', [BBMController::class, 'destroy'])->name('bbm.destroy');

    /// Kendaraan Routes
    Route::get('/kendaraan/create', [KendaraanController::class, 'create'])->name('kendaraan.create');
    Route::post('/kendaraan', [KendaraanController::class, 'store'])->name('kendaraan.store');
    Route::get('/kendaraan/{kendaraan}/edit', [KendaraanController::class, 'edit'])->name('kendaraan.edit');
    Route::put('/kendaraan/{kendaraan}', [KendaraanController::class, 'update'])->name('kendaraan.update');
    Route::delete('/kendaraan/{kendaraan}', [KendaraanController::class, 'destroy'])->name('kendaraan.destroy');
    Route::get('kendaraan/export', [KendaraanController::class, 'export'])->name('kendaraan.export');
});

// Claim management routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/claims', [KlaimBBMController::class, 'index'])->name('claims.index');
    Route::get('/claims/create', [KlaimBBMController::class, 'create'])->name('claims.create');
    Route::post('/claims', [KlaimBBMController::class, 'store'])->name('claims.store');
    Route::get('/claims/{claim}', [KlaimBBMController::class, 'show'])->name('claims.show');
    Route::get('/claims/{claim}/edit', [KlaimBBMController::class, 'edit'])->name('claims.edit');
    Route::put('/claims/{claim}', [KlaimBBMController::class, 'update'])->name('claims.update');
    Route::delete('/claims/{claim}', [KlaimBBMController::class, 'destroy'])->name('claims.destroy');
    Route::get('/claims/sisa-saldo/{periode}', [KlaimBBMController::class, 'getSisaSaldo']);
    Route::post('/claims/export', [KlaimBBMController::class, 'export'])->name('claims.export');


    // Route untuk print
    Route::get('/claims/{claim}/preview', [KlaimBBMController::class, 'preview'])->name('claims.preview');
    Route::get('/claims/{claim}/pdf', [KlaimBBMController::class, 'pdf'])->name('claims.pdf');


    // Kendaraan Routes  
    Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan.index');

    // User Profile Routes
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.index');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password');
});
