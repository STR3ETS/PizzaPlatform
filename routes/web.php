<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// De onboarding is tegelijk de registratie
Route::get('/onboarding', function () {
    return view('onboarding');
})->name('onboarding');
Route::post('/onboarding/afronden', [OnboardingController::class, 'finish'])->name('onboarding.finish');
Route::redirect('/registreren', '/onboarding');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/wachtwoord-vergeten', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/wachtwoord-vergeten', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/wachtwoord-herstellen/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/wachtwoord-herstellen', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::post('/dashboard/intro-gezien', function (\Illuminate\Http\Request $request) {
        $request->user()->forceFill(['intro_seen' => true])->save();

        return response()->json(['ok' => true]);
    })->name('dashboard.intro');

    Route::post('/dashboard/status', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'online' => ['required', 'boolean'],
            'mode' => ['required', 'in:bezorgen_afhalen,alleen_afhalen'],
        ]);
        $request->user()->forceFill(['is_online' => $data['online'], 'order_mode' => $data['mode']])->save();

        return response()->json(['ok' => true]);
    })->name('dashboard.status');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
