<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class PostCooldown
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
        if ($request->user('web')->posts()->inSevenMinutes()->count()) {
            return response()->json([
                'message' => 'Wait ' . (420 - Carbon::now()
                            ->diffInSeconds(Carbon::parse(
                                $request->user('web')->posts()->first()->created_at)
                            )) . ' seconds to post!'
            ], 400);
        }

        return $next($request);
    }
}
