<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Inventory;
use App\Models\RoomType;
use App\Models\User;
use App\Services\Admin\DashboardService;
use App\Services\Booking\BookingService;
use App\Services\Hotel\HotelService;
use App\Services\Support\SupportChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private HotelService $hotelService,
        private BookingService $bookingService,
        private SupportChatService $chatService
    ) {}

    public function dashboard()
    {
        $overview = $this->dashboardService->getOverview();
        $pendingCount = Hotel::where('status', 'pending')->count();

        return view('admin.dashboard', compact('overview', 'pendingCount'));
    }

    // ========== HOTELS ==========
    public function hotels(Request $request)
    {
        $pendingCount = Hotel::where('status', 'pending')->count();
        $filters = [];
        if ($request->status && $request->status !== 'all') {
            $filters['status'] = $request->status;
        }
        $hotels = $this->hotelService->getAllHotels($filters);
        $hotelOwners = User::where('global_role', 'partner')->get();

        return view('admin.hotels.index', compact('hotels', 'pendingCount', 'hotelOwners'));
    }

    public function createHotel()
    {
        $pendingCount = Hotel::where('status', 'pending')->count();
        $hotelOwners = User::where('global_role', 'partner')->get();
        return view('admin.hotels.create', compact('pendingCount', 'hotelOwners'));
    }

    public function storeHotel(Request $request)
    {
        $data = $request->validate([
            'owner_id'       => 'required|exists:users,id',
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'city'           => 'required|string|max:255',
            'area'           => 'required|string|max:255',
            'address'        => 'required|string',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:255',
            'check_in_time'  => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'status'         => 'required|in:pending,approved,rejected',
            'amenities'      => 'nullable|array',
            'images'         => 'nullable|array',
            'images.*'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('hotels', 'public');
                $imagePaths[] = Storage::url($path);
            }
        }

        $hotel = Hotel::create([
            'owner_id'       => $data['owner_id'],
            'name'           => $data['name'],
            'description'    => $data['description'] ?? null,
            'city'           => $data['city'],
            'area'           => $data['area'],
            'address'        => $data['address'],
            'phone'          => $data['phone'] ?? null,
            'email'          => $data['email'] ?? null,
            'amenities'      => $data['amenities'] ?? null,
            'images'         => !empty($imagePaths) ? $imagePaths : null,
            'check_in_time'  => $data['check_in_time'] ?? '14:00:00',
            'check_out_time' => $data['check_out_time'] ?? '12:00:00',
            'status'         => $data['status'],
            'approved_at'    => $data['status'] === 'approved' ? now() : null,
            'approved_by'    => $data['status'] === 'approved' ? 1 : null,
        ]);

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel created successfully.');
    }

    public function approveHotel(int $id)
    {
        $this->hotelService->approveHotel($id);
        return back()->with('success', 'Hotel approved successfully.');
    }

    public function rejectHotel(Request $request, int $id)
    {
        $this->hotelService->rejectHotel($id, $request->reason);
        return back()->with('success', 'Hotel rejected.');
    }

    public function editHotel(int $id)
    {
        $pendingCount = Hotel::where('status', 'pending')->count();
        $hotel = Hotel::with('owner')->findOrFail($id);
        $hotelOwners = User::where('global_role', 'partner')->get();
        return view('admin.hotels.edit', compact('hotel', 'pendingCount', 'hotelOwners'));
    }

    public function updateHotel(Request $request, int $id)
    {
        $data = $request->validate([
            'owner_id'       => 'required|exists:users,id',
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'city'           => 'required|string|max:255',
            'area'           => 'required|string|max:255',
            'address'        => 'required|string',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:255',
            'check_in_time'  => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'status'         => 'required|in:pending,approved,rejected',
        ]);

        $hotel = Hotel::findOrFail($id);
        $hotel->update(array_merge($data, [
            'approved_at' => $data['status'] === 'approved' ? ($hotel->approved_at ?? now()) : null,
            'approved_by' => $data['status'] === 'approved' ? ($hotel->approved_by ?? 1) : null,
        ]));

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel updated successfully.');
    }

    public function deleteHotel(int $id)
    {
        Hotel::findOrFail($id)->delete();
        return back()->with('success', 'Hotel deleted successfully.');
    }

    // ========== HOTEL DETAIL (Room Types) ==========
    public function hotelDetail(int $id)
    {
        $pendingCount = Hotel::where('status', 'pending')->count();
        $hotel = Hotel::with(['roomTypes', 'owner'])->withCount('bookings')->findOrFail($id);

        return view('admin.hotels.detail', compact('hotel', 'pendingCount'));
    }

    public function addRoomType(Request $request, int $hotelId)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'max_occupancy'   => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'number_of_beds'  => 'required|integer|min:1',
            'is_active'       => 'nullable|boolean',
            'images'          => 'nullable|string',
            'amenities'       => 'nullable|array',
        ]);

        Hotel::findOrFail($hotelId);

        // Process images from textarea (one URL per line)
        $images = null;
        if (!empty($data['images'])) {
            $images = array_filter(array_map('trim', explode("\n", $data['images'])));
            $images = array_filter($images, function($url) {
                return filter_var($url, FILTER_VALIDATE_URL);
            });
            $images = !empty($images) ? array_values($images) : null;
        }

        RoomType::create([
            'hotel_id'        => $hotelId,
            'name'            => $data['name'],
            'description'     => $data['description'] ?? null,
            'max_occupancy'   => $data['max_occupancy'],
            'price_per_night' => $data['price_per_night'],
            'number_of_beds'  => $data['number_of_beds'],
            'amenities'       => $data['amenities'] ?? null,
            'images'          => $images,
            'is_active'       => $data['is_active'] ?? true,
        ]);

        return back()->with('success', 'Room type added.');
    }

    public function deleteRoomType(int $hotelId, int $roomTypeId)
    {
        RoomType::where('hotel_id', $hotelId)->where('id', $roomTypeId)->firstOrFail()->delete();
        return back()->with('success', 'Room type deleted.');
    }

    public function setRoomInventory(Request $request, int $hotelId, int $roomTypeId)
    {
        $data = $request->validate([
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after:start_date',
            'total_rooms'     => 'required|integer|min:1',
            'available_rooms' => 'nullable|integer|min:0',
        ]);

        $hotel = Hotel::findOrFail($hotelId);
        $roomType = RoomType::where('hotel_id', $hotelId)->where('id', $roomTypeId)->firstOrFail();

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $totalRooms = $data['total_rooms'];
        $availableRooms = $data['available_rooms'] ?? $totalRooms;

        $daysCreated = 0;
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            Inventory::updateOrCreate(
                [
                    'room_type_id' => $roomType->id,
                    'date'         => $currentDate->toDateString(),
                ],
                [
                    'total_rooms'     => $totalRooms,
                    'available_rooms' => $availableRooms,
                ]
            );
            $daysCreated++;
            $currentDate->addDay();
        }

        return back()->with('success', "Inventory set for {$daysCreated} days ({$totalRooms} total, {$availableRooms} available).");
    }

    // ========== BOOKINGS ==========
    public function bookings(Request $request)
    {
        $pendingCount = Hotel::where('status', 'pending')->count();
        $filters = [];
        if ($request->status && $request->status !== 'all') {
            $filters['status'] = $request->status;
        }
        $bookings = $this->bookingService->getAllBookings($filters);

        return view('admin.bookings.index', compact('bookings', 'pendingCount'));
    }

    public function createBooking()
    {
        $pendingCount = Hotel::where('status', 'pending')->count();
        $hotels = Hotel::where('status', 'approved')->with('roomTypes')->get();
        $customers = User::where('global_role', 'customer')->get();

        return view('admin.bookings.create', compact('pendingCount', 'hotels', 'customers'));
    }

    public function storeBooking(Request $request)
    {
        $data = $request->validate([
            'customer_id'     => 'required|exists:users,id',
            'hotel_id'        => 'required|exists:hotels,id',
            'room_type_id'    => 'required|exists:room_types,id',
            'guest_name'      => 'required|string|max:255',
            'guest_email'     => 'required|email|max:255',
            'guest_phone'     => 'required|string|max:30',
            'check_in_date'   => 'required|date|after_or_equal:today',
            'check_out_date'  => 'required|date|after:check_in_date',
            'number_of_rooms' => 'required|integer|min:1',
            'number_of_guests'=> 'required|integer|min:1',
            'status'          => 'required|in:pending,confirmed,cancelled',
            'notes'           => 'nullable|string',
        ]);

        $checkIn = Carbon::parse($data['check_in_date']);
        $checkOut = Carbon::parse($data['check_out_date']);
        $roomType = RoomType::findOrFail($data['room_type_id']);
        $nights = $checkIn->diffInDays($checkOut);
        $totalPrice = (float) $roomType->price_per_night * $nights * $data['number_of_rooms'];

        DB::transaction(function () use ($data, $checkIn, $checkOut, $totalPrice, $roomType) {
            $currentDate = $checkIn->copy();
            $endDate = $checkOut->copy()->subDay();

            while ($currentDate <= $endDate) {
                $inv = Inventory::where('room_type_id', $data['room_type_id'])
                    ->where('date', $currentDate->toDateString())
                    ->lockForUpdate()
                    ->first();

                if ($data['status'] === 'confirmed') {
                    if (! $inv || $inv->available_rooms < $data['number_of_rooms']) {
                        throw new \Exception("Insufficient inventory for {$currentDate->toDateString()}");
                    }
                    $inv->decrement('available_rooms', $data['number_of_rooms']);
                }

                $currentDate->addDay();
            }

            Booking::create([
                'customer_id'     => $data['customer_id'],
                'hotel_id'        => $data['hotel_id'],
                'room_type_id'    => $data['room_type_id'],
                'booking_reference'=> Booking::generateBookingReference(),
                'guest_name'      => $data['guest_name'],
                'guest_email'     => $data['guest_email'],
                'guest_phone'     => $data['guest_phone'],
                'check_in_date'   => $checkIn->toDateString(),
                'check_out_date'  => $checkOut->toDateString(),
                'number_of_rooms' => $data['number_of_rooms'],
                'number_of_guests'=> $data['number_of_guests'],
                'total_price'     => $totalPrice,
                'status'          => $data['status'],
                'notes'           => $data['notes'] ?? null,
                'confirmed_at'    => $data['status'] === 'confirmed' ? now() : null,
            ]);
        });

        return redirect()->route('admin.bookings.index')->with('success', 'Booking created successfully.');
    }

    // ========== SUPPORT CHATS ==========
    public function chats(Request $request)
    {
        $pendingCount = Hotel::where('status', 'pending')->count();
        $filters = [];
        if ($request->status && $request->status !== 'all') {
            $filters['status'] = $request->status;
        }
        $conversations = $this->chatService->getAllConversations($filters);

        return view('admin.chats.index', compact('conversations', 'pendingCount'));
    }
}
