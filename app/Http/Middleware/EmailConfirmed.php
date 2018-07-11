<?php

namespace App\Http\Middleware;

use Closure;

class EmailConfirmed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user('web')->confirmed) {
            return response()->json([
                'message' => 'Make sure you confirmed your email address!'
            ], 401);
        }

        return $next($request);
    }
}
