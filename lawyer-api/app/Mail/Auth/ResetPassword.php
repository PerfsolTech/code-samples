<?php


namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class ResetPassword extends Mailable
{
    use Queueable;

    protected $token;
    /**
     * @var User
     */
    private $user;

    /**
     * ResetPassword constructor.
     * @param $token
     */
    public function __construct(User $user, $token)
    {
        $this->token = $token;
        $this->user = $user;
    }

    public function build()
    {
        $this->subject(__('emails.auth.reset_password.subject'));
        return $this->markdown('emails.auth.reset_password')
            ->with([
                'resetPasswordLink' => route('password.reset', [
                    'token' => $this->token,
                    'email' => $this->user->email
                ])
            ]);
    }
}