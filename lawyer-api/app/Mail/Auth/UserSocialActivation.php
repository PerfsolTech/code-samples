<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserSocialActivation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
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
        $this->subject(__('emails.auth.social_activation.subject'));
        return $this->markdown('emails.auth.social_activation')
            ->with([
                'perspective' => strtolower($this->user->type),
                'activationLink' => route('user.activateSocial', ['token' => $this->user->social->activation_token])
            ]);
    }
}
