<?php

namespace App\Services;

use App\Models\LawyerCase;
use App\Models\LawyerCaseHistory;
use App\Models\Review;
use App\Models\User;
use App\Notifications\Client\ReviewApproved;
use App\Notifications\Lawyer\ReviewPublished;
use App\Repositories\ChatRepository;
use App\Repositories\ReviewRepository;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    protected $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function approve(Review $review): bool
    {
        if (!$review) {
            return false;
        }

        if ($review->isApproved()) {
            return false;
        }

        DB::transaction(function () use ($review) {
            $review->update(['status' => Review::STATUS_APPROVED]);
            $review->lawyer->lawyer->update(['rating' => $this->reviewRepository->getApprovedReviews($review->lawyer_id)]);
        });

        $review->lawyer->notify(new ReviewPublished($review));
        $review->reviewer->notify(new ReviewApproved($review));

        return true;
    }


    public function decline(Review $review): bool
    {
        if (!$review) {
            return false;
        }

        if ($review->isDeclined()) {
            return false;
        }

        $review->update(['status' => Review::STATUS_DECLINED]);
        return true;
    }

    public function isUserCanAddReview(User $reviewer, $lawyer_id): bool
    {
        $lawyerWasAcceptedToTheCase = LawyerCaseHistory::join('cases', 'cases.id', '=', 'lawyer_case_histories.case_id')
            ->where('lawyer_case_histories.lawyer_id', $lawyer_id)
            ->where('lawyer_case_histories.status', LawyerCase::STATUS_ACCEPTED)
            ->where('cases.user_id', $reviewer->id)
            ->get(['lawyer_case_histories.id'])->first();

        $chatIsStarted = app(ChatRepository::class)->isChatStarted($lawyer_id, $reviewer->id);

        return $lawyerWasAcceptedToTheCase || $chatIsStarted;
    }

    public function isReviewAlreadySubmitted(User $reviewer, $lawyer_id): bool
    {
        $review = Review::where('lawyer_id', $lawyer_id)
            ->where('reviewer_id', $reviewer->id)
            ->where(function ($query) {
                $query->where('status', '=', Review::STATUS_MODERATION)
                    ->orWhere('status', '=', Review::STATUS_APPROVED);
            })
            ->get(['id'])
            ->first();
        return (bool)($review);
    }
}