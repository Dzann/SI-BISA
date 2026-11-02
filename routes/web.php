<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KamusAdminController;
use App\Http\Controllers\TranslateController;
use Illuminate\Support\Facades\Route;

// Halaman Publik & Aplikasi
Route::view('/', 'landing');
Route::view('/login', 'login')->name('login');
Route::view('/app', 'beranda');
Route::view('/riwayat', 'riwayat');
Route::get('/riwayat/{id}', fn ($id) => view('riwayat-detail', ['id' => $id]));
Route::view('/pengaturan', 'pengaturan');
Route::view('/akun', 'akun');

// Halaman Khusus Admin
Route::get('/admin', fn () => view('admin'))->middleware(['auth', 'admin']);

// API Internal (ditempatkan di web.php agar session dan proteksi CSRF aktif)
Route::prefix('api')->group(function () {
    // Alur Penerjemahan & Riwayat
    Route::post('translate', [TranslateController::class, 'translate'])->middleware('throttle:60,1');
    Route::get('riwayat', [TranslateController::class, 'riwayat']);
    Route::get('riwayat/{id}', [TranslateController::class, 'detail']);

    // Autentikasi Pengguna & Admin
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth');

    // Manajemen Kamus Isyarat BISINDO (Khusus Admin)
    Route::middleware(['auth', 'admin'])->apiResource('kamus', KamusAdminController::class)->except('show');
});
