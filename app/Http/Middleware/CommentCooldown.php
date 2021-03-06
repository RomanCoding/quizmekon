<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class CommentCooldown
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->user('web')->comments()->inFiveMinutes()->count()) {
            return response()->json([
                'message' => 'Wait ' . (300 - Carbon::now()
                        ->diffInSeconds(Carbon::parse(
                            $request->user('web')->comments()->first()->created_at)
                        )) . ' seconds to comment!'
            ], 400);
        }

        return $next($request);
    }
}
