<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiLanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        cache()->get('lang' . auth()->id()) ? app()->setLocale(cache()->get('lang' . auth()->id())) : app()->setLocale('ru');
        return $next($request);
    }
}
