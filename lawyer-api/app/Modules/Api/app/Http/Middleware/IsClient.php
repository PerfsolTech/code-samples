<?php

namespace Api\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class IsClient
{

    public function handle($request, Closure $next)
    {
        if (!Auth::user()->isClient()) {
            throw  new AuthenticationException();
        }

        return $next($request);
    }
}