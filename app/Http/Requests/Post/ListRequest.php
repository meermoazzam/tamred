<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
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
            'user_id' => 'nullable|integer',
            'title' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:10000',
            'city' => 'nullable|string|max:200',
            'state' => 'nullable|string|max:200',
            'country' => 'nullable|string|max:200',
            'tags' => 'nullable|string|max:200',
            'categories' => 'nullable|array',
        ];
    }
}
