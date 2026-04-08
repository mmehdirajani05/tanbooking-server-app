@extends('admin.layouts.app')

@section('title', 'Create Booking')
@section('page-title', 'Create New Booking')

@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.bookings.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" x-data="{
        hotelId: null,
        roomTypes: {{ json_encode($hotels->pluck('roomTypes', 'id')->map(fn($rts) => $rts->toArray())) }},
        selectedRoomTypes: [],
        pricePerNight: 0,
        checkIn: '',
        checkOut: '',
        rooms: 1,
        availability: null,
        checkingAvailability: false,
        get nights() {
            if (!this.checkIn || !this.checkOut) return 0;
            const d1 = new Date(this.checkIn), d2 = new Date(this.checkOut);
            return Math.max(0, Math.ceil((d2 - d1) / 86400000));
        },
        get totalPrice() {
            return (this.pricePerNight * this.nights * this.rooms).toFixed(2);
        },
        get canBook() {
            if (!this.availability) return false;
            return this.availability.available && this.availability.min_available >= this.rooms;
        },
        onHotelChange() {
            const h = this.roomTypes[this.hotelId] || [];
            this.selectedRoomTypes = h.filter(r => r.is_active);
            this.pricePerNight = 0;
            this.availability = null;
        },
        onRoomChange(e) {
            const room = this.selectedRoomTypes.find(r => r.id == e.target.value);
            this.pricePerNight = room ? parseFloat(room.price_per_night) : 0;
            this.checkAvailability();
        },
        async checkAvailability() {
            if (!this.hotelId || !this.$el.querySelector('[name=room_type_id]').value || !this.checkIn || !this.checkOut) {
                this.availability = null;
                return;
            }
            
            this.checkingAvailability = true;
            try {
                const response = await fetch(`/api/customer/hotels/search`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        check_in_date: this.checkIn,
                        check_out_date: this.checkOut,
                        number_of_guests: 1,
                        city: ''
                    })
                });
                const data = await response.json();
                
                // Find the selected hotel and room in results
                const hotel = data.data?.find(h => h.id == this.hotelId);
                const roomTypeId = this.$el.querySelector('[name=room_type_id]').value;
                const room = hotel?.room_types?.find(r => r.id == roomTypeId);
                
                if (room) {
                    this.availability = {
                        available: true,
                        min_available: room.available_rooms,
                        message: `${room.available_rooms} rooms available for selected dates`
                    };
                } else {
                    this.availability = {
                        available: false,
                        min_available: 0,
                        message: 'No rooms available for selected dates'
                    };
                }
            } catch (error) {
                console.error('Error checking availability:', error);
                this.availability = {
                    available: false,
                    min_available: 0,
                    message: 'Unable to check availability'
                };
            } finally {
                this.checkingAvailability = false;
            }
        }
    }" @change.debounce.500ms="checkAvailability()">
        @csrf
        <div class="p-6 space-y-6">
            <!-- Guest Selection -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-user text-primary-500 mr-2"></i>Guest Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Customer <span class="text-red-500">*</span></label>
                        <select name="customer_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guest Name <span class="text-red-500">*</span></label>
                        <input type="text" name="guest_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Full name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guest Email <span class="text-red-500">*</span></label>
                        <input type="email" name="guest_email" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none" placeholder="guest@email.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guest Phone <span class="text-red-500">*</span></label>
                        <input type="text" name="guest_phone" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none" placeholder="+1 234 567 890">
                    </div>
                </div>
            </div>

            <!-- Hotel & Room -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-hotel text-amber-500 mr-2"></i>Hotel & Room</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hotel <span class="text-red-500">*</span></label>
                        <select name="hotel_id" x-model="hotelId" @change="onHotelChange()" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none">
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $h)
                                <option value="{{ $h->id }}">{{ $h->name }} ({{ $h->city }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Room Type <span class="text-red-500">*</span></label>
                        <select name="room_type_id" @change="onRoomChange" :disabled="!hotelId" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none disabled:bg-gray-100">
                            <option value="">Select Room Type</option>
                            <template x-for="room in selectedRoomTypes" :key="room.id">
                                <option :value="room.id" x-text="room.name + ' ($' + room.price_per_night + '/night)'"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-calendar text-green-500 mr-2"></i>Booking Details</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check-in <span class="text-red-500">*</span></label>
                        <input type="date" name="check_in_date" x-model="checkIn" :min="'{{ now()->toDateString() }}'" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check-out <span class="text-red-500">*</span></label>
                        <input type="date" name="check_out_date" x-model="checkOut" :min="checkIn || '{{ now()->toDateString() }}'" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rooms <span class="text-red-500">*</span></label>
                        <input type="number" name="number_of_rooms" x-model="rooms" min="1" value="1" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guests <span class="text-red-500">*</span></label>
                        <input type="number" name="number_of_guests" min="1" value="1" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                </div>

                <!-- Availability Status -->
                <div class="mt-4" x-show="availability" x-cloak>
                    <div class="p-4 rounded-lg border" :class="availability.available ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'">
                        <div class="flex items-center gap-3">
                            <i class="fas text-xl" :class="availability.available ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600'"></i>
                            <div>
                                <p class="font-medium" :class="availability.available ? 'text-green-800' : 'text-red-800'" x-text="availability.message"></p>
                                <p class="text-sm mt-1" :class="availability.available ? 'text-green-600' : 'text-red-600'" x-show="availability.available">
                                    You can book up to <span x-text="availability.min_available"></span> room(s)
                                </p>
                            </div>
                            <div x-show="checkingAvailability" class="ml-auto">
                                <i class="fas fa-spinner fa-spin text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Notes -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-clipboard-list text-purple-500 mr-2"></i>Additional</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Special requests..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Price Preview -->
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
                <h4 class="font-semibold text-emerald-800 mb-3"><i class="fas fa-calculator mr-2"></i>Price Preview</h4>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div><span class="text-emerald-600">Nights:</span> <span x-text="nights || 0" class="font-semibold text-emerald-800">0</span></div>
                    <div><span class="text-emerald-600">Rate/night:</span> $<span x-text="pricePerNight.toFixed(2)" class="font-semibold text-emerald-800">0.00</span></div>
                    <div><span class="text-emerald-600">Total:</span> <span class="text-xl font-bold text-emerald-800">$<span x-text="totalPrice">0.00</span></span></div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
            <a href="{{ route('admin.bookings.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">Cancel</a>
            <button type="submit" :disabled="!canBook || checkingAvailability" class="px-5 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-save mr-2"></i>
                <span x-text="checkingAvailability ? 'Checking...' : (canBook ? 'Create Booking' : 'Select Dates First')"></span>
            </button>
        </div>
    </form>
</div>
@endsection
