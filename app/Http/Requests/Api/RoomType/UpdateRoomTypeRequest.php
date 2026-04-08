<?php

namespace App\Http\Requests\Api\RoomType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'sometimes|string|max:255',
            'description'     => 'nullable|string',
            'max_occupancy'   => 'sometimes|integer|min:1',
            'price_per_night' => 'sometimes|numeric|min:0',
            'number_of_beds'  => 'sometimes|integer|min:1',
            'amenities'       => 'nullable|array',
            'images'          => 'nullable|array',
            'is_active'       => 'nullable|boolean',
        ];
    }
}
