<?php

namespace Api\Http\Requests\Lawyers;

use Illuminate\Foundation\Http\FormRequest;

class Ordering extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order' => 'in:rate,cases,name',
            'desc' => 'in:ASC,DESC',
        ];
    }
}