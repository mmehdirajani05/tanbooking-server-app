<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Services\Company\CompanyService;
use Illuminate\Http\Request;

class CompanyApprovalController extends Controller
{
    public function __construct(private CompanyService $companyService) {}

    /**
     * List pending companies
     */
    public function pending()
    {
        $companies = Company::where('status', 'pending')
            ->with('owner:id,name,email,phone')
            ->withCount('modules')
            ->withCount('documents')
            ->latest()
            ->paginate(20);

        return view('admin.companies.pending', compact('companies'));
    }

    /**
     * Show company details
     */
    public function show(int $id)
    {
        $company = Company::with([
            'owner:id,name,email,phone',
            'modules',
            'documents',
        ])->findOrFail($id);

        return view('admin.companies.detail', compact('company'));
    }

    /**
     * Approve company
     */
    public function approve(int $id)
    {
        $this->companyService->approveCompany($id);

        return back()->with('success', 'Company approved successfully.');
    }

    /**
     * Reject company
     */
    public function reject(Request $request, int $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $this->companyService->rejectCompany($id, $validated['reason']);

        return back()->with('success', 'Company rejected.');
    }

    /**
     * Show company documents
     */
    public function documents(int $id)
    {
        $company = Company::with(['documents', 'modules'])->findOrFail($id);

        return view('admin.companies.documents', compact('company'));
    }

    /**
     * Verify document
     */
    public function verifyDocument(Request $request, int $docId)
    {
        $validated = $request->validate([
            'status' => 'required|in:verified,rejected',
            'reason' => 'nullable|string|max:1000',
        ]);

        $this->companyService->verifyDocument(
            $docId,
            $validated['status'],
            $validated['reason'] ?? null
        );

        return back()->with('success', 'Document ' . $validated['status'] . '.');
    }
}