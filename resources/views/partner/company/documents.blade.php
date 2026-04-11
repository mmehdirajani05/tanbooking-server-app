@extends('partner.auth.layout')

@section('title', 'Documents')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Upload Company Documents</h2>
            
            <form method="POST" action="{{ route('partner.company.documents.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Document Type</label>
                        <select name="document_type" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none">
                            <option value="">Select Document Type</option>
                            <option value="brela">BRELA Registration Certificate</option>
                            <option value="tin">TIN Certificate</option>
                            <option value="tourism_license">Tourism License</option>
                            <option value="event_license">Event License</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Document File</label>
                        <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none">
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                    </div>
                    <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 transition font-semibold">
                        <i class="fas fa-upload mr-2"></i>Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection