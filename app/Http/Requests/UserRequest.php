<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'password' => [
                'required',
                'confirmed',
//                Password::min(8)     // minimum length
//                ->letters()               // at least one letter
//                ->mixedCase()             // at least one uppercase + one lowercase
//                ->numbers()               // at least one number
//                ->symbols()               // at least one symbol
//                ->uncompromised(),        // not in data breaches
            ],
            'roles' => 'required|array'
        ];
    }
}
