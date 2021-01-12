<?php

namespace App\Mail\Api\Settings;

use App\Models\Feedback;
use Illuminate\Mail\Mailable;

class ContactUs extends Mailable
{
    protected $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function build()
    {
        return $this->view('emails.api.user.settings.contact_us')
            ->with([
                'feedback' => $this->feedback,
            ]);
    }

}