<?php

namespace App\Http\Requests\Chat;

use App\Models\Chat\Conversation;
use Illuminate\Foundation\Http\FormRequest;

class GetMessageRequest extends FormRequest
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
            "conversation_id" => 'required|integer|exists:' . (new Conversation())->getTable() . ',id',
        ];
    }
}
