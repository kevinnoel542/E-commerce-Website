<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * Note: This only shows the registration form view.
     * Actual registration is handled by FastAPI backend.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    // Note: store() method removed
    // Registration logic is handled by FastAPI backend
}
