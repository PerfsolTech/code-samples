<?php

namespace Api\Http\Transformers\V1\Lawyers\Profile;

use League\Fractal\TransformerAbstract;

class LanguagesTransformer extends TransformerAbstract
{

    public function transform($lawyerLanguage)
    {
        return [
            'id' => $lawyerLanguage->id,
            'name' => $lawyerLanguage->language->name,
            'level' => $lawyerLanguage->level,
        ];
    }
}