<?php

namespace App\Http\Requests\Post;

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
            'title' => 'sometimes|nullable|string|max:1000',
            'description' => 'sometimes|nullable|string|max:10000',
            'location' => 'sometimes|required|string|max:255',
            'latitude' => 'sometimes|required|string|max:20',
            'longitude' => 'sometimes|required|string|max:20',
            'city' => 'sometimes|nullable|string|max:200',
            'state' => 'sometimes|nullable|string|max:200',
            'country' => 'sometimes|nullable|string|max:200',
            'tags' => 'sometimes|nullable|array',
            'tagged_users' => 'sometimes|nullable|array',
            'allow_comments' => 'sometimes|nullable|boolean',
        ];
    }
}
