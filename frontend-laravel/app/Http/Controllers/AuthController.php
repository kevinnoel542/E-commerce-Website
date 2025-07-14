<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    private string $fastApiUrl;

    public function __construct()
    {
        $this->fastApiUrl = 'http://127.0.0.1:8000/api/v1';
    }

    /**
     * Handle login form submission and connect to FastAPI
     */
    public function login(Request $request): RedirectResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        try {
            // Send credentials to FastAPI
            $response = Http::post('http://127.0.0.1:8000/api/v1/auth/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Extract tokens and user data from the correct structure
                $tokens = $data['tokens'] ?? [];
                $user = $data['user'] ?? [];

                // Debug: Log the user data to see what role is being returned
                \Log::info('User login data:', ['user' => $user, 'role' => $user['role'] ?? 'user']);

                // Determine user role with admin override
                $userRole = $user['role'] ?? 'user';
                if ($user['email'] === 'starkalyboy@gmail.com') {
                    $userRole = 'admin';
                }

                // Store access_token and role as specified
                Session::put('access_token', $tokens['access_token'] ?? null);
                Session::put('auth_token', $tokens['access_token'] ?? null); // Keep for compatibility
                Session::put('jwt_token', $tokens['access_token'] ?? null); // Keep for compatibility
                Session::put('refresh_token', $tokens['refresh_token'] ?? null);
                Session::put('role', $userRole); // Store role as specified
                Session::put('user_role', $userRole); // Keep for compatibility
                Session::put('user_data', $user);
                Session::put('user_email', $user['email'] ?? $request->email);
                Session::put('user_id', $user['id'] ?? null);
                Session::put('user', $user);

                // Remember me functionality
                if ($request->has('remember')) {
                    Session::put('remember_token', true);
                }

                // Log the corrected role
                \Log::info('Corrected user role stored in session:', ['email' => $user['email'], 'role' => $userRole]);

                // Role-based redirect
                return $this->redirectBasedOnRole($userRole);
            } else {
                // Handle FastAPI error response
                $errorData = $response->json();
                $errorMessage = $errorData['detail'] ?? 'Login failed. Please check your credentials.';

                return redirect()->back()
                    ->withErrors(['email' => $errorMessage])
                    ->withInput($request->except('password'));
            }
        } catch (\Exception $e) {
            // Handle connection errors
            return redirect()->back()
                ->withErrors(['email' => 'Unable to connect to authentication service. Please try again later.'])
                ->withInput($request->except('password'));
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request): RedirectResponse
    {
        try {
            // Optional: Call FastAPI logout endpoint
            $token = Session::get('auth_token');
            if ($token) {
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])->post($this->fastApiUrl . '/auth/logout');
            }
        } catch (\Exception $e) {
            // Continue with logout even if FastAPI call fails
        }

        // Clear all session data
        Session::flush();

        return redirect()->route('login')
            ->with('status', 'You have been logged out successfully.');
    }

    /**
     * Role-based redirect after successful login
     */
    private function redirectBasedOnRole(string $role): RedirectResponse
    {
        switch ($role) {
            case 'admin':
            case 'super_admin':
            case 'manager':
            case 'moderator':
                return redirect()->route('admin.dashboard')
                    ->with('status', 'Welcome back, Admin!');

            case 'user':
            default:
                return redirect()->route('dashboard.user')
                    ->with('status', 'Welcome back!');
        }
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated(): bool
    {
        return Session::has('auth_token') && Session::has('user_data');
    }

    /**
     * Get current user data
     */
    public static function user(): ?array
    {
        return Session::get('user_data');
    }

    /**
     * Get current user role
     */
    public static function userRole(): string
    {
        return Session::get('user_role', 'user');
    }

    /**
     * Get auth token
     */
    public static function token(): ?string
    {
        return Session::get('auth_token');
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole(string $role): bool
    {
        return self::userRole() === $role;
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }
}
