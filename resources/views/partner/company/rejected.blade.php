@extends('partner.layouts.app')

@section('title', 'Company Rejected')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-times-circle text-red-600 text-4xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Application Rejected</h2>
        <p class="text-gray-600 mb-6">Unfortunately, your company application was not approved at this time.</p>
        
        @if($company && $company->rejection_reason)
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6 text-left">
            <h3 class="font-semibold text-red-900 mb-2">Reason for Rejection:</h3>
            <p class="text-red-800">{{ $company->rejection_reason }}</p>
        </div>
        @endif

        <div class="bg-gray-50 rounded-lg p-4 text-left">
            <p class="text-sm text-gray-700">
                <i class="fas fa-lightbulb mr-2 text-amber-500"></i>
                <strong>What can you do?</strong> You can contact support for clarification or resubmit your application with corrected information.
            </p>
        </div>
    </div>
</div>
@endsection