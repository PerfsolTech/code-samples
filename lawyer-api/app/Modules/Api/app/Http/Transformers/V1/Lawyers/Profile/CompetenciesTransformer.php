<?php

namespace Api\Http\Transformers\V1\Lawyers\Profile;

use League\Fractal\TransformerAbstract;

class CompetenciesTransformer extends TransformerAbstract
{

    public function transform($lawyerCompetency)
    {
        return [
            'id' => $lawyerCompetency->id,
            'name' => $lawyerCompetency->competency->name,
        ];
    }

}