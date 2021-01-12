<?php

namespace Api\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSettings extends FormRequest
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
        $user = Auth::user();
        if ($user->isClient()) {
            return [
                'settings.notifications' => 'required|boolean',
            ];
        }

        if ($user->isLawyer()) {
            return [
                'settings.notifications' => 'required|boolean',
                'settings.calls_allowed' => 'required|boolean',
                'settings.messages_allowed' => 'required|boolean',
            ];
        }
    }

}