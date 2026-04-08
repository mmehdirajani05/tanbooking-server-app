@extends('admin.layouts.app')

@section('title', 'Bookings')
@section('page-title', 'Booking Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-4 flex-wrap">
            <a href="?status=all" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status', 'all') === 'all' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">All</a>
            <a href="?status=pending" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'pending' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Pending</a>
            <a href="?status=confirmed" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'confirmed' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Confirmed</a>
            <a href="?status=cancelled" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'cancelled' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Cancelled</a>
        </div>
    </div>
    <a href="{{ route('admin.bookings.create') }}" class="bg-primary-600 text-white px-5 py-2.5 rounded-xl hover:bg-primary-700 transition font-medium text-sm">
        <i class="fas fa-plus mr-2"></i>Create Booking
    </a>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="text-left px-6 py-4 font-medium">Reference</th>
                    <th class="text-left px-4 py-4 font-medium">Guest</th>
                    <th class="text-left px-4 py-4 font-medium">Hotel</th>
                    <th class="text-left px-4 py-4 font-medium">Dates</th>
                    <th class="text-left px-4 py-4 font-medium">Rooms</th>
                    <th class="text-left px-4 py-4 font-medium">Total</th>
                    <th class="text-left px-4 py-4 font-medium">Status</th>
                    <th class="text-left px-4 py-4 font-medium">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-xs font-medium text-primary-600">{{ $booking->booking_reference }}</td>
                    <td class="px-4 py-4">
                        <p class="text-gray-800 font-medium">{{ $booking->guest_name }}</p>
                        <p class="text-xs text-gray-400">{{ $booking->guest_email }}</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-gray-800">{{ $booking->hotel->name }}</p>
                        <p class="text-xs text-gray-400">{{ $booking->hotel->city }}</p>
                    </td>
                    <td class="px-4 py-4 text-xs">
                        <p class="text-gray-600">{{ $booking->check_in_date->format('M d') }}</p>
                        <p class="text-gray-400">→ {{ $booking->check_out_date->format('M d, Y') }}</p>
                    </td>
                    <td class="px-4 py-4 text-gray-600">{{ $booking->number_of_rooms }}</td>
                    <td class="px-4 py-4 font-semibold text-gray-800">${{ number_format($booking->total_price, 2) }}</td>
                    <td class="px-4 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
                            @if($booking->status === 'confirmed') bg-emerald-100 text-emerald-700
                            @elseif($booking->status === 'pending') bg-amber-100 text-amber-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-gray-500 text-xs">{{ $booking->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-gray-400">
                    <i class="fas fa-calendar-check text-4xl mb-3 block"></i>
                    No bookings found
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection
