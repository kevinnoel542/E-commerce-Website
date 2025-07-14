<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Views Only)
|--------------------------------------------------------------------------
|
| These routes only provide the login and register views for reuse.
| Actual authentication logic is handled by FastAPI backend.
| Laravel auth logic has been removed as per project requirements.
|
*/

Route::middleware('guest')->group(function () {
    // Keep register view for reuse (form will submit to FastAPI)
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    // Keep login view for reuse (form will submit to FastAPI)
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
});

// Note: All POST routes and auth middleware routes removed
// Authentication is handled by FastAPI backend
