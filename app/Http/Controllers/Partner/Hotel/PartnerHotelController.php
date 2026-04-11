<?php

namespace App\Http\Controllers\Partner\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PartnerHotelController extends Controller
{
    /**
     * List hotels for this partner's company
     */
    public function index()
    {
        $company = Auth::user()->companies()->where('companies.status', 'approved')->first();
        
        $hotels = Hotel::where('company_id', $company->id)
            ->withCount('roomTypes')
            ->withCount('bookings')
            ->latest()
            ->paginate(15);

        return view('partner.hotels.index', compact('hotels', 'company'));
    }

    /**
     * Show create hotel form
     */
    public function create()
    {
        $company = Auth::user()->companies()->where('companies.status', 'approved')->first();
        return view('partner.hotels.create', compact('company'));
    }

    /**
     * Store hotel
     */
    public function store(Request $request)
    {
        $company = Auth::user()->companies()->where('companies.status', 'approved')->first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'city' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'amenities' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'retail_price' => 'nullable|numeric|min:0',
            'contract_price' => 'nullable|numeric|min:0',
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('hotels/' . $company->id, 'public');
                $imagePaths[] = Storage::url($path);
            }
        }

        $hotel = Hotel::create([
            'company_id' => $company->id,
            'owner_id' => Auth::id(), // Keep for backward compatibility
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'city' => $validated['city'],
            'area' => $validated['area'],
            'address' => $validated['address'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'amenities' => $validated['amenities'] ?? null,
            'images' => !empty($imagePaths) ? $imagePaths : null,
            'check_in_time' => $validated['check_in_time'] ?? '14:00:00',
            'check_out_time' => $validated['check_out_time'] ?? '12:00:00',
            'status' => 'pending',
            'retail_price' => $validated['retail_price'] ?? null,
            'contract_price' => $validated['contract_price'] ?? null,
        ]);

        return redirect()->route('partner.hotels.index')
            ->with('success', 'Hotel created successfully and pending approval.');
    }

    /**
     * Show hotel details
     */
    public function show(int $id)
    {
        $company = Auth::user()->companies()->where('companies.status', 'approved')->first();
        
        $hotel = Hotel::where('company_id', $company->id)
            ->with(['roomTypes', 'bookings'])
            ->withCount('roomTypes')
            ->withCount('bookings')
            ->findOrFail($id);

        return view('partner.hotels.detail', compact('hotel', 'company'));
    }

    /**
     * Show edit hotel form
     */
    public function edit(int $id)
    {
        $company = Auth::user()->companies()->where('companies.status', 'approved')->first();
        
        $hotel = Hotel::where('company_id', $company->id)->findOrFail($id);

        return view('partner.hotels.edit', compact('hotel', 'company'));
    }

    /**
     * Update hotel
     */
    public function update(Request $request, int $id)
    {
        $company = Auth::user()->companies()->where('companies.status', 'approved')->first();
        
        $hotel = Hotel::where('company_id', $company->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'city' => 'sometimes|string|max:255',
            'area' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'amenities' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'search_tags' => 'nullable|array',
            'retail_price' => 'nullable|numeric|min:0',
            'contract_price' => 'nullable|numeric|min:0',
        ]);

        // Handle image uploads (append to existing)
        if ($request->hasFile('images')) {
            $existingImages = $hotel->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('hotels/' . $company->id, 'public');
                $existingImages[] = Storage::url($path);
            }
            $validated['images'] = $existingImages;
        }

        // If hotel was rejected and owner updates it, reset to pending
        if ($hotel->status === 'rejected') {
            $validated['status'] = 'pending';
            $validated['rejection_reason'] = null;
        }

        $hotel->update($validated);

        return redirect()->route('partner.hotels.index')
            ->with('success', 'Hotel updated successfully.');
    }

    /**
     * Delete hotel
     */
    public function destroy(int $id)
    {
        $company = Auth::user()->companies()->where('companies.status', 'approved')->first();
        
        $hotel = Hotel::where('company_id', $company->id)->findOrFail($id);
        $hotel->delete();

        return back()->with('success', 'Hotel deleted successfully.');
    }
}
