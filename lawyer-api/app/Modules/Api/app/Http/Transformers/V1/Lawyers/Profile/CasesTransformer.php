<?php

namespace Api\Http\Transformers\V1\Lawyers\Profile;

use League\Fractal\TransformerAbstract;

class CasesTransformer extends TransformerAbstract
{

    public function transform($cases)
    {
        return [
            'open' => isset($cases['OPEN']) ? $cases['OPEN'] : 0,
            'closed' => isset($cases['CLOSED']) ? $cases['CLOSED'] : 0,
        ];

    }

}