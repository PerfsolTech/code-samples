<?php

namespace Api\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;


class IsLawyer
{
    public function handle($request, Closure $next)
    {
        if (!Auth::user()->isLawyer()) {
            throw  new AuthenticationException();
        }

        return $next($request);
    }
}