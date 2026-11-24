<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PenggunaanController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Middleware\CheckLevel;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\LaporanController;

// Public routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:auth-attempt')
    ->name('user.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Pelanggan authentication routes
Route::get('/pelanggan/login', [AuthController::class, 'showPelangganLoginForm'])->name('pelanggan.login');
Route::get('/aktivasi', [AuthController::class, 'showActivateForm'])->name('pelanggan.activate');
Route::post('/aktivasi', [AuthController::class, 'activateAccount'])
    ->middleware('throttle:5,1')
    ->name('pelanggan.activate.submit');
Route::post('/pelanggan/login', [AuthController::class, 'pelangganLogin'])
    ->middleware('throttle:5,1')
    ->name('pelanggan.login.submit');
Route::post('/pelanggan/logout', [AuthController::class, 'pelangganLogout'])->name('pelanggan.logout');

// Admin routes (requires authentication)
Route::middleware('ensure.auth:web')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Penggunaan kWh (Admin & Kasir)
    Route::get('/penggunaan/massal', [PenggunaanController::class, 'massal'])->name('penggunaan.massal');
    Route::post('/penggunaan/massal', [PenggunaanController::class, 'storeMassal'])->name('penggunaan.massal.store');
    Route::get('/penggunaan/meter-terakhir', [PenggunaanController::class, 'getMeterTerakhir'])->name(
        'penggunaan.meter-terakhir',
    );
    Route::resource('penggunaan', PenggunaanController::class);

    // Tagihan (Admin & Kasir)
    Route::resource('tagihan', TagihanController::class);

    // Pembayaran (Admin & Kasir - kecuali pembatalan/destroy)
    Route::resource('pembayaran', PembayaranController::class)->except(['destroy']);

    // Laporan Rekapitulasi (Admin & Kasir)
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.export-csv');

    // Khusus Administrator
    Route::middleware('admin.only')->group(function () {
        Route::resource('pelanggan', PelangganController::class);
        Route::resource('tarif', TarifController::class);
        Route::delete('/pembayaran/{pembayaran}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
    });
});

Route::middleware('ensure.auth:pelanggan')->group(function () {
    Route::get('/customer/dashboard', [DashboardController::class, 'pelangganDashboard'])->name('pelanggan.dashboard');
    Route::get('/customer/tagihan', [PembayaranController::class, 'pelangganTagihan'])->name('pelanggan.tagihan');
    Route::get('/customer/tagihan/{id}/struk', [PelangganController::class, 'struk'])->name('pelanggan.tagihan.struk');
    Route::get('/customer/penggunaan', [PelangganController::class, 'riwayatPenggunaan'])->name('pelanggan.penggunaan');
    Route::get('/customer/tarif', [TarifController::class, 'pelangganIndex'])->name('pelanggan.tarif');

    // Profil & Ganti Password Pelanggan
    Route::get('/customer/profil', [PelangganController::class, 'editProfil'])->name('pelanggan.profil.edit');
    Route::put('/customer/profil', [PelangganController::class, 'updateProfil'])->name('pelanggan.profil.update');
    Route::put('/customer/profil/password', [PelangganController::class, 'updatePassword'])->name(
        'pelanggan.profil.password',
    );
});
