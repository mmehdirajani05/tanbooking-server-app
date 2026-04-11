@extends('partner.layouts.app')

@section('title', 'Company Pending Approval')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
        <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-clock text-amber-600 text-4xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Company Under Review</h2>
        <p class="text-gray-600 mb-6">Your company registration is being reviewed by our admin team. You will be notified once it's approved.</p>
        
        @if($company)
        <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
            <h3 class="font-semibold text-gray-900 mb-3">Company Details</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Company Name:</span>
                    <span class="font-medium">{{ $company->company_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status:</span>
                    <span class="px-2 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">Pending</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Submitted:</span>
                    <span class="font-medium">{{ $company->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-left">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>What's next?</strong> Our team will review your application and documents. This usually takes 1-2 business days.
            </p>
        </div>
    </div>
</div>
@endsection