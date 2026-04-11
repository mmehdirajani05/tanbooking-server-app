@extends('partner.layouts.app')

@section('title', 'Dashboard')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
    <p class="mt-1 text-sm text-gray-500">Here's what's happening with your business today.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    @if(isset($stats['hotels']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Hotels</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['hotels']['total'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-hotel text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-green-600 font-medium">{{ $stats['hotels']['approved'] }} approved</span>
            <span class="text-gray-400 mx-2">·</span>
            <span class="text-amber-600 font-medium">{{ $stats['hotels']['pending'] }} pending</span>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['bookings']['total'] }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-green-600 font-medium">{{ $stats['bookings']['confirmed'] }} confirmed</span>
            <span class="text-gray-400 mx-2">·</span>
            <span class="text-amber-600 font-medium">{{ $stats['bookings']['pending'] }} pending</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Revenue</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">${{ number_format($stats['bookings']['revenue'] ?? 0, 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-gray-500">From confirmed bookings</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Active Modules</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ count($stats['modules']) }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-cube text-amber-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-1">
            @foreach($stats['modules'] as $module)
                <span class="px-2 py-1 bg-primary-50 text-primary-700 rounded text-xs font-medium capitalize">{{ $module }}</span>
            @endforeach
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Bookings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
            <a href="{{ route('partner.bookings.index') }}" class="text-sm text-primary-600 hover:text-primary-700">View all</a>
        </div>
        <div class="divide-y divide-gray-200">
            @php
                $recentBookings = \App\Models\Booking::where('company_id', $company->id)->latest()->limit(5)->get();
            @endphp
            @forelse($recentBookings as $booking)
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-semibold">
                        {{ substr($booking->guest_name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $booking->guest_name }}</p>
                        <p class="text-sm text-gray-500">{{ $booking->hotel?->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-medium text-gray-900">${{ number_format($booking->total_price, 2) }}</p>
                    <span class="px-2 py-1 rounded text-xs font-medium 
                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                           ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-calendar text-3xl mb-2 block"></i>
                No bookings yet
            </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-4">
            @if(in_array('hotel', $stats['modules']))
            <a href="{{ route('partner.hotels.create') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-plus-circle text-3xl text-primary-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Add Hotel</span>
            </a>
            @endif

            @if(in_array('tourism', $stats['modules']))
            <a href="{{ route('partner.tourism.packages.create') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-plus-circle text-3xl text-green-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Add Tour Package</span>
            </a>
            @endif

            @if(in_array('event', $stats['modules']))
            <a href="{{ route('partner.events.create') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-plus-circle text-3xl text-purple-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Create Event</span>
            </a>
            @endif

            <a href="{{ route('partner.company.show') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-building text-3xl text-amber-600 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">Company Profile</span>
            </a>
        </div>
    </div>
</div>
@endsection