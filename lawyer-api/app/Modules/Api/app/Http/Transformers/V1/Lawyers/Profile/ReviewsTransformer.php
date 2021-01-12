<?php

namespace Api\Http\Transformers\V1\Lawyers\Profile;

use League\Fractal\TransformerAbstract;

class ReviewsTransformer extends TransformerAbstract
{
    public function transform($review)
    {
        return [
            'id' => $review->id,
            'title' => $review->title,
            'body' => $review->body,
            'rating' => $review->rating,
            'created_at' => $review->created_at->__toString(),
        ];
    }

}