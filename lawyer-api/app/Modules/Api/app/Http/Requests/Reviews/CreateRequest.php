<?php

namespace Api\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'review.title' => 'required|max:255',
            'review.body' => 'required|max:1000',
            'review.rating' => 'required|numeric|min:1|max:5',
        ];
    }

}