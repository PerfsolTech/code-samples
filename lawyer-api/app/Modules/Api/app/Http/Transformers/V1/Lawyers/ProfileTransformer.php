<?php

namespace Api\Http\Transformers\V1\Lawyers;

use Api\Http\Transformers\V1\Lawyers\Profile\CasesTransformer;
use Api\Http\Transformers\V1\Lawyers\Profile\CompetenciesTransformer;
use Api\Http\Transformers\V1\Lawyers\Profile\LanguagesTransformer;
use Api\Http\Transformers\V1\Lawyers\Profile\ReviewsTransformer;
use App\Models\User;
use App\Repositories\CasesRepository;
use App\Services\Image;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class ProfileTransformer extends TransformerAbstract
{
    private $imageService;
    /**
     * @var ReviewService
     */
    private $reviewService;

    /**
     * Attachments constructor.
     * @param $imageService
     */
    public function __construct(Image $imageService, ReviewService $reviewService)
    {
        $this->imageService = $imageService;
        $this->reviewService = $reviewService;
    }

    public function transform(User $lawyer)
    {
        return [
            'id' => $lawyer->id,
            'rating' => (float)$lawyer->lawyer->rating,
            'address' => $lawyer->lawyer->address,

            'calls_allowed' => (bool)isset($lawyer->settings['calls_allowed']) ? $lawyer->settings['calls_allowed'] : true,
            'messages_allowed' => (bool)isset($lawyer->settings['messages_allowed']) ? $lawyer->settings['messages_allowed'] : true,
            'about' => $lawyer->lawyer->about,
            'website' => $lawyer->lawyer->website,
            'firm' => $lawyer->lawyer->firm,
            'linkedin' => $lawyer->lawyer->linkedin,

            'avatar' => $this->imageService->getUrl($lawyer->avatar),
            'phone' => $lawyer->phone,
            'email' => $lawyer->email,
            'name' => $lawyer->name,
            'latitude' => (float)$lawyer->latitude,
            'longitude' => (float)$lawyer->longitude,
            'is_favorite' => (bool)$lawyer->isFavorite,
            'is_review_added' => (bool)$this->reviewService->isReviewAlreadySubmitted(Auth::user(),$lawyer->id),
            'can_add_review' => (bool)$this->reviewService->isUserCanAddReview(Auth::user(), $lawyer->id),

            'assigned_case' => $this->includeAssignedCase($lawyer->assigned_case),
            'competencies' => $this->includeCompetencies($lawyer),
            'languages' => $this->includeLanguages($lawyer),
            'cases' => $this->includeCases($lawyer),
            'reviews' => $this->includeReviews($lawyer),

            'created_at' => $lawyer->created_at->__toString(),
        ];
    }

    public function includeCompetencies(User $lawyer)
    {
        if (!$lawyer->competencies) {
            return [];
        }
        return $lawyer->competencies->map(function ($lawyerCompetency) {
            return (new CompetenciesTransformer())->transform($lawyerCompetency);
        });
    }

    public function includeLanguages(User $lawyer)
    {
        if (!$lawyer->languages) {
            return [];
        }

        return $lawyer->languages->map(function ($lawyerLanguage) {
            return (new LanguagesTransformer())->transform($lawyerLanguage);
        });
    }

    public function includeCases(User $lawyer)
    {
        $count = (new CasesRepository())->countByType($lawyer->id);
        return (new CasesTransformer())->transform($count);
    }

    public function includeReviews(User $lawyer)
    {
        $reviews = [];
        if (count($lawyer->reviews)) {
            foreach ($lawyer->reviews as $review)
                $reviews[] = (new ReviewsTransformer())->transform($review);
        }
        return $reviews;
    }

    private function includeAssignedCase($assigned_case)
    {
        if (!$assigned_case) {
            return [];
        }

        return [[
            'status' => $assigned_case->status,
            'case_id' => $assigned_case->case_id,
            'number' => $assigned_case->number
        ]
        ];
    }
}