<?php

namespace App\Services\Admin;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getOverview(): array
    {
        $totalHotels = Hotel::count();
        $pendingHotels = Hotel::where('status', 'pending')->count();
        $approvedHotels = Hotel::where('status', 'approved')->count();
        $rejectedHotels = Hotel::where('status', 'rejected')->count();

        $totalRoomTypes = RoomType::count();
        $activeRoomTypes = RoomType::where('is_active', true)->count();

        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();

        $totalRevenue = Booking::where('status', 'confirmed')
            ->sum('total_price');

        $totalCustomers = User::where('global_role', 'customer')->count();
        $totalHotelOwners = User::where('global_role', 'partner')->count();

        $totalConversations = Conversation::count();
        $openConversations = Conversation::where('status', 'open')->count();
        $activeConversations = Conversation::where('status', 'active')->count();
        $closedConversations = Conversation::where('status', 'closed')->count();

        // Recent bookings
        $recentBookings = Booking::with(['customer:id,name,email', 'hotel:id,name,city'])
            ->latest()
            ->limit(10)
            ->get();

        // Top hotels by booking count
        $topHotels = Hotel::withCount(['bookings' => function ($q) {
            $q->where('status', 'confirmed');
        }])
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get(['id', 'name', 'city']);

        // Bookings by status breakdown (last 30 days)
        $bookingsLast30Days = Booking::select('status', DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('status')
            ->get();

        return [
            'hotels' => [
                'total'    => $totalHotels,
                'pending'  => $pendingHotels,
                'approved' => $approvedHotels,
                'rejected' => $rejectedHotels,
            ],
            'room_types' => [
                'total'  => $totalRoomTypes,
                'active' => $activeRoomTypes,
            ],
            'bookings' => [
                'total'     => $totalBookings,
                'pending'   => $pendingBookings,
                'confirmed' => $confirmedBookings,
                'cancelled' => $cancelledBookings,
            ],
            'revenue' => [
                'total_confirmed' => number_format((float) $totalRevenue, 2, '.', ''),
            ],
            'users' => [
                'total_customers' => $totalCustomers,
                'total_hotel_owners' => $totalHotelOwners,
            ],
            'conversations' => [
                'total'  => $totalConversations,
                'open'   => $openConversations,
                'active' => $activeConversations,
                'closed' => $closedConversations,
            ],
            'recent_bookings'     => $recentBookings,
            'top_hotels'          => $topHotels,
            'bookings_last_30d'   => $bookingsLast30Days,
        ];
    }
}
