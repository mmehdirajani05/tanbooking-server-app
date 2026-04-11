<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TanBooking Partner')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="h-full bg-gray-50">
    <div class="min-h-full" x-data="{ sidebarOpen: false }">
        <!-- Sidebar for desktop -->
        <div class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0">
            <div class="flex flex-col flex-grow bg-white border-r border-gray-200 pt-5 pb-4 overflow-y-auto">
                <div class="flex items-center flex-shrink-0 px-4 mb-5">
                    <a href="{{ route('partner.dashboard') }}" class="text-2xl font-bold text-primary-600">
                        <i class="fas fa-hotel mr-2"></i>TanBooking
                    </a>
                </div>
                <nav class="mt-5 flex-1 px-2 space-y-1">
                    <a href="{{ route('partner.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('partner.dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-chart-pie mr-3 text-lg {{ request()->routeIs('partner.dashboard') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                        Dashboard
                    </a>
                    
                    @if(auth()->user()->companies()->where('companies.status', 'approved')->exists())
                    <a href="{{ route('partner.company.show') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('partner.company.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-building mr-3 text-lg {{ request()->routeIs('partner.company.*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                        Company Profile
                    </a>
                    @endif

                    @if(auth()->user()->hasApprovedCompanyWithModule('hotel'))
                    <a href="{{ route('partner.hotels.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('partner.hotels.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-hotel mr-3 text-lg {{ request()->routeIs('partner.hotels.*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                        My Hotels
                    </a>
                    @endif

                    @if(auth()->user()->hasApprovedCompanyWithModule('tourism'))
                    <a href="{{ route('partner.tourism.packages.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('partner.tourism.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-map-marked-alt mr-3 text-lg {{ request()->routeIs('partner.tourism.*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                        Tourism Packages
                    </a>
                    @endif

                    @if(auth()->user()->hasApprovedCompanyWithModule('event'))
                    <a href="{{ route('partner.events.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('partner.events.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-calendar-alt mr-3 text-lg {{ request()->routeIs('partner.events.*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                        Events
                    </a>
                    @endif

                    <a href="{{ route('partner.bookings.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('partner.bookings.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-calendar-check mr-3 text-lg {{ request()->routeIs('partner.bookings.*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                        Bookings
                    </a>
                </nav>
                <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                    <div class="flex items-center">
                        <div>
                            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-xs font-medium text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-sign-out-alt mr-1"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile sidebar -->
        <div class="lg:hidden" x-show="sidebarOpen" @click.away="sidebarOpen = false">
            <div class="fixed inset-0 flex z-40">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button @click="sidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>
                    <!-- Mobile nav content same as desktop -->
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-64 flex flex-col">
            <!-- Top bar -->
            <div class="sticky top-0 z-10 bg-white border-b border-gray-200 lg:hidden">
                <div class="flex justify-between h-16 px-4">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <a href="{{ route('partner.dashboard') }}" class="ml-4 text-xl font-bold text-primary-600">
                            <i class="fas fa-hotel mr-2"></i>TanBooking
                        </a>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1 py-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                            <div class="flex">
                                <i class="fas fa-check-circle text-green-400 mt-0.5"></i>
                                <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                                <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                            <div class="flex">
                                <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
                                <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
                                <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mb-4 bg-amber-50 border-l-4 border-amber-400 p-4 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                            <div class="flex">
                                <i class="fas fa-exclamation-triangle text-amber-400 mt-0.5"></i>
                                <p class="ml-3 text-sm text-amber-700">{{ session('warning') }}</p>
                                <button @click="show = false" class="ml-auto text-amber-500 hover:text-amber-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
