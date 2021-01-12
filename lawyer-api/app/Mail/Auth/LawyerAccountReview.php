<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class LawyerAccountReview extends Mailable
{

    use Queueable;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject(__('emails.auth.lawyer_review.subject'));
        return $this->markdown('emails.auth.lawyer_review');
    }

}