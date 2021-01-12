<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class LawyerRegistred extends Mailable
{

    use Queueable;

    private $user;

    /**
     * LawyerRegistred constructor.
     * @param $user
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
        $this->subject("New lawyer registred");
        return $this->markdown('emails.auth.lawyer_registred')->with([
            'user' => $this->user
        ]);
    }

}