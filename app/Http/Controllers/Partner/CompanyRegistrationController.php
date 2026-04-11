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
            'modules' => 'required|array|min:1',
            'modules.*' => 'in:hotel,tourism,event,esim',
        ]);

        $company = $this->companyService->createCompany($validated);

        return redirect()->route('partner.company.pending')
            ->with('success', 'Company registration submitted! Your application is under review.');
    }
}