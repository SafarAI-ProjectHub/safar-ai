<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                if ($user->hasRole('Admin')) {
                    return redirect(RouteServiceProvider::ADMIN_HOME);
                } elseif ($user->hasRole('Teacher')) {
                    return redirect(RouteServiceProvider::TEACHER_HOME);
                } elseif ($user->hasRole('Student')) {
                    return redirect(RouteServiceProvider::STUDENT_HOME);
                } elseif ($user->hasRole('Super Admin')) {
                    return redirect(RouteServiceProvider::SUPER_ADMIN_HOME);
                }

                // Default redirect if no role is matched
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}