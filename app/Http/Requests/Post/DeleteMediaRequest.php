<?php

namespace App\Http\Requests\Post;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;

class DeleteMediaRequest extends FormRequest
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
            'media_ids' => 'required|array',
            'media_ids.*' => 'integer|exists:' . (new Media())->getTable() . ',id',
        ];
    }
}
