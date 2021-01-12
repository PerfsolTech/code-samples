<?php

namespace Api\Http\Transformers\V1\Lawyers;

use League\Fractal\TransformerAbstract;

class NamesTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name
        ];
    }
}