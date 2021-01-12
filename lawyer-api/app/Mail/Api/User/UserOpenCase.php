<?php

namespace App\Mail\Api\User;

use App\Models\CaseModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserOpenCase extends Mailable
{
    use Queueable, SerializesModels;

    protected $case;
    protected $action;

    public function __construct(CaseModel $case, string $action)
    {
        $this->case = $case;
    }

    public function build()
    {
        return $this->view('emails.api.user.open_case')
            ->with([
                'case' => $this->case,
                'action' => $this->action,
            ]);
    }
}