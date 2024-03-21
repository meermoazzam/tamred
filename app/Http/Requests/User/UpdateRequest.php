<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'bio' => 'sometimes|nullable|string|max:2000',
            'nickname' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255',
            'gender' => 'sometimes|required|in:male,female,other',
            'date_of_birth' => 'sometimes|required|date_format:Y-m-d|before:today',
            'location' => 'sometimes|required|max:255',
            'latitude' => 'sometimes|required',
            'longitude' => 'sometimes|required',
            'city' => 'sometimes|required|max:255',
            'state' => 'sometimes|max:255',
            'country' => 'sometimes|required|max:255',
            'language' => 'sometimes|string|max:100|in:italian,english',
        ];
    }
}
