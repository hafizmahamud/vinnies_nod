<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ForceAccepted2FA
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
        if (!Auth::check()) {
            return $next($request);
        }

        if ($request->isMethod('post')) {
            return $next($request);
        }

        if(Route::currentRouteName() !== 'projects.create' && $request->user()->has_accepted_terms){
            return redirect()->route('projects.create');
        }


        

        // if(Route::currentRouteName() !== '2fa.index' && $request->user()->has_accepted_terms && !$request->user()->google2fa_enabled_at){
        //     return redirect()->route('2fa.index'); 
        // }

        return $next($request);
    }
}
