<?php

namespace App\Http\Requests\Itin;

use App\Models\Itinerary;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $id = $this->id;
        return [
            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique((new Itinerary)->getTable())
                    ->where(function ($query) use ($id)
                    {
                        return $query
                            ->where('user_id', auth()->id())
                            ->whereNot('id', $id)
                            ->whereNot('status', 'deleted');
                    }
                ),
            ],
            'post_ids' => 'required|array',
            'post_ids.*' => 'integer|exists:' . (new Post())->getTable() . ',id',
            'is_collaborative' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'The Itinerary name must be unique for the user'
        ];
    }
}
