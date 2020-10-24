<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\User;


class RegistrationFormRequest extends FormRequest
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

        // organization_id - добавить проверку required_if, exists


        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:10',
            'password_confirmation' => 'required|string|min:6|max:10',

            'role' => ['required',  Rule::in([User::REQUEST_USER_TYPE_WITH_ORGANIZATION, User::REQUEST_USER_TYPE_WITHOUT_ORGANIZATION])],
            'organization_id' => [Rule::requiredIf('role' === User::REQUEST_USER_TYPE_WITH_ORGANIZATION)]
        ];
    }
}
