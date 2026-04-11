<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Services\Company\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyRegistrationController extends Controller
{
    public function __construct(private CompanyService $companyService) {}

    /**
     * Show company registration form
     */
    public function create()
    {
        return view('partner.company.register');
    }

    /**
     * Store company registration
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'business_type' => 'required|in:Company,Individual,Partnership,NGO',
            'registration_number' => 'nullable|string|max:100',
            'tin_number' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'region' => 'required|string|max:100',
            'address' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:30',
            'contact_email' => 'nullable|email|max:255',
            'modules' => 'required|array|min:1',
            'modules.*' => 'in:hotel,tourism,event,esim',
            
            // Hotel-specific
            'hotel_property_count' => 'nullable|integer|min:1',
            'hotel_category' => 'nullable|in:luxury,upscale,mid_scale,economy,boutique',
            
            // Tourism-specific
            'tourism_license_number' => 'nullable|string|max:100',
            'tourism_region' => 'nullable|in:mainland,zanzibar,both',
            'tour_types' => 'nullable|array',
            'tour_types.*' => 'in:safari,cultural,beach,adventure',
            
            // Event-specific
            'event_license_number' => 'nullable|string|max:100',
            'event_type' => 'nullable|in:concert,conference,sports,cultural,corporate',
            
            // eSIM-specific
            'esim_provider' => 'nullable|string|max:100',
        ]);

        $company = $this->companyService->createCompany($validated);

        // Store module-specific details if needed (can be extended later)
        // For now, all validated data is available for future use

        return redirect()->route('partner.company.pending')
            ->with('success', 'Company registration submitted! Your application is under review.');
    }
}