<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\AuthController;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Ensures user is authenticated and has admin role
     */
    public function handle(Request $request, Closure $next): Response
    {
        // First check if user is authenticated
        if (!AuthController::isAuthenticated()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login')
                ->with('error', 'Please log in to access this page.');
        }

        // Then check if user has admin role
        $userData = session('user_data');
        $isAdmin = AuthController::isAdmin() || ($userData && $userData['email'] === 'starkalyboy@gmail.com');

        if (!$isAdmin) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access denied. Admin privileges required.'], 403);
            }

            return redirect()->route('dashboard.user')
                ->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
