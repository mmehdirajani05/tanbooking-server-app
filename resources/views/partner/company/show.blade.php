@extends('partner.layouts.app')

@section('title', 'Company Profile')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Company Profile</h1>
        <p class="mt-1 text-sm text-gray-500">Manage your company details and documents</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Company Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-semibold text-xl">
                    {{ substr($company->display_name, 0, 1) }}
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $company->company_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $company->business_type }}</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium 
                {{ $company->status === 'approved' ? 'bg-green-100 text-green-800' : 
                   ($company->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                {{ ucfirst($company->status) }}
            </span>
        </div>

        <!-- Company Details -->
        <div class="p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Company Details</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Registration Number</p>
                    <p class="font-medium">{{ $company->registration_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">TIN Number</p>
                    <p class="font-medium">{{ $company->tin_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Country</p>
                    <p class="font-medium">{{ $company->country }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Region</p>
                    <p class="font-medium">{{ $company->region ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Modules -->
        <div class="px-6 py-4 border-t border-gray-200">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Active Modules</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($company->modules as $module)
                    <span class="px-3 py-1 rounded-full text-xs font-medium 
                        {{ $module->status === 'approved' ? 'bg-green-100 text-green-800' : 
                           ($module->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ ucfirst($module->module_type) }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Documents -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold text-gray-900">Documents</h4>
                <a href="{{ route('partner.company.documents') }}" class="text-sm text-primary-600 hover:text-primary-700">
                    <i class="fas fa-upload mr-1"></i>Upload Documents
                </a>
            </div>
            <div class="space-y-3">
                @forelse($documents as $doc)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-pdf text-2xl text-red-500"></i>
                        <div>
                            <p class="font-medium text-gray-900">{{ $doc->document_type }}</p>
                            <p class="text-sm text-gray-500">{{ $doc->file_name }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 rounded text-xs font-medium 
                        {{ $doc->status === 'verified' ? 'bg-green-100 text-green-800' : 
                           ($doc->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ ucfirst($doc->status) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No documents uploaded yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection