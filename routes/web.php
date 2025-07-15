<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PenggunaanController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PembayaranController;

// Public routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Pelanggan authentication routes
Route::get('/pelanggan/login', [AuthController::class, 'showPelangganLoginForm'])->name('pelanggan.login');
Route::post('/pelanggan/login', [AuthController::class, 'pelangganLogin'])->name('pelanggan.login.submit');
Route::post('/pelanggan/logout', [AuthController::class, 'pelangganLogout'])->name('pelanggan.logout');

// Admin routes (requires authentication)
Route::middleware('ensure.auth:web')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pelanggan management
    Route::resource('pelanggan', PelangganController::class);

    // Penggunaan management
    Route::resource('penggunaan', PenggunaanController::class);

    // Tagihan management
    Route::resource('tagihan', TagihanController::class);

    // Pembayaran management
    Route::resource('pembayaran', PembayaranController::class);
});

Route::middleware('ensure.auth:pelanggan')->group(function () {
    Route::get('/customer/dashboard', [DashboardController::class, 'pelangganDashboard'])->name('pelanggan.dashboard');
    Route::get('/customer/tagihan', [PembayaranController::class, 'pelangganTagihan'])->name('pelanggan.tagihan');
});
