<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private string $fastApiUrl;

    public function __construct()
    {
        $this->fastApiUrl = 'http://127.0.0.1:8000/api/v1';
    }

    /**
     * Show profile view
     */
    public function show(): View
    {
        $user = AuthController::user();
        $token = AuthController::token();

        // Initialize profile data
        $profile = $user; // Default to session user data

        try {
            // Call GET /auth/profile API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($this->fastApiUrl . '/auth/profile');

            if ($response->successful()) {
                $apiProfile = $response->json();
                if ($apiProfile) {
                    $profile = $apiProfile;
                }
            }
        } catch (\Exception $e) {
            // Log error but continue with session data
            \Log::error('Profile fetch error: ' . $e->getMessage());
        }

        return view('profile.show', [
            'user' => $user,
            'profile' => $profile
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(): View
    {
        $user = AuthController::user();
        $token = AuthController::token();

        // Initialize profile data
        $profile = $user; // Default to session user data

        try {
            // Call GET /auth/profile API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($this->fastApiUrl . '/auth/profile');

            if ($response->successful()) {
                $apiProfile = $response->json();
                if ($apiProfile) {
                    $profile = $apiProfile;
                }
            }
        } catch (\Exception $e) {
            // Log error but continue with session data
            \Log::error('Profile fetch error: ' . $e->getMessage());
        }

        return view('profile.edit', [
            'user' => (object) $user, // Convert array to object for compatibility
            'profile' => $profile
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $token = AuthController::token();

        try {
            // Call PUT /auth/profile API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->put($this->fastApiUrl . '/auth/profile', [
                'full_name' => $request->full_name ?? $request->name,
                'phone' => $request->phone,
                'email' => $request->email, // Include email in case it needs to be updated
            ]);

            if ($response->successful()) {
                $updatedProfile = $response->json();

                // Update session data with new profile info
                $currentUser = AuthController::user();
                $currentUser['full_name'] = $updatedProfile['full_name'] ?? ($request->full_name ?? $request->name);
                $currentUser['phone'] = $updatedProfile['phone'] ?? $request->phone;
                $currentUser['email'] = $updatedProfile['email'] ?? $request->email;

                session(['user_data' => $currentUser]);
                session(['user' => $currentUser]);

                return Redirect::route('profile.edit')->with('status', 'Profile updated successfully!');
            } else {
                // Handle JWT expiration
                if ($response->status() === 401) {
                    // Clear expired session data
                    Session::forget(['auth_token', 'user_data', 'user_role']);

                    return redirect()->route('login')
                        ->with('error', 'Your session has expired. Please log in again.');
                }

                $error = $response->json();
                $errorMessage = $error['detail'] ?? $error['message'] ?? 'Failed to update profile';

                return Redirect::route('profile.edit')
                    ->withErrors(['error' => $errorMessage])
                    ->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());

            // Check if it's a token expiration issue
            if (strpos($e->getMessage(), 'JWT expired') !== false || strpos($e->getMessage(), '401') !== false) {
                Session::forget(['auth_token', 'user_data', 'user_role']);

                return redirect()->route('login')
                    ->with('error', 'Your session has expired. Please log in again.');
            }

            return Redirect::route('profile.edit')
                ->withErrors(['error' => 'Network error: Unable to update profile'])
                ->withInput();
        }
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = AuthController::user();
        $token = AuthController::token();

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->put($this->fastApiUrl . '/auth/change-password', [
                'current_password' => $request->current_password,
                'new_password' => $request->password,
            ]);

            if ($response->successful()) {
                return redirect()->route('profile.edit')
                    ->with('status', 'Password updated successfully.');
            } else {
                // Handle JWT expiration
                if ($response->status() === 401) {
                    // Clear expired session data
                    Session::forget(['auth_token', 'user_data', 'user_role']);

                    return redirect()->route('login')
                        ->with('error', 'Your session has expired. Please log in again.');
                }

                $errorData = $response->json();
                $errorMessage = $errorData['detail'] ?? 'Failed to update password.';

                return redirect()->route('profile.edit')
                    ->withErrors(['current_password' => $errorMessage]);
            }
        } catch (\Exception $e) {
            return redirect()->route('profile.edit')
                ->withErrors(['current_password' => 'Unable to update password. Please try again.']);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // For now, just redirect back with a message
        // TODO: Implement FastAPI account deletion integration
        return Redirect::route('profile.edit')->with('status', 'Account deletion not yet implemented');
    }
}
