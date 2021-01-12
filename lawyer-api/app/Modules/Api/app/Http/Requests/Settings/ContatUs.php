<?php

namespace Api\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class ContatUs extends FormRequest
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
            'feedback.category' => 'required|string|max:250',
            'feedback.message' => 'required|string|max:1000',
        ];
    }

}