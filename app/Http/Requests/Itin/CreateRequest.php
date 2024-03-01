<?php

namespace App\Http\Requests\Itin;

use App\Models\Itinerary;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique((new Itinerary)->getTable())
                    ->where(function ($query)
                    {
                        return $query->where('user_id', auth()->id())
                            ->whereNot('status', 'deleted');
                    }
                ),
            ],
            'post_ids' => 'required|array',
            'post_ids.*' => 'integer|exists:' . (new Post())->getTable() . ',id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'The Itinerary name must be unique for the user'
        ];
    }
}
