<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        // Generate both API and CSRF tokens
        $tokens = $request->user()->generateTokens();

        return response()->json([
            'user' => $request->user(),
            'api_token' => $tokens['api_token'],
            'csrf_token' => $tokens['csrf_token'],
        ], 200);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        // Clear the API and CSRF tokens
        if ($request->user()) {
            $request->user()->update([
                'api_token' => null,
                'csrf_token' => null,
            ]);
        }

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
