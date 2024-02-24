<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'title' => 'required|string|max:1000',
            'description' => 'nullable|string|max:10000',
            'location' => 'required|string|max:255',
            'latitude' => 'required|string|max:20',
            'longitude' => 'required|string|max:20',
            'city' => 'nullable|string|max:200',
            'state' => 'nullable|string|max:200',
            'country' => 'nullable|string|max:200',
            'tags' => 'nullable|array',
            'media' => 'nullable|array',
            'allow_comments' => 'boolean',
        ];
    }
}
