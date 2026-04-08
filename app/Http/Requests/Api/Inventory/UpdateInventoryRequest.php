<?php

namespace App\Http\Requests\Api\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'            => 'required|date',
            'total_rooms'     => 'required|integer|min:0',
            'available_rooms' => 'nullable|integer|min:0',
        ];
    }
}
