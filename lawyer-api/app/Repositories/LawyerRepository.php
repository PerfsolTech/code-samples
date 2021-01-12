<?php

namespace App\Repositories;

use AnthonyMartin\GeoLocation\GeoLocation;
use App\Models\CaseModel;
use App\Models\City;
use App\Models\LawyerCase;
use App\Models\LawyerCompetency;
use App\Models\LawyerProfile;
use App\Models\User;
use App\Models\UserLawyer;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LawyerRepository
{
    const ORDER = [
        'id' => 'users.id',
        'name' => 'users.first_name',
        'rate' => 'lawyer_profiles.rating',
        'cases' => 'lawyer_profiles.cases',
    ];

    const SEARCH_RADIUS = 50;
    /**
     * @var LawyerProfile
     */
    private $model;

    public function __construct(LawyerProfile $model)
    {
        $this->model = $model;
    }

    public function search(
        $user_id,
        $latitude = null,
        $longitude = null,
        $q = '',
        $language_id = null,
        $competency_id = null,
        $city_id = null,
        $favorites_user_id = null,
        $order = 'rate',
        $dir = 'DESC',
        $per_page = 15
    )
    {
        /** @var Builder $query */
        $query = User::lawyerProfile()
            ->active()
            ->join('lawyer_profiles', 'lawyer_profiles.user_id', '=', 'users.id')
            ->where('users.id', '!=', $user_id);

        if (strlen($q) > 0) {
            $query->whereRaw("CONCAT(first_name,' ',last_name) like ?", "%$q%");
        }

        if ($latitude && $longitude) {
            $this->matchCoordinates($query, $latitude, $longitude);
        } elseif ($city_id) {
            $city = City::find($city_id);
            $this->matchCoordinates($query, $city->latitude, $city->longitude);
        }

        if ($language_id) {
            $query->join('lawyer_languages', 'users.id', '=', 'lawyer_languages.lawyer_id')
                ->where('lawyer_languages.language_id', '=', $language_id);
        }

        if ($competency_id) {
            $query->join('lawyer_competencies', 'users.id', '=', 'lawyer_competencies.lawyer_id')
                ->where('lawyer_competencies.competency_id', '=', $competency_id);
        }

        if ($favorites_user_id) {
            $query->whereIn('users.id', $this->getFavoritesLawyerIds($favorites_user_id));
        }

        $query->orderBy(self::ORDER[$order] ?? 'lawyer_profiles.rating', $dir);

        $result = $query->paginate($per_page);

        $lawyers = $result->getCollection();
        $lawyer_ids = $lawyers->map(function ($e) {
            return $e->user_id;
        });

        $competencies = $this->loadCompetencies($lawyer_ids);

        foreach ($lawyers as $key => $lawyer) {
            $lawyers[$key]->competencies = isset($competencies[$lawyer->user_id]) ? $competencies[$lawyer->user_id] : new Collection();
        }

        $result->setCollection($lawyers);
        return $result;
    }


    public function searchNames($user_id, $q = '')
    {
        if (strlen($q) > 0) {
            return User::lawyerProfile()
                ->active()
                ->select(['id', 'first_name', 'last_name'])
                ->whereRaw("CONCAT(first_name,' ',last_name) like ?", "%$q%")
                ->where('id', '!=', $user_id)
                ->orderBy('first_name')
                ->limit(200)
                ->get();
        }
        return [];
    }

    public function getSuggestedLawyers($competency_id)
    {
        $items = User::select(DB::Raw("CONCAT_WS(' ', `users`.`first_name`,`users`.`last_name`,`lawyer_profiles`.`cases`) as lawyer_name"), 'users.id')
            ->lawyerProfile()
            ->active()
            ->join('lawyer_profiles', 'lawyer_profiles.user_id', '=', 'users.id')
            ->join('lawyer_competencies', 'users.id', '=', 'lawyer_competencies.lawyer_id')
            ->where('lawyer_competencies.competency_id', '=', $competency_id)
            ->orderBy('lawyer_profiles.rating', 'DESC')
            ->get()
            ->pluck('lawyer_name', 'id');

        if (!$items) {
            return [];
        }
        return $items;
    }

    private function matchCoordinates($query, $latitude, $longitude)
    {
        $coordinates = GeoLocation::fromDegrees($latitude, $longitude)
            ->boundingCoordinates(self::SEARCH_RADIUS, 'km');
        $query->whereBetween('users.latitude', [$coordinates[0]->getLatitudeInDegrees(), $coordinates[1]->getLatitudeInDegrees()])
            ->whereBetween('users.longitude', [$coordinates[0]->getLongitudeInDegrees(), $coordinates[1]->getLongitudeInDegrees()]);
        return $query;
    }


    private function loadCompetencies($ids)
    {
        $competencies = LawyerCompetency::leftJoin('competencies', 'competencies.id', '=', 'lawyer_competencies.competency_id')
            ->whereIn('lawyer_competencies.lawyer_id', $ids)
            ->get()
            ->groupBy('lawyer_id');

        return $competencies;
    }


    private function loadCaseInfo($lawyer_id, $user_id)
    {
        $statuses = LawyerCase::select('lawyer_cases.lawyer_id', 'lawyer_cases.case_id', 'lawyer_cases.status', 'cases.number')
            ->join('cases', 'cases.id', '=', 'lawyer_cases.case_id')
            ->where('lawyer_cases.lawyer_id', $lawyer_id)
            ->where('lawyer_cases.user_id', $user_id)
            ->where('cases.status', '!=', CaseModel::STATUS_CLOSED)
            ->first();

        return $statuses;
    }


    public function getProfile($lawyer_id, $user_id)
    {
        $lawyer = User::lawyerProfile()
            ->active()
            ->where('id', $lawyer_id)
            ->with([
                'lawyer',
                'languages.language',
                'competencies.competency',
                'lawyerCases',
                'reviews' => function ($query) {
                    $query->approved()
                        ->orderBy('id', 'DESC')
                        ->limit(3);
                }
            ])
            ->first();

        if(!$lawyer){
            return null;
        }

        $lawyer->isFavorite = $lawyer->isFavorite($user_id);
        $lawyer->assigned_case = $this->loadCaseInfo($lawyer->id, $user_id);

        return $lawyer;
    }

    public function getLawyer($lawyer_id)
    {
        return User::lawyerProfile()
            ->active()
            ->where('id', $lawyer_id)
            ->first();
    }

    private function getFavoritesLawyerIds($favorites_user_id)
    {
        $ids = UserLawyer::where('user_id', $favorites_user_id)
            ->select('lawyer_id')
            ->pluck('lawyer_id');
        return $ids;
    }
}