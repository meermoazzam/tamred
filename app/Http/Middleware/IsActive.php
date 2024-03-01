<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if( 'active' != auth()->user()->status ) {
            if($request->expectsJson()) {
                return response()->json(['message' => "Not Allowed, your account is " . auth()->user()->status], 403);
            } else {
                return back()->with(['error' => "Not Allowed, your account is " . auth()->user()->status]);
            }
        }

        return $next($request);
    }
}
