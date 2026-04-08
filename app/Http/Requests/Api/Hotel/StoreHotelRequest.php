<?php

namespace App\Http\Requests\Api\Hotel;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRequest extends FormRequest
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
            'city'            => 'required|string|max:255',
            'area'            => 'required|string|max:255',
            'address'         => 'required|string',
            'phone'           => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:255',
            'amenities'       => 'nullable|array',
            'images'          => 'nullable|array',
            'images.*'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'check_in_time'   => 'nullable|date_format:H:i',
            'check_out_time'  => 'nullable|date_format:H:i',
        ];
    }
}
