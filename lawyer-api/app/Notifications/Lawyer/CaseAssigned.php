<?php

namespace App\Notifications\Lawyer;


use App\Models\CaseModel;
use App\Notifications\BaseNotification;

class CaseAssigned extends BaseNotification
{
    private $case;

    /**
     * CaseAccepted constructor.
     * @param $case
     */
    public function __construct(CaseModel $case)
    {
        $this->case = $case;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = [];

        if ($notifiable->sendFCMNotification()) {
            $via[] = 'fcm';
        }

        return $via;
    }


    public function toFcm($notifiable)
    {
        $message = new \Benwilkins\FCM\FcmMessage();
        $message->content([
            'title' => __('notifications.types.case_assigned.title', [
                'id' => $this->case->number
            ]),
            'body' => __('notifications.types.case_assigned.body', [
                'title' => $this->case->title
            ]),
        ])->data([
            'action' => 'OPEN_CASE',
            'case_id' => $this->case->id
        ])->priority(\Benwilkins\FCM\FcmMessage::PRIORITY_HIGH);

        return $message;
    }

}