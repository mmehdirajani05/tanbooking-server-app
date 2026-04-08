@extends('admin.layouts.app')

@section('title', $hotel->name)
@section('page-title', $hotel->name)

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Hotel Info -->
    <div class="xl:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-building text-primary-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $hotel->name }}</h3>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        @if($hotel->status === 'approved') bg-emerald-100 text-emerald-700
                        @elseif($hotel->status === 'pending') bg-amber-100 text-amber-700
                        @else bg-red-100 text-red-700 @endif">
                        {{ ucfirst($hotel->status) }}
                    </span>
                </div>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">City</span><span class="font-medium">{{ $hotel->city }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Area</span><span class="font-medium">{{ $hotel->area }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Phone</span><span class="font-medium">{{ $hotel->phone ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Email</span><span class="font-medium">{{ $hotel->email ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Check-in</span><span class="font-medium">{{ $hotel->check_in_time ? $hotel->check_in_time->format('H:i') : '14:00' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Check-out</span><span class="font-medium">{{ $hotel->check_out_time ? $hotel->check_out_time->format('H:i') : '12:00' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Owner</span><span class="font-medium">{{ $hotel->owner->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Bookings</span><span class="font-medium">{{ $hotel->bookings_count }}</span></div>
            </div>

            @if($hotel->description)
            <div class="pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-600">{{ $hotel->description }}</p>
            </div>
            @endif

            @if($hotel->amenities)
            <div class="pt-4 border-t border-gray-100">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Amenities</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($hotel->amenities as $amenity)
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                            <i class="fas fa-check mr-1"></i>{{ $amenity }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($hotel->images && count($hotel->images) > 0)
            <div class="pt-4 border-t border-gray-100">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Hotel Images ({{ count($hotel->images) }})</h4>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($hotel->images as $image)
                        <a href="{{ $image }}" target="_blank" class="relative group block">
                            <img src="{{ $image }}" alt="Hotel Image" class="w-full h-32 object-cover rounded-lg border border-gray-200">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded-lg flex items-center justify-center">
                                <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 text-xl"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex gap-2 pt-2">
                <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="flex-1 text-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium"><i class="fas fa-edit mr-1"></i>Edit</a>
                <a href="{{ route('admin.hotels.index') }}" class="flex-1 text-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium"><i class="fas fa-arrow-left mr-1"></i>Back</a>
            </div>
        </div>
    </div>

    <!-- Room Types -->
    <div class="xl:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Room Types ({{ $hotel->roomTypes->count() }})</h3>
                <button onclick="document.getElementById('addRoomModal').showModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition text-sm font-medium">
                    <i class="fas fa-plus mr-1"></i>Add Room Type
                </button>
            </div>

            <div class="divide-y divide-gray-50">
                @forelse($hotel->roomTypes as $rt)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bed text-amber-500"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $rt->name }}</p>
                                <p class="text-xs text-gray-400">{{ $rt->max_occupancy }} guests · {{ $rt->number_of_beds }} bed(s)</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">${{ number_format($rt->price_per_night, 2) }}</p>
                                <p class="text-xs text-gray-400">per night</p>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $rt->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $rt->is_active ? 'Active' : 'Inactive' }}</span>
                            <form method="POST" action="{{ route('admin.hotels.room-types.delete', [$hotel->id, $rt->id]) }}" onsubmit="return confirm('Delete this room type?')">
                                @method('DELETE') @csrf
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition"><i class="fas fa-trash text-sm"></i></button>
                            </form>
                        </div>
                    </div>
                    
                    @if($rt->amenities)
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($rt->amenities as $amenity)
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs">{{ $amenity }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    @if($rt->images && count($rt->images) > 0)
                    <div class="flex gap-2 mt-3">
                        @foreach(array_slice($rt->images, 0, 3) as $img)
                            <img src="{{ $img }}" class="w-20 h-16 object-cover rounded border">
                        @endforeach
                        @if(count($rt->images) > 3)
                            <div class="w-20 h-16 rounded border bg-gray-100 flex items-center justify-center text-xs text-gray-500">+{{ count($rt->images) - 3 }} more</div>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Inventory Management for this room type -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <h5 class="text-sm font-medium text-gray-700"><i class="fas fa-calendar-check mr-1"></i> Inventory Management</h5>
                            <button onclick="document.getElementById('inventoryModal{{ $rt->id }}').showModal()" class="text-xs text-primary-600 hover:text-primary-700">
                                <i class="fas fa-edit mr-1"></i>Manage Inventory
                            </button>
                        </div>
                        <p class="text-xs text-gray-500">Click "Manage Inventory" to set room availability for specific dates</p>
                    </div>
                    
                    <!-- Inventory Modal for this room -->
                    <dialog id="inventoryModal{{ $rt->id }}" class="rounded-xl p-0 shadow-2xl backdrop:bg-black/50">
                        <div class="bg-white p-6 w-[600px] max-h-[90vh] overflow-y-auto">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Manage Inventory - {{ $rt->name }}</h4>
                            <form method="POST" action="{{ route('admin.hotels.room-types.inventory', [$hotel->id, $rt->id]) }}" class="space-y-4">
                                @csrf
                                <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg">
                                    <p class="text-sm text-blue-800"><i class="fas fa-info-circle mr-1"></i> Set the total number of rooms and available rooms for each date</p>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="start_date" required min="{{ now()->toDateString() }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="end_date" required min="{{ now()->addDay()->toDateString() }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Rooms <span class="text-red-500">*</span></label>
                                        <input type="number" name="total_rooms" required min="1" value="10" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Available Rooms (initial)</label>
                                    <input type="number" name="available_rooms" min="0" value="10" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                                    <p class="text-xs text-gray-500 mt-1">Leave blank to set equal to total rooms</p>
                                </div>
                                <div class="flex gap-2 mt-4">
                                    <button type="button" onclick="document.getElementById('inventoryModal{{ $rt->id }}').close()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Cancel</button>
                                    <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">Set Inventory</button>
                                </div>
                            </form>
                        </div>
                    </dialog>
                </div>
                @empty
                <div class="px-6 py-12 text-center text-gray-400">
                    <i class="fas fa-bed text-3xl mb-2 block"></i>No room types yet. Add one to get started.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Add Room Type Modal -->
<dialog id="addRoomModal" class="rounded-xl p-0 shadow-2xl backdrop:bg-black/50">
    <div class="bg-white p-6 w-[600px] max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add Room Type</h3>
        <form method="POST" action="{{ route('admin.hotels.room-types.store', $hotel->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Room Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Deluxe Room">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Occupancy <span class="text-red-500">*</span></label>
                    <input type="number" name="max_occupancy" required min="1" value="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Beds <span class="text-red-500">*</span></label>
                    <input type="number" name="number_of_beds" required min="1" value="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price per Night <span class="text-red-500">*</span></label>
                <input type="number" name="price_per_night" required min="0" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="99.99">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Room description..."></textarea>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Room Images (URLs)</label>
                <textarea name="images" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="https://example.com/room1.jpg&#10;https://example.com/room2.jpg"></textarea>
                <p class="text-xs text-gray-500 mt-1">Enter one image URL per line</p>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Amenities</label>
                <div class="grid grid-cols-2 gap-2">
                    @php
                        $roomAmenities = ['TV', 'AC', 'Mini Bar', 'Safe', 'Balcony', 'WiFi', 'City View', 'Sea View'];
                    @endphp
                    @foreach($roomAmenities as $amenity)
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="{{ $amenity }}" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">{{ $amenity }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Active (available for booking)</label>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="button" onclick="document.getElementById('addRoomModal').close()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">Add Room Type</button>
            </div>
        </form>
    </div>
</dialog>
@endsection
