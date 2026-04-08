@extends('admin.layouts.app')

@section('title', 'Edit Hotel')
@section('page-title', 'Edit Hotel — ' . $hotel->name)

@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.hotels.update', $hotel->id) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-info-circle text-primary-500 mr-2"></i>Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hotel Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $hotel->name) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">{{ old('description', $hotel->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Owner <span class="text-red-500">*</span></label>
                        <select name="owner_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                            @foreach($hotelOwners as $owner)
                                <option value="{{ $owner->id }}" {{ $hotel->owner_id == $owner->id ? 'selected' : '' }}>{{ $owner->name }} ({{ $owner->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                            <option value="approved" {{ $hotel->status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ $hotel->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ $hotel->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-map-marker-alt text-red-500 mr-2"></i>Location</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city', $hotel->city) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Area</label>
                        <input type="text" name="area" value="{{ old('area', $hotel->area) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="address" required rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">{{ old('address', $hotel->address) }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-phone text-green-500 mr-2"></i>Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $hotel->phone) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $hotel->email) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
                        <input type="time" name="check_in_time" value="{{ old('check_in_time', $hotel->check_in_time ? $hotel->check_in_time->format('H:i') : '14:00') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
                        <input type="time" name="check_out_time" value="{{ old('check_out_time', $hotel->check_out_time ? $hotel->check_out_time->format('H:i') : '12:00') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-100">
            <a href="{{ route('admin.hotels.delete', $hotel->id) }}" onclick="return confirm('Delete this hotel permanently?')" class="text-red-600 hover:text-red-800 text-sm font-medium"><i class="fas fa-trash mr-1"></i>Delete</a>
            <div class="flex gap-3">
                <a href="{{ route('admin.hotels.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium text-sm"><i class="fas fa-save mr-2"></i>Update Hotel</button>
            </div>
        </div>
    </form>
</div>
@endsection
