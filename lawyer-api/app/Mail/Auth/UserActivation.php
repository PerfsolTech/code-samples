<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserActivation extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject(__('emails.auth.activation.subject'));
        return $this->markdown('emails.auth.activation')
            ->with([
                'activationLink' => route('user.activate', [
                    'perspective' => strtolower($this->user->type),
                    'token' => $this->user->activation->token
                ])
            ]);
    }
}
