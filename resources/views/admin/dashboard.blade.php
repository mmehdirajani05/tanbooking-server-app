@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- Total Hotels -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Hotels</p>
                <p class="text-3xl font-bold text-gray-800">{{ $overview['hotels']['total'] }}</p>
                <p class="text-xs text-emerald-600 mt-1">
                    <i class="fas fa-check-circle mr-1"></i>{{ $overview['hotels']['approved'] }} approved
                </p>
            </div>
            <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-building text-blue-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Bookings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Bookings</p>
                <p class="text-3xl font-bold text-gray-800">{{ $overview['bookings']['total'] }}</p>
                <p class="text-xs text-amber-600 mt-1">
                    <i class="fas fa-clock mr-1"></i>{{ $overview['bookings']['pending'] }} pending
                </p>
            </div>
            <div class="w-14 h-14 bg-amber-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-calendar-check text-amber-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Revenue -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Revenue</p>
                <p class="text-3xl font-bold text-gray-800">${{ $overview['revenue']['total_confirmed'] }}</p>
                <p class="text-xs text-gray-400 mt-1">From confirmed bookings</p>
            </div>
            <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-dollar-sign text-emerald-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Active Chats -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Open Chats</p>
                <p class="text-3xl font-bold text-gray-800">{{ $overview['conversations']['open'] }}</p>
                <p class="text-xs text-blue-600 mt-1">
                    <i class="fas fa-comment mr-1"></i>{{ $overview['conversations']['active'] }} active
                </p>
            </div>
            <div class="w-14 h-14 bg-purple-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-comments text-purple-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <!-- Recent Bookings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Recent Bookings</h3>
            <a href="{{ route('admin.bookings.index') }}" class="text-sm text-primary-600 hover:text-primary-700">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium">Reference</th>
                        <th class="text-left px-4 py-3 font-medium">Customer</th>
                        <th class="text-left px-4 py-3 font-medium">Hotel</th>
                        <th class="text-left px-4 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($overview['recent_bookings'] as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $booking->booking_reference }}</td>
                        <td class="px-4 py-3">{{ $booking->customer->name }}</td>
                        <td class="px-4 py-3">{{ $booking->hotel->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($booking->status === 'confirmed') bg-emerald-100 text-emerald-700
                                @elseif($booking->status === 'pending') bg-amber-100 text-amber-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No bookings yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Hotels -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Top Hotels by Bookings</h3>
            <a href="{{ route('admin.hotels.index') }}" class="text-sm text-primary-600 hover:text-primary-700">View All →</a>
        </div>
        <div class="p-4 space-y-3">
            @forelse($overview['top_hotels'] as $hotel)
            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-primary-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $hotel->name }}</p>
                    <p class="text-xs text-gray-500">{{ $hotel->city }}</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-gray-800">{{ $hotel->bookings_count }}</p>
                    <p class="text-xs text-gray-500">bookings</p>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-gray-400">No hotels yet</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <p class="text-sm text-gray-500 mb-3">Users</p>
        <div class="flex items-center gap-6">
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $overview['users']['total_customers'] }}</p>
                <p class="text-xs text-gray-400">Customers</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $overview['users']['total_hotel_owners'] }}</p>
                <p class="text-xs text-gray-400">Hotel Owners</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <p class="text-sm text-gray-500 mb-3">Room Types</p>
        <div class="flex items-center gap-6">
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $overview['room_types']['total'] }}</p>
                <p class="text-xs text-gray-400">Total</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-emerald-600">{{ $overview['room_types']['active'] }}</p>
                <p class="text-xs text-gray-400">Active</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <p class="text-sm text-gray-500 mb-3">Hotels Status</p>
        <div class="flex items-center gap-4">
            <div>
                <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded text-xs font-medium">{{ $overview['hotels']['pending'] }}</span>
                <p class="text-xs text-gray-400 mt-1">Pending</p>
            </div>
            <div>
                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-medium">{{ $overview['hotels']['approved'] }}</span>
                <p class="text-xs text-gray-400 mt-1">Approved</p>
            </div>
            <div>
                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">{{ $overview['hotels']['rejected'] }}</span>
                <p class="text-xs text-gray-400 mt-1">Rejected</p>
            </div>
        </div>
    </div>
</div>
@endsection
