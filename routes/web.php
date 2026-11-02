<?php
use App\Http\Controllers\{AuthController, KamusAdminController, TranslateController};
use Illuminate\Support\Facades\Route;

// Halaman
Route::view('/', 'landing');
Route::view('/login', 'login')->name('login');
Route::view('/app', 'beranda');
Route::view('/riwayat', 'riwayat');
Route::get('/riwayat/{id}', fn ($id) => view('riwayat-detail', ['id' => $id]));
Route::view('/pengaturan', 'pengaturan');
Route::view('/akun', 'akun');
Route::get('/admin', function () {
    abort_unless(auth()->check() && auth()->user()->role === 'admin', 403, 'Khusus admin');
    return view('admin');
})->middleware('auth');

// API (di web.php agar session + CSRF aktif)
Route::prefix('api')->group(function () {
    Route::post('translate', [TranslateController::class, 'translate']);
    Route::get('riwayat', [TranslateController::class, 'riwayat']);
    Route::get('riwayat/{id}', [TranslateController::class, 'detail']);

    Route::post('login', [AuthController::class, 'login']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth');

    Route::middleware('auth')->apiResource('kamus', KamusAdminController::class)->except('show');
});
