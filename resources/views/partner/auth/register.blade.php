@extends('partner.auth.layout')

@section('title', 'Partner Registration')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg mb-4">
                <i class="fas fa-user-plus text-primary-600 text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Become a Partner</h1>
            <p class="mt-2 text-gray-600">Create your account to start listing properties</p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form method="POST" action="{{ route('partner.register.post') }}">
                @csrf

                @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
                        <div class="ml-3">
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Full Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-1 text-gray-400"></i> Full Name
                    </label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition"
                        placeholder="John Doe">
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-1 text-gray-400"></i> Email Address
                    </label>
                    <input type="email" id="email" name="email" required value="{{ old('email') }}" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition"
                        placeholder="partner@example.com">
                </div>

                <!-- Phone -->
                <div class="mb-6">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-phone mr-1 text-gray-400"></i> Phone Number (Optional)
                    </label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition"
                        placeholder="+255 123 456 789">
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1 text-gray-400"></i> Password
                    </label>
                    <input type="password" id="password" name="password" required 
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition"
                        placeholder="Minimum 8 characters">
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1 text-gray-400"></i> Confirm Password
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required 
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition"
                        placeholder="Re-enter password">
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 transition font-semibold text-lg shadow-lg hover:shadow-xl">
                    <i class="fas fa-user-plus mr-2"></i>Create Account
                </button>
            </form>

            <!-- Divider -->
            <div class="mt-6 relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Already have an account?</span>
                </div>
            </div>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <a href="{{ route('partner.login') }}" class="text-primary-600 hover:text-primary-700 font-semibold">
                    <i class="fas fa-sign-in-alt mr-1"></i>Sign In
                </a>
            </div>
        </div>

        <!-- Benefits -->
        <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Why Partner With Us?</h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                    <p class="text-sm text-gray-600">Manage hotels, tourism packages, and events</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                    <p class="text-sm text-gray-600">Real-time booking management</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                    <p class="text-sm text-gray-600">Reach thousands of travelers</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection