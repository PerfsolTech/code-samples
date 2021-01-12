<?php

namespace Api\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiAuth
{

    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->header('Authorization');

            if (app()->isLocal()) {
                return $next($request);
            }

            if (!$token) {
                Log::info('!$token');
                throw  new AuthenticationException();
            }

            $this->authService->authorize($token);

            return $next($request);
        } catch (\Exception $e) {
            Log::info('ApiAuth Exception');
            throw  new $e;
        }
    }
}
