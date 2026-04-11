<?php

namespace App\Services\Company;

use App\Models\Company;
use App\Models\CompanyModule;
use App\Models\CompanyDocument;
use App\Models\CompanyUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;

class CompanyService
{
    /**
     * Create a new company with modules
     */
    public function createCompany(array $data): Company
    {
        return DB::transaction(function () use ($data) {
            // Create company
            $company = Company::create([
                'owner_id' => Auth::id(),
                'company_name' => $data['company_name'],
                'display_name' => $data['display_name'],
                'business_type' => $data['business_type'],
                'registration_number' => $data['registration_number'] ?? null,
                'tin_number' => $data['tin_number'] ?? null,
                'license_number' => $data['license_number'] ?? null,
                'incorporation_date' => $data['incorporation_date'] ?? null,
                'country' => $data['country'] ?? 'Tanzania',
                'region' => $data['region'] ?? null,
                'address' => $data['address'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'contact_email' => $data['contact_email'] ?? null,
                'website' => $data['website'] ?? null,
                'status' => 'pending',
            ]);

            // Create company modules
            if (isset($data['modules']) && is_array($data['modules'])) {
                foreach ($data['modules'] as $moduleType) {
                    CompanyModule::create([
                        'company_id' => $company->id,
                        'module_type' => $moduleType,
                        'status' => 'pending',
                    ]);
                }
            }

            // Add owner as company user
            CompanyUser::create([
                'company_id' => $company->id,
                'user_id' => Auth::id(),
                'role' => 'owner',
                'status' => 'active',
            ]);

            return $company;
        });
    }

    /**
     * Upload document for company
     */
    public function uploadDocument(Company $company, UploadedFile $file, string $documentType, ?string $moduleType = null): CompanyDocument
    {
        $path = $file->store('company-documents/' . $company->id, 'public');

        return CompanyDocument::create([
            'company_id' => $company->id,
            'module_type' => $moduleType,
            'document_type' => $documentType,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'pending',
        ]);
    }

    /**
     * Approve company and its modules
     */
    public function approveCompany(int $companyId): Company
    {
        return DB::transaction(function () use ($companyId) {
            $company = Company::findOrFail($companyId);
            
            $company->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

            // Approve all modules
            $company->modules()->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            return $company;
        });
    }

    /**
     * Reject company with reason
     */
    public function rejectCompany(int $companyId, string $reason): Company
    {
        $company = Company::findOrFail($companyId);
        
        $company->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        // Reject all modules
        $company->modules()->update([
            'status' => 'rejected',
        ]);

        return $company;
    }

    /**
     * Verify a company document
     */
    public function verifyDocument(int $documentId, string $status, ?string $reason = null): CompanyDocument
    {
        $document = CompanyDocument::findOrFail($documentId);
        
        $document->update([
            'status' => $status,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => $status === 'rejected' ? $reason : null,
        ]);

        return $document;
    }

    /**
     * Check if company has all documents verified
     */
    public function hasAllDocumentsVerified(Company $company): bool
    {
        $pendingDocs = $company->documents()
            ->whereIn('status', ['pending', 'rejected'])
            ->count();

        return $pendingDocs === 0;
    }
}