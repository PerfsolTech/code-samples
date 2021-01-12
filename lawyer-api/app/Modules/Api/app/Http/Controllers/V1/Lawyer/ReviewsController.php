<?php

namespace Api\Http\Controllers\V1\Lawyer;

use Api\Http\Controllers\ApiController;
use Api\Http\Transformers\V1\Lawyers\Profile\ReviewsTransformer;
use App\Repositories\LawyerRepository;
use App\Repositories\ReviewRepository;
use Illuminate\Http\Request;

class ReviewsController extends ApiController
{
    private $reviewRepository;
    private $lawyerRepository;

    public function __construct(ReviewRepository $reviewRepository, LawyerRepository $lawyerRepository)
    {
        $this->reviewRepository = $reviewRepository;
        $this->lawyerRepository = $lawyerRepository;
    }

    public function index($lawyer_id, Request $request)
    {
        $per_page = $request->get('per_page', 15);
        $paginatedResult = $this->reviewRepository->paginatedList($lawyer_id, $per_page);
        return $this->responsePagination($paginatedResult, new ReviewsTransformer());
    }
}