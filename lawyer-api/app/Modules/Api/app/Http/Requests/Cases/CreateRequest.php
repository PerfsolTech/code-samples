<?php

namespace Api\Http\Requests\Cases;

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
            'case.title' => 'required|max:250',
            'case.message' => 'required|max:1000',
            'case.competency_id' => 'required|numeric|exists:competencies,id',
            'case.city_id' => 'required|numeric|exists:cities,id',
            'case.language_id' => 'required|numeric|exists:languages,id',
            'case.attachments'=>'array',
            'case.attachments.*.data'=>'required',
        ];
    }

}