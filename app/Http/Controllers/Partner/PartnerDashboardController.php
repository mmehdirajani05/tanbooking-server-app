<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Booking;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class PartnerDashboardController extends Controller
{
    /**
     * Partner dashboard - shows stats based on their company's modules
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's approved company
        $company = $user->companies()
            ->where('status', 'approved')
            ->first();

        if (!$company) {
            return redirect()->route('partner.company.pending');
        }

        $modules = $company->modules->pluck('module_type')->toArray();
        
        $stats = [
            'modules' => $modules,
            'hotels' => null,
            'tourism' => null,
            'events' => null,
            'bookings' => null,
            'revenue' => null,
        ];

        // Load stats based on modules
        if (in_array('hotel', $modules)) {
            $stats['hotels'] = [
                'total' => Hotel::where('company_id', $company->id)->count(),
                'approved' => Hotel::where('company_id', $company->id)->where('status', 'approved')->count(),
                'pending' => Hotel::where('company_id', $company->id)->where('status', 'pending')->count(),
            ];
        }

        // Get bookings for this company
        $stats['bookings'] = [
            'total' => Booking::where('company_id', $company->id)->count(),
            'pending' => Booking::where('company_id', $company->id)->where('status', 'pending')->count(),
            'confirmed' => Booking::where('company_id', $company->id)->where('status', 'confirmed')->count(),
            'revenue' => Booking::where('company_id', $company->id)
                ->where('status', 'confirmed')
                ->sum('total_price'),
        ];

        return view('partner.dashboard', compact('company', 'stats'));
    }
}