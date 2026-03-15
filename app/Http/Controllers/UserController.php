<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => ['required'],
            'date_of_birth' => ['nullable', 'date'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Generate authentication tokens
        $tokens = $user->generateTokens();

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'api_token' => $tokens['api_token'],
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt authentication
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        // Generate fresh tokens
        $tokens = $user->generateTokens();

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'api_token' => $tokens['api_token'],
            'csrf_token' => $tokens['csrf_token'],
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'No token provided'], 400);
        }

        // Find user by token and clear it
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $user->update([
            'api_token' => null,
            'csrf_token' => null,
        ]);

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser(Request $request)
    {
        return response()->json($request->user());
    }
}
