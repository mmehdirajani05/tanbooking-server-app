<?php

namespace App\Http\Requests\Api\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get authenticated user's role
        $user = auth()->user();
        $isHotelOwner = $user && $user->global_role === 'partner';
        $isAdmin = $user && $user->global_role === 'admin';
        
        $rules = [
            'guest_name'      => 'required|string|max:255',
            'guest_email'     => 'nullable|email|max:255', // Made nullable for walk-in guests
            'guest_phone'     => 'required|string|max:30',
            'hotel_id'        => 'required|exists:hotels,id',
            'room_type_id'    => 'required|exists:room_types,id',
            'check_in_date'   => 'required|date|after_or_equal:today',
            'check_out_date'  => 'required|date|after:check_in_date',
            'number_of_rooms' => 'required|integer|min:1',
            'number_of_guests'=> 'required|integer|min:1',
            'notes'           => 'nullable|string',
        ];

        // customer_id is optional for hotel owners/admins (they can create walk-in customers)
        // But required validation for regular customers (auto-filled from auth)
        if ($isHotelOwner || $isAdmin) {
            $rules['customer_id'] = 'nullable|exists:users,id';
        }

        return $rules;
    }
}
