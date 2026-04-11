@extends('partner.layouts.app')

@section('title', 'Add Hotel')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Add New Hotel</h1>
        <p class="mt-1 text-sm text-gray-500">Fill in your hotel details and upload images</p>
    </div>

    <form method="POST" action="{{ route('partner.hotels.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ 
        images: [],
        handleImages(e) {
            this.images = Array.from(e.target.files);
        }
    }">
        @csrf
        <div class="p-6 space-y-6">
            <!-- Basic Info -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-primary-500"></i> Basic Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hotel Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none" placeholder="Grand Hotel">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none" placeholder="Describe your hotel..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-red-500"></i> Location
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
                        <input type="text" name="city" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none" placeholder="Dar es Salaam">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Area <span class="text-red-500">*</span></label>
                        <input type="text" name="area" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none" placeholder="City Center">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Address <span class="text-red-500">*</span></label>
                        <textarea name="address" required rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none" placeholder="123 Main Street"></textarea>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-phone text-green-500"></i> Contact Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none" placeholder="+255 123 456 789">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none" placeholder="info@hotel.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check-in Time</label>
                        <input type="time" name="check_in_time" value="14:00" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check-out Time</label>
                        <input type="time" name="check_out_time" value="12:00" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-concierge-bell text-blue-500"></i> Amenities
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                        $amenitiesList = ['WiFi', 'Pool', 'Gym', 'Spa', 'Restaurant', 'Parking', 'Room Service', 'Laundry', 'Business Center', 'Airport Shuttle', 'Pet Friendly', 'Bar'];
                    @endphp
                    @foreach($amenitiesList as $amenity)
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="{{ $amenity }}" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">{{ $amenity }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Pricing -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-dollar-sign text-green-500"></i> Pricing
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Retail Price (per night)</label>
                        <input type="number" name="retail_price" min="0" step="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none" placeholder="5000.00">
                        <p class="text-xs text-gray-500 mt-1">Price visible to all customers</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contract Price (per night)</label>
                        <input type="number" name="contract_price" min="0" step="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none" placeholder="4000.00">
                        <p class="text-xs text-gray-500 mt-1">Visible only to contractors</p>
                    </div>
                </div>
            </div>

            <!-- Images Upload -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-images text-purple-500"></i> Hotel Images
                </h3>
                <div class="space-y-4">
                    <label class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-3"></i>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500">PNG, JPG, JPEG (Max 2MB each)</p>
                        </div>
                        <input type="file" name="images[]" multiple accept="image/*" class="hidden" @change="handleImages">
                    </label>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <template x-for="(image, index) in images" :key="index">
                            <div class="relative group">
                                <img :src="URL.createObjectURL(image)" class="w-full h-40 object-cover rounded-lg border-2 border-gray-200">
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check text-white text-2xl"></i>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
            <a href="{{ route('partner.hotels.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">Cancel</a>
            <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium text-sm">
                <i class="fas fa-save mr-2"></i>Submit for Approval
            </button>
        </div>
    </form>
</div>
@endsection