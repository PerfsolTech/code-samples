<?php

namespace App\Notifications\Lawyer;

use App\Models\Review;
use App\Notifications\BaseNotification;

class ReviewPublished extends BaseNotification
{
    private $review;

    /**
     * ReviewPublished constructor.
     * @param $review
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
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
            'title' => __('notifications.types.review_published.title'),
            'body' => __('notifications.types.review_published.body',[
                'rating'=> $this->review->rating
            ]),
        ])->priority(\Benwilkins\FCM\FcmMessage::PRIORITY_HIGH);

        return $message;
    }

}