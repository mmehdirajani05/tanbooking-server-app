@extends('partner.layouts.app')

@section('title', 'Edit Company')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Company</h1>
        <p class="mt-1 text-sm text-gray-500">Update your company details</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('partner.company.update') }}">
            @method('PUT')
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Display Name</label>
                    <input type="text" name="display_name" value="{{ old('display_name', $company->display_name) }}" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $company->contact_phone) }}" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $company->contact_email) }}" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                    <input type="url" name="website" value="{{ old('website', $company->website) }}" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none"
                        placeholder="https://example.com">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none">{{ old('address', $company->address) }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('partner.company.show') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection