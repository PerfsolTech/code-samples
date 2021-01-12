<?php


namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class ResetPasswordSuccessful extends Mailable
{
    use Queueable;

    public function build()
    {
        $this->subject(__('emails.auth.reset_password_successful.subject'));
        return $this->markdown('emails.auth.reset_password_successful');
    }


}