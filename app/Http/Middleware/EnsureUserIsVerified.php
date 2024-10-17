<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if ($request->user() && !$request->user()->is_verified) {
            // Return a custom message when the user is not verified
            return response()->json(['message' => 'Your email address is not verified.'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
