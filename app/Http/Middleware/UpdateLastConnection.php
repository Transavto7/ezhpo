<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Carbon;

class UpdateLastConnection
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
        $user = $request->user('api');

        if ($user) {
            $user->last_connection_at = Carbon::now();
            $user->save();
        }

        return $next($request);
    }
}
