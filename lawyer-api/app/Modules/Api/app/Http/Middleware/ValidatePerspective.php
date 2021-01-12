<?php

namespace Api\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ValidatePerspective
{
    public function handle($request, Closure $next)
    {
        if (!in_array($request->route('perspective'), ['user', 'lawyer'])) {
            throw new NotFoundHttpException();
        }

        return $next($request);
    }
}