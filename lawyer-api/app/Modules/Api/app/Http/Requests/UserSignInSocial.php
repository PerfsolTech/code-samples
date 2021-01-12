<?php

namespace Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSignInSocial extends FormRequest
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
        $rules = [
            'access_token' => 'required',
        ];
        if ('lawyer' === $this->route('perspective')) {
            $rules = array_merge($rules, config('validation.lawyer-profile'));
        }
        return $rules;

    }
}
