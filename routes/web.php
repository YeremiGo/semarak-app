<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rute menampilkan formulir
Route::get('/laporan/create/{aset}', [LaporanController::class, 'create'])->name('laporan.create');

// Rute menampilkan halaman kedua
Route::post('/laporan/next', [LaporanController::class, 'next'])->name('laporan.next');

// Rute menyimpan data
Route::post('/laporan/store', [LaporanController::class, 'store'])->name('laporan.store');

// Rute menampilkan laporan
Route::get('/laporan/{laporan}', [LaporanController::class, 'show'])->name('laporan.show');

// Rute menampilkan login
Route::get('/login', [AuthController::class, 'show'])->name('login')->middleware('guest');

// Rute memproses data login
Route::post('/login', [AuthController::class, 'store']);

// Rute untuk logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute dashboard setelah login
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');