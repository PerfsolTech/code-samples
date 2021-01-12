<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class RollbarLogger
{
    public static function logException(\Exception $exception, $additionalInfo = [])
    {
        $request = Request::capture();

        $data = [
            'payload' => $request->json(),
            'path' => $request->path(),
            'user' => 'Guest',
        ];
        if (Auth::user()) {
            $user = Auth::user();
            $data['user'] = [
                'id' => $user->id,
                'status' => $user->status,
                'email' => $user->email,
                'type' => $user->type
            ];
        }
        Rollbar::log(
            Level::ERROR,
            $exception,
            array_merge($data, $additionalInfo)
        );
    }

}