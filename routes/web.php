<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PenggunaanController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Middleware\CheckLevel;

// Public routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('user.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Pelanggan authentication routes
Route::get('/pelanggan/login', [AuthController::class, 'showPelangganLoginForm'])->name('pelanggan.login');
Route::get('/aktivasi', [AuthController::class, 'showActivateForm'])->name('pelanggan.activate');
Route::post('/aktivasi', [AuthController::class, 'activateAccount'])->name('pelanggan.activate.submit');
Route::post('/pelanggan/login', [AuthController::class, 'pelangganLogin'])->name('pelanggan.login.submit');
Route::post('/pelanggan/logout', [AuthController::class, 'pelangganLogout'])->name('pelanggan.logout');

// Admin routes (requires authentication)
Route::middleware('ensure.auth:web')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pelanggan management
    Route::resource('pelanggan', PelangganController::class);

    // Penggunaan management
    // Penggunaan management (Rute khusus wajib di atas Route::resource)
    Route::get('/penggunaan/massal', [PenggunaanController::class, 'massal'])->name('penggunaan.massal');
    Route::post('/penggunaan/massal', [PenggunaanController::class, 'storeMassal'])->name('penggunaan.massal.store');
    Route::get('/penggunaan/meter-terakhir', [PenggunaanController::class, 'getMeterTerakhir'])->name('penggunaan.meter-terakhir');
    Route::resource('penggunaan', PenggunaanController::class);

    // Tagihan management
    Route::resource('tagihan', TagihanController::class);

    // Pembayaran management
    Route::resource('pembayaran', PembayaranController::class);

    Route::resource('tarif', TarifController::class);
});

Route::middleware('ensure.auth:pelanggan')->group(function () {
    Route::get('/customer/dashboard', [DashboardController::class, 'pelangganDashboard'])->name('pelanggan.dashboard');
    Route::get('/customer/tagihan', [PembayaranController::class, 'pelangganTagihan'])->name('pelanggan.tagihan');
    Route::get('/customer/penggunaan', [PelangganController::class, 'riwayatPenggunaan'])->name('pelanggan.penggunaan');

});
