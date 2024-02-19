<?php

namespace App\Http\Requests\User;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class AttachCategoryRequest extends FormRequest
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
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:' . (new Category())->getTable() . ',id',
        ];
    }
}
