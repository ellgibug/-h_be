<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestorePasswordRequest extends FormRequest
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
            'code' => 'required|string|exists:users',
            'password_reset_code' => 'required|string|exists:users',
            'password_reset_token' => 'required|string|exists:users',
            'password' => 'required|string|min:6|max:10',
            'password_confirmation' => 'required|string|min:6|max:10',
        ];
    }
}
