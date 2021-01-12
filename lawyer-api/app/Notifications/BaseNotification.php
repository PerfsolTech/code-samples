<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BaseNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['mail'];

        if ($notifiable->sendFCMNotification()) {
            $via[] = 'fcm';
        }

        return $via;
    }
}