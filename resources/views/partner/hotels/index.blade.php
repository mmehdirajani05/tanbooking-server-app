@extends('partner.layouts.app')

@section('title', 'My Hotels')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">My Hotels</h1>
        <p class="mt-1 text-sm text-gray-500">Manage your hotel properties</p>
    </div>
    <a href="{{ route('partner.hotels.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition font-medium">
        <i class="fas fa-plus mr-2"></i>Add Hotel
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($hotels->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
        @foreach($hotels as $hotel)
        <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition">
            @if($hotel->images && count($hotel->images) > 0)
                <img src="{{ $hotel->images[0] }}" alt="{{ $hotel->name }}" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-hotel text-gray-300 text-5xl"></i>
                </div>
            @endif
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-semibold text-gray-900">{{ $hotel->name }}</h3>
                    <span class="px-2 py-1 rounded text-xs font-medium 
                        {{ $hotel->status === 'approved' ? 'bg-green-100 text-green-800' : 
                           ($hotel->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ ucfirst($hotel->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mb-3"><i class="fas fa-map-marker-alt mr-1"></i>{{ $hotel->city }}, {{ $hotel->area }}</p>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span><i class="fas fa-bed mr-1"></i>{{ $hotel->room_types_count }} rooms</span>
                    <span><i class="fas fa-calendar-check mr-1"></i>{{ $hotel->bookings_count }} bookings</span>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('partner.hotels.show', $hotel->id) }}" class="flex-1 text-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                    <a href="{{ route('partner.hotels.edit', $hotel->id) }}" class="flex-1 text-center px-3 py-2 bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100 transition text-sm font-medium">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $hotels->links() }}
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-hotel text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Hotels Yet</h3>
        <p class="text-gray-500 mb-4">Start by adding your first hotel property.</p>
        <a href="{{ route('partner.hotels.create') }}" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition font-medium">
            <i class="fas fa-plus mr-2"></i>Add Your First Hotel
        </a>
    </div>
    @endif
</div>
@endsection