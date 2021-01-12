<?php

namespace Api\Http\Controllers\V1\Lawyer;

use Api\Exceptions\ApiException;
use Api\Http\Controllers\ApiController;
use Api\Http\Requests\Cases\Ordering;
use Api\Http\Transformers\V1\Cases\GetCaseTransformer;
use Api\Http\Transformers\V1\Cases\ListTransformer;
use Api\Http\Transformers\V1\Cases\TitlesTransformer;
use App\Exceptions\CaseException;
use App\Models\CaseModel;
use App\Models\User;
use App\Repositories\CasesRepository;
use App\Repositories\LawyerRepository;
use App\Services\CaseService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CasesController extends ApiController
{
    private $caseService;

    public function __construct(CaseService $caseService)
    {
        $this->caseService = $caseService;
    }

    public function accept($case_id)
    {
        try {
            $this->caseService->lawyersAcceptsCaseRequest($this->getCase($case_id), $this->user());
        } catch (\Exception $e) {
            if ($e instanceof CaseException) {
                throw new ApiException(422, $e->getCode());
            }
            throw $e;
        }
        return $this->responseSuccess();
    }


    public function dismiss($case_id)
    {
        try {
            $this->caseService->lawyerDismissCaseRequest($this->getCase($case_id), $this->user());
        } catch (\Exception $e) {
            if ($e instanceof CaseException) {
                throw new ApiException(422, $e->getCode());
            }
            throw $e;
        }
        return $this->responseSuccess();
    }


    public function index(CasesRepository $casesRepository, Ordering $request)
    {
        $pagintedResult = $casesRepository->search(
            $this->user()->id,
            'lawyer',
            trim($request->get('q')),
            $request->get('competency_id'),
            $request->get('status'),
            $request->get('city_id'),
            $request->get('order', 'id'),
            $request->get('dir', 'DESC'),
            $request->get('per_page')
        );
        return $this->responsePagination($pagintedResult, new ListTransformer());
    }

    public function listTitles(CasesRepository $casesRepository, Request $request)
    {
        $result = $casesRepository->searchTitles($this->user()->id, 'lawyer', trim($request->get('q')));
        return $this->responseCollection($result, new TitlesTransformer());
    }


    public function get($case_id, CasesRepository $casesRepository, GetCaseTransformer $caseTransformer)
    {
        $case = $casesRepository->get($case_id);
        if (!$case) {
            throw new NotFoundHttpException();
        }
        return $this->responseItem($case, $caseTransformer);
    }


    private function getCase($case_id)
    {
        $case = CaseModel::where('id', $case_id)->first();
        if (!$case) {
            throw new CaseException("Invalid case id", 16);
        }
        return $case;
    }
}