<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip CSRF check for GET, HEAD, OPTIONS requests
        if ($request->isMethod('get') || $request->isMethod('head') || $request->isMethod('options')) {
            return $next($request);
        }

        // Skip CSRF check for unauthenticated requests to /register and /login (they don't have tokens yet)
        if (!$request->user() && in_array($request->path(), ['register', 'login'])) {
            return $next($request);
        }

        // Verify CSRF token for authenticated requests
        if ($request->user()) {
            $csrfToken = $request->header('X-CSRF-TOKEN');
            
            if (!$csrfToken || $csrfToken !== $request->user()->csrf_token) {
                return response()->json(['error' => 'CSRF token mismatch'], 419);
            }
        }

        return $next($request);
    }
}
