<?php

namespace App\Http\Requests\Album;

use App\Models\Album;
use App\Models\User;
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
                Rule::unique((new Album)->getTable())
                    ->where(function ($query) use ($id)
                    {
                        return $query
                            ->where('user_id', auth()->id())
                            ->whereNot('id', $id)
                            ->whereNot('status', 'deleted');
                    }
                ),
            ],
            'is_collaborative' => 'required|boolean',
            'user_ids' => 'sometimes|array',
            'user_ids.*' => 'integer|exists:' . (new User())->getTable() . ',id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'The album name must be unique for the user'
        ];
    }
}
