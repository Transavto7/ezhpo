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

        if($user->hasRole('driver', '==')) {
            return redirect(route('page.driver'));
        }

        return $next($request);
    }
}
