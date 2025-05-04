<?php

namespace App\Http\Middleware;

use App\Employee;
use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class HideAdmin
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
        $user = Auth::guard('web')->user() ?? Auth::guard('api')->user();

        if ($user && ($user->login !== User::DEFAULT_USER_LOGIN)) {
            User::addGlobalScope('hideDefaultUser', function ($builder) use ($user) {
                $builder->where('login', '!=', User::DEFAULT_USER_LOGIN);
            });

            Employee::addGlobalScope('hideDefaultUser', function ($builder) use ($user) {
                $builder->whereHas('user', function ($query) {
                    $query->where('login', '!=', User::DEFAULT_USER_LOGIN);
                });
            });
        }

        return $next($request);
    }
}
