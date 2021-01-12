<?php

namespace App\Notifications\Lawyer;

use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class LawyerActivated extends BaseNotification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('emails.auth.lawyer_activated.subject'))
            ->markdown('emails.auth.lawyer_activated');
    }

    public function toFcm($notifiable)
    {
        $message = new \Benwilkins\FCM\FcmMessage();
        $message->content([
            'title' => __('notifications.types.lawyer_activated.title'),
            'body' => __('notifications.types.lawyer_activated.body'),
        ])->priority(\Benwilkins\FCM\FcmMessage::PRIORITY_HIGH);

        return $message;
    }
}
