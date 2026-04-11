<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerBookingController extends Controller
{
    /**
     * List all bookings for this partner's company
     */
    public function index(Request $request)
    {
        $company = Auth::user()->companies()->where('status', 'approved')->first();

        $query = Booking::where('company_id', $company->id)
            ->with(['customer:id,name,email,phone', 'hotel:id,name,city']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('module_type') && $request->module_type !== 'all') {
            $query->where('module_type', $request->module_type);
        }

        $bookings = $query->latest()->paginate(20);

        return view('partner.bookings.index', compact('bookings'));
    }
}
