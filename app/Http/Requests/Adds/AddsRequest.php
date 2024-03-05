<?php

namespace App\Http\Requests\Adds;

use Illuminate\Foundation\Http\FormRequest;

class AddsRequest extends FormRequest
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
     *            'country' => 'sometimes|required|max:255',
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:500',
            'author' => 'required|string|max:255',
            'link' => 'required|string|max:1000',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'gender' => 'required|in:male,female,other',
            'min_age' => 'required',
            'max_age' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'range' => 'required',
            'status' => 'required',
        ];
    }
}
