<?php

namespace App\Http\Requests\Api\RoomType;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'max_occupancy'   => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'number_of_beds'  => 'required|integer|min:1',
            'amenities'       => 'nullable|array',
            'images'          => 'nullable|array',
            'is_active'       => 'nullable|boolean',
        ];
    }
}
