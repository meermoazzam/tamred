<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if( true != auth()->user()->is_admin ) {
            if($request->expectsJson()) {
                return response()->json(['message' => "Not Allowed"], 403);
            } else {
                return back()->with(['error' => 'Not Allowed']);
            }
        }

        return $next($request);
    }
}
