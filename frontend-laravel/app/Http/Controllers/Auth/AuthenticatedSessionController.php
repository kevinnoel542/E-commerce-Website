<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * Note: This only shows the login form view.
     * Actual authentication is handled by FastAPI backend.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    // Note: store() and destroy() methods removed
    // Authentication logic is handled by FastAPI backend
}
