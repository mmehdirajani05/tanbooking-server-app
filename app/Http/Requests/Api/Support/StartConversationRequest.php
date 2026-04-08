<?php

namespace App\Http\Requests\Api\Support;

use Illuminate\Foundation\Http\FormRequest;

class StartConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotel_id' => 'nullable|exists:hotels,id',
            'subject'  => 'nullable|string|max:255',
            'message'  => 'required|string',
        ];
    }
}
