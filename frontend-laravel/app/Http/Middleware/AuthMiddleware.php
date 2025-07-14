<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\AuthController;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Ensures user is authenticated via FastAPI session
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!AuthController::isAuthenticated()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login')
                ->with('error', 'Please log in to access this page.');
        }

        return $next($request);
    }
}
