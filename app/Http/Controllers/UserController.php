<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => ['required'],
            'country_id' => ['required'],
            'date_of_birth' => ['required', 'date'],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'country_id' => $validated['country_id'],
            'date_of_birth' => $validated['date_of_birth'],
        ]);

        event(new Registered($user));

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

    public function deleteUser(Request $request)
    {
        $user = $request->user();
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully'], 200);
    }

    public function getUserCountry(Request $request)
    {
        $countryName = DB::table('users')
            ->where('users.id', $request->query('userId'))
            ->join('countries', 'users.country_id', '=', 'countries.id')
            ->select('countries.country_name as name')
            ->first();
        return $countryName;
    }

}