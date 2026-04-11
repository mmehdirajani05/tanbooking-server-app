@extends('partner.auth.layout')

@section('title', 'Register Your Company')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-3xl w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg mb-4">
                <i class="fas fa-building text-primary-600 text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Register Your Company</h1>
            <p class="mt-2 text-gray-600">Tell us about your business to get started</p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8" x-data="{
            modules: [],
            activeTab: null,
            setActiveTab(module) {
                if (this.modules.includes(module)) {
                    this.activeTab = module;
                }
            },
            init() {
                this.$watch('modules', (value) => {
                    if (!this.activeTab || !value.includes(this.activeTab)) {
                        this.activeTab = value.length > 0 ? value[0] : null;
                    }
                });
            },
            get hasModules() { return this.modules.length > 0; }
        }">
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

                <!-- Step 1: Company Details -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center font-semibold">1</div>
                        <h3 class="text-lg font-semibold text-gray-900">Company Details</h3>
                    </div>

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
                    </div>
                </div>

                <!-- Step 2: Location -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center font-semibold">2</div>
                        <h3 class="text-lg font-semibold text-gray-900">Location</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none" placeholder="123 Safari Road, Arusha"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Select Modules -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center font-semibold">3</div>
                        <h3 class="text-lg font-semibold text-gray-900">Select Modules *</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary-500 transition">
                            <input type="checkbox" name="modules[]" value="hotel" x-model="modules" class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <div class="ml-3">
                                <i class="fas fa-hotel text-primary-600 text-xl mb-1"></i>
                                <p class="font-medium text-gray-900">Hotel Booking</p>
                                <p class="text-xs text-gray-500">List & manage hotels</p>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary-500 transition">
                            <input type="checkbox" name="modules[]" value="tourism" x-model="modules" class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <div class="ml-3">
                                <i class="fas fa-map-marked-alt text-primary-600 text-xl mb-1"></i>
                                <p class="font-medium text-gray-900">Tourism</p>
                                <p class="text-xs text-gray-500">Create tour packages</p>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary-500 transition">
                            <input type="checkbox" name="modules[]" value="event" x-model="modules" class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <div class="ml-3">
                                <i class="fas fa-calendar-alt text-primary-600 text-xl mb-1"></i>
                                <p class="font-medium text-gray-900">Events</p>
                                <p class="text-xs text-gray-500">Manage events & tickets</p>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary-500 transition">
                            <input type="checkbox" name="modules[]" value="esim" x-model="modules" class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <div class="ml-3">
                                <i class="fas fa-sim-card text-primary-600 text-xl mb-1"></i>
                                <p class="font-medium text-gray-900">eSIM</p>
                                <p class="text-xs text-gray-500">Sell eSIM packages</p>
                            </div>
                        </label>
                    </div>

                    <!-- Tabs for selected modules -->
                    <div x-show="hasModules" x-transition class="border-b border-gray-200">
                        <nav class="flex space-x-4">
                            <template x-if="modules.includes('hotel')">
                                <button type="button" @click="activeTab = 'hotel'" 
                                    :class="activeTab === 'hotel' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-3 px-4 border-b-2 font-medium text-sm transition">
                                    <i class="fas fa-hotel mr-2"></i>Hotel Details
                                </button>
                            </template>
                            <template x-if="modules.includes('tourism')">
                                <button type="button" @click="activeTab = 'tourism'" 
                                    :class="activeTab === 'tourism' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-3 px-4 border-b-2 font-medium text-sm transition">
                                    <i class="fas fa-map-marked-alt mr-2"></i>Tourism Details
                                </button>
                            </template>
                            <template x-if="modules.includes('event')">
                                <button type="button" @click="activeTab = 'event'" 
                                    :class="activeTab === 'event' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-3 px-4 border-b-2 font-medium text-sm transition">
                                    <i class="fas fa-calendar-alt mr-2"></i>Event Details
                                </button>
                            </template>
                            <template x-if="modules.includes('esim')">
                                <button type="button" @click="activeTab = 'esim'" 
                                    :class="activeTab === 'esim' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-3 px-4 border-b-2 font-medium text-sm transition">
                                    <i class="fas fa-sim-card mr-2"></i>eSIM Details
                                </button>
                            </template>
                        </nav>
                    </div>
                </div>

                <!-- Step 4: Module-Specific Details (Only shows active tab) -->
                <div class="mb-8">
                    
                    <!-- Hotel Details -->
                    <div x-show="activeTab === 'hotel'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-blue-900 mb-4">
                            <i class="fas fa-hotel mr-2"></i>Hotel Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-blue-800 mb-2">Number of Properties</label>
                                <input type="number" name="hotel_property_count" min="1" value="1" 
                                    class="w-full border border-blue-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none bg-white"
                                    placeholder="1">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-blue-800 mb-2">Hotel Category</label>
                                <select name="hotel_category" class="w-full border border-blue-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                    <option value="">Select Category</option>
                                    <option value="luxury">Luxury (5-star)</option>
                                    <option value="upscale">Upscale (4-star)</option>
                                    <option value="mid_scale">Mid-scale (3-star)</option>
                                    <option value="economy">Economy (1-2 star)</option>
                                    <option value="boutique">Boutique</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tourism Details -->
                    <div x-show="activeTab === 'tourism'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-green-50 border border-green-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-green-900 mb-4">
                            <i class="fas fa-map-marked-alt mr-2"></i>Tourism Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-green-800 mb-2">Tour Operator License Number</label>
                                <input type="text" name="tourism_license_number" 
                                    class="w-full border border-green-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 outline-none bg-white"
                                    placeholder="TO-2024-12345">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-green-800 mb-2">Region of Operation</label>
                                <select name="tourism_region" class="w-full border border-green-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 outline-none bg-white">
                                    <option value="">Select Region</option>
                                    <option value="mainland">Mainland Tanzania</option>
                                    <option value="zanzibar">Zanzibar</option>
                                    <option value="both">Both</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-green-800 mb-2">Tour Types Offered</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="tour_types[]" value="safari" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                        <span class="ml-2 text-sm text-gray-700">Safari</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="tour_types[]" value="cultural" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                        <span class="ml-2 text-sm text-gray-700">Cultural Tours</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="tour_types[]" value="beach" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                        <span class="ml-2 text-sm text-gray-700">Beach/Marine</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="tour_types[]" value="adventure" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                        <span class="ml-2 text-sm text-gray-700">Adventure</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Details -->
                    <div x-show="activeTab === 'event'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-purple-50 border border-purple-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-purple-900 mb-4">
                            <i class="fas fa-calendar-alt mr-2"></i>Event Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-purple-800 mb-2">Event License Number</label>
                                <input type="text" name="event_license_number" 
                                    class="w-full border border-purple-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 outline-none bg-white"
                                    placeholder="EV-2024-12345">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-purple-800 mb-2">Primary Event Type</label>
                                <select name="event_type" class="w-full border border-purple-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 outline-none bg-white">
                                    <option value="">Select Type</option>
                                    <option value="concert">Concerts & Music</option>
                                    <option value="conference">Conferences</option>
                                    <option value="sports">Sports Events</option>
                                    <option value="cultural">Cultural Festivals</option>
                                    <option value="corporate">Corporate Events</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- eSIM Details -->
                    <div x-show="activeTab === 'esim'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-orange-50 border border-orange-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-orange-900 mb-4">
                            <i class="fas fa-sim-card mr-2"></i>eSIM Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-orange-800 mb-2">eSIM Provider Partner</label>
                                <input type="text" name="esim_provider" value="YAS" readonly
                                    class="w-full border border-orange-300 rounded-lg px-4 py-3 bg-orange-100 text-orange-800 outline-none">
                                <p class="text-xs text-orange-600 mt-1">Currently integrated with YAS eSIM portal</p>
                            </div>
                        </div>
                    </div>

                    <!-- No module selected message -->
                    <div x-show="!hasModules" class="bg-gray-50 border border-gray-200 rounded-xl p-8 text-center">
                        <i class="fas fa-arrow-up text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">Select at least one module above to see specific details form</p>
                    </div>

                </div>

                <!-- Submit -->
                <button type="submit" 
                    class="w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 transition font-semibold text-lg shadow-lg hover:shadow-xl"
                    :disabled="!hasModules">
                    <i class="fas fa-paper-plane mr-2"></i>Submit for Approval
                </button>
            </form>

            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> After submission, our team will review your application. This usually takes 1-2 business days.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection