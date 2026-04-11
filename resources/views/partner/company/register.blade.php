@extends('partner.auth.layout')

@section('title', 'Register Your Company')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-2xl w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg mb-4">
                <i class="fas fa-building text-primary-600 text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Register Your Company</h1>
            <p class="mt-2 text-gray-600">Basic information to get started</p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form method="POST" action="{{ route('partner.company.register.post') }}">
                @csrf

                @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                    <ul class="text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Company Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Company Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Legal Company Name *</label>
                            <input type="text" name="company_name" required value="{{ old('company_name') }}" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none"
                                placeholder="Tanzania Safari Tours Ltd">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Display Name *</label>
                            <input type="text" name="display_name" required value="{{ old('display_name') }}" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none"
                                placeholder="Tanzania Safari Tours">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Type *</label>
                            <select name="business_type" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none">
                                <option value="">Select Type</option>
                                <option value="Company">Company</option>
                                <option value="Individual">Individual</option>
                                <option value="Partnership">Partnership</option>
                                <option value="NGO">NGO</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Registration Number</label>
                            <input type="text" name="registration_number" value="{{ old('registration_number') }}" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none"
                                placeholder="BRELA12345">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">TIN Number</label>
                            <input type="text" name="tin_number" value="{{ old('tin_number') }}" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none"
                                placeholder="TIN987654">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                            <select name="country" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none">
                                <option value="Tanzania">Tanzania</option>
                                <option value="Kenya">Kenya</option>
                                <option value="Uganda">Uganda</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Region *</label>
                            <select name="region" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none">
                                <option value="">Select Region</option>
                                <option value="Arusha">Arusha</option>
                                <option value="Dar es Salaam">Dar es Salaam</option>
                                <option value="Zanzibar">Zanzibar</option>
                                <option value="Kilimanjaro">Kilimanjaro</option>
                                <option value="Mwanza">Mwanza</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Select Modules -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Modules *</h3>
                    <p class="text-sm text-gray-500 mb-4">You can add hotels, tour packages, or events after approval</p>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary-500 transition">
                            <input type="checkbox" name="modules[]" value="hotel" class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <div class="ml-3">
                                <i class="fas fa-hotel text-primary-600 text-xl mb-1"></i>
                                <p class="font-medium text-gray-900">Hotel Booking</p>
                                <p class="text-xs text-gray-500">List & manage hotels</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary-500 transition">
                            <input type="checkbox" name="modules[]" value="tourism" class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <div class="ml-3">
                                <i class="fas fa-map-marked-alt text-primary-600 text-xl mb-1"></i>
                                <p class="font-medium text-gray-900">Tourism</p>
                                <p class="text-xs text-gray-500">Create tour packages</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary-500 transition">
                            <input type="checkbox" name="modules[]" value="event" class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <div class="ml-3">
                                <i class="fas fa-calendar-alt text-primary-600 text-xl mb-1"></i>
                                <p class="font-medium text-gray-900">Events</p>
                                <p class="text-xs text-gray-500">Manage events & tickets</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary-500 transition">
                            <input type="checkbox" name="modules[]" value="esim" class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <div class="ml-3">
                                <i class="fas fa-sim-card text-primary-600 text-xl mb-1"></i>
                                <p class="font-medium text-gray-900">eSIM</p>
                                <p class="text-xs text-gray-500">Sell eSIM packages</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 transition font-semibold text-lg shadow-lg hover:shadow-xl">
                    <i class="fas fa-paper-plane mr-2"></i>Submit for Approval
                </button>
            </form>

            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> After approval, you can add hotels with full details, room types, and manage bookings.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection