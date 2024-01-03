<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;


class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            
            $role = Auth::User()->id_rol;
        
            if ($role != 11) {
    
                if ($role === 12) {
                    return redirect('Devoluciones');
                } else {
                    return redirect(RouteServiceProvider::HOME);
                }
                
            } else {
                return redirect('Schedule');
            }
        }

        return $next($request);
    }
}
