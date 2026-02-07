<?php

use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


Auth::routes();
Route::get('/', function () {
    return view('auth.login');
})->name('home');

Route::controller(PasswordResetController::class)->group(function () {
    Route::get('send-otp', 'sendOtp')->name('send-otp');
    Route::post('verify-otp', 'checkOtp')->name('verify-otp');
    Route::get('verify-token/{token}', 'validateToken')->name('verify-token');
    Route::post('update-password', 'updatePassword')->name('update-password');

});


Route::get('/clear', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return response()->json([
        'status' => true,
        'message' => '✅ All caches cleared successfully!'
    ]);
});