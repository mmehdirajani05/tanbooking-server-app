@extends('admin.layouts.app')

@section('title', $company->display_name)
@section('page-title', $company->display_name)
@section('pageSubTitle', 'Company Details & Approval')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Company Details -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 text-2xl font-bold">
                        {{ substr($company->display_name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $company->company_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $company->business_type }}</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    {{ $company->status === 'approved' ? 'bg-green-100 text-green-800' : 
                       ($company->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ ucfirst($company->status) }}
                </span>
            </div>

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
                <div class="col-span-2">
                    <p class="text-gray-500">Address</p>
                    <p class="font-medium">{{ $company->address ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Contact Phone</p>
                    <p class="font-medium">{{ $company->contact_phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Contact Email</p>
                    <p class="font-medium">{{ $company->contact_email ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Modules -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Requested Modules</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($company->modules as $module)
                <div class="border border-gray-200 rounded-lg p-4 text-center">
                    <i class="fas 
                        {{ $module->module_type === 'hotel' ? 'fa-hotel' : 
                           ($module->module_type === 'tourism' ? 'fa-map-marked-alt' : 
                           ($module->module_type === 'event' ? 'fa-calendar-alt' : 'fa-sim-card')) }} 
                        text-3xl text-primary-600 mb-2"></i>
                    <p class="font-medium text-gray-900 capitalize">{{ $module->module_type }}</p>
                    <span class="inline-block mt-2 px-2 py-1 rounded text-xs font-medium 
                        {{ $module->status === 'approved' ? 'bg-green-100 text-green-800' : 
                           ($module->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ ucfirst($module->status) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Documents -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Submitted Documents</h4>
            <div class="space-y-3">
                @forelse($company->documents as $doc)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-pdf text-2xl text-red-500"></i>
                        <div>
                            <p class="font-medium text-gray-900">{{ $doc->document_type }}</p>
                            <p class="text-sm text-gray-500">{{ $doc->file_name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-1 rounded text-xs font-medium 
                            {{ $doc->status === 'verified' ? 'bg-green-100 text-green-800' : 
                               ($doc->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ ucfirst($doc->status) }}
                        </span>
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-primary-600 hover:text-primary-900">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No documents uploaded yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Approval Actions -->
    <div class="space-y-6">
        <!-- Owner Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Owner Information</h4>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-semibold">
                        {{ substr($company->owner->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium">{{ $company->owner->name }}</p>
                        <p class="text-sm text-gray-500">{{ $company->owner->email }}</p>
                    </div>
                </div>
                @if($company->owner->phone)
                <p class="text-sm text-gray-600"><i class="fas fa-phone mr-2"></i>{{ $company->owner->phone }}</p>
                @endif
            </div>
        </div>

        <!-- Actions -->
        @if($company->status === 'pending')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Approval Actions</h4>
            
            <!-- Approve Button -->
            <form action="{{ route('admin.companies.approve', $company->id) }}" method="POST" class="mb-4" onsubmit="return confirm('Are you sure you want to approve this company?')">
                @csrf
                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium">
                    <i class="fas fa-check mr-2"></i>Approve Company
                </button>
            </form>

            <!-- Reject Form -->
            <div x-data="{ showRejectForm: false, reason: '' }">
                <button @click="showRejectForm = !showRejectForm" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition font-medium">
                    <i class="fas fa-times mr-2"></i>Reject Company
                </button>
                
                <div x-show="showRejectForm" class="mt-4 space-y-3" x-cloak>
                    <textarea x-model="reason" name="reason" rows="3" placeholder="Enter rejection reason..." 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 outline-none"></textarea>
                    <form action="{{ route('admin.companies.reject', $company->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="reason" :value="reason">
                        <button type="submit" class="w-full bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800 transition font-medium" :disabled="!reason">
                            Confirm Rejection
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection