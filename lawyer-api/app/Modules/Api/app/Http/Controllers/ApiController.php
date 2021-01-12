<?php

namespace Api\Http\Controllers;

use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class ApiController extends BaseController
{
    use ResponseService;

    protected function user(): User
    {
        return Auth::user();
    }

    protected function getUserType(Request $request)
    {
        return Auth::check() ? $this->user()->type : User::PERSPECTIVE_TYPES[explode('/', $request->getPathInfo())[3]];
    }
}