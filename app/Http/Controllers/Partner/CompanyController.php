<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Services\Company\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function __construct(private CompanyService $companyService) {}

    /**
     * Show company pending page
     */
    public function pending()
    {
        $company = Auth::user()->ownedCompanies()->first();
        return view('partner.company.pending', compact('company'));
    }

    /**
     * Show company rejected page
     */
    public function rejected()
    {
        $company = Auth::user()->ownedCompanies()->where('status', 'rejected')->first();
        return view('partner.company.rejected', compact('company'));
    }

    /**
     * Show company profile
     */
    public function show()
    {
        $company = Auth::user()->companies()->where('status', 'approved')->first();
        
        if (!$company) {
            return redirect()->route('partner.company.pending');
        }

        $documents = $company->documents()->latest()->get();
        $modules = $company->modules;

        return view('partner.company.show', compact('company', 'documents', 'modules'));
    }

    /**
     * Show edit company form
     */
    public function edit()
    {
        $company = Auth::user()->companies()->where('status', 'approved')->first();
        return view('partner.company.edit', compact('company'));
    }

    /**
     * Update company
     */
    public function update(Request $request)
    {
        $company = Auth::user()->companies()->where('status', 'approved')->first();

        $validated = $request->validate([
            'display_name' => 'sometimes|string|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'contact_email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
        ]);

        $company->update($validated);

        return back()->with('success', 'Company updated successfully.');
    }

    /**
     * Show documents page
     */
    public function documents()
    {
        $company = Auth::user()->companies()->where('status', 'approved')->first();
        $documents = $company->documents()->latest()->get();
        
        return view('partner.company.documents', compact('company', 'documents'));
    }

    /**
     * Upload document
     */
    public function uploadDocument(Request $request)
    {
        $company = Auth::user()->companies()->where('status', 'approved')->first();

        $validated = $request->validate([
            'document_type' => 'required|string|in:brela,tin,tourism_license,event_license,other',
            'module_type' => 'nullable|string|in:hotel,tourism,event,esim',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $document = $this->companyService->uploadDocument(
            $company,
            $request->file('file'),
            $validated['document_type'],
            $validated['module_type'] ?? null
        );

        return back()->with('success', 'Document uploaded successfully.');
    }
}
