<?php

namespace Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserJoin extends FormRequest
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
            'user.email' => 'required|unique:users,email',
            'user.password' => config('auth.password_rule'),
        ];
        if ('lawyer' === $this->route('perspective')) {
            $rules = array_merge($rules, config('validation.lawyer-profile'));
        }
        return $rules;
    }


    protected function formatErrors(Validator $validator)
    {
        $errors = $validator->errors()->all();

        $keys = $validator->errors()->keys();
        if (in_array('The user.email has already been taken.', $errors)) {
            return [
                'error_code' => '001',
            ];
        }

//        if (in_array('user.password', $keys)) {
//            return [
//                'error_code' => '004',
//            ];
//        }
        return $validator->getMessageBag()->toArray();
    }
}
