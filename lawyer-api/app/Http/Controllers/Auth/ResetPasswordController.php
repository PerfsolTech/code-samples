<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Auth\ResetPasswordSuccessful;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Services\DeepLink;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Auth::check()) {
            Auth::logout();
        }
    }


    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => config('auth.password_rule_reset'),
        ];
    }


    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [
            'regex' => 'Please include upper case and lower letteres and numbers to make your password stronger'
        ];
    }


    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());
        if (!$this->validatePasswordHistory($this->credentials($request))) {
            return $this->sendResetFailedResponse($request, trans('passwords.the_same_password'));
        }
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($response == Password::PASSWORD_RESET) {
            Mail::to($request->input('email'))
                ->send(new ResetPasswordSuccessful());

            return redirect()->route('home', ['client'])
                ->with('flashMessage', [
                    'title' => 'Great!',
                    'message' => trans($response),
                    'image' => '/images/reset_password_checked_white.png'
                ])
                ->with('deep-link', (new DeepLink())->getByUserEmail($request->input('email')) . '://user/forgot-password');
        } else {
            return $this->sendResetFailedResponse($request, $response);
        }
    }

    public function showResetForm(Request $request, $token = null)
    {
        $passwordReset = PasswordReset::where('email', $request->email)->first();
        if (!$passwordReset) {
            return redirect()->route('home', ['client']);
        }

        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    private function validatePasswordHistory($credentials)
    {
        $hashes = User::where('email', '=', $credentials['email'])
            ->first()
            ->userPasswords()
            ->limit(3)
            ->orderBy('id', 'DESC')
            ->pluck('password');

        foreach ($hashes as $hash) {
            if (Hash::check($credentials['password'], $hash)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param  string $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();

        $this->guard()->login($user);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
