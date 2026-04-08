<?php

namespace App\Http\Requests\Api\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'total_rooms'     => 'required|integer|min:0',
        ];
    }
}
