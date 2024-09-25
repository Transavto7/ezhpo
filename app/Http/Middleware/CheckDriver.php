<?php

namespace App\Http\Middleware;

use Closure;

class CheckDriver
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
        $user = $request->user();

        if($user->hasRole('driver') && ($user->roles()->count() === 1)) {
            return redirect(route('driver.index'));
        }

        return $next($request);
    }
}
