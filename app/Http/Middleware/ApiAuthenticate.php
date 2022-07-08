<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiAuthenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        $isActive = false;
        if (auth()->user() !== null) {
            if (auth()->user()->is_active != 0) {
                $isActive = 1;
            }
        }
        if (! $request->expectsJson() || !$isActive) {
            throw new HttpResponseException(response()->json([
                'success'   => false,
                'message'   => 'auth.error',
            ], 401));
        }
    }
}
