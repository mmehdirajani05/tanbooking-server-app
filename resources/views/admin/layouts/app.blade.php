<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TanBooking Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="h-full bg-gray-50">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200" x-data="{ mobileMenuOpen: false }">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex flex-shrink-0 items-center">
                            <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold text-primary-600">
                                <i class="fas fa-hotel mr-2"></i>TanBooking
                            </a>
                        </div>
                        <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.dashboard') ? 'border-primary-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.companies.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.companies.*') ? 'border-primary-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Companies
                                @if(\App\Models\Company::where('status', 'pending')->count() > 0)
                                    <span class="ml-2 bg-amber-100 text-amber-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                        {{ \App\Models\Company::where('status', 'pending')->count() }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('admin.hotels.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.hotels.*') ? 'border-primary-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Hotels
                            </a>
                            <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.bookings.*') ? 'border-primary-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Bookings
                            </a>
                        </div>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" type="button" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2" id="user-menu-button">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                </button>
                            </div>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu">
                                <div class="px-4 py-2 text-sm text-gray-700 border-b border-gray-100">
                                    <p class="font-medium">{{ auth()->user()->name }}</p>
                                    <p class="text-gray-500 text-xs">{{ auth()->user()->email }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                            <i class="fas" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Mobile menu -->
            <div x-show="mobileMenuOpen" class="sm:hidden">
                <div class="space-y-1 pb-3 pt-2">
                    <a href="{{ route('admin.dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.dashboard') ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium">Dashboard</a>
                    <a href="{{ route('admin.companies.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.companies.*') ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium">Companies</a>
                    <a href="{{ route('admin.hotels.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.hotels.*') ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium">Hotels</a>
                    <a href="{{ route('admin.bookings.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.bookings.*') ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium">Bookings</a>
                </div>
                <div class="border-t border-gray-200 pb-3 pt-4">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold text-lg">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button @click="show = false" class="text-green-500 hover:text-green-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button @click="show = false" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-4 bg-amber-50 border-l-4 border-amber-400 p-4 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-amber-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-amber-700">{{ session('warning') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button @click="show = false" class="text-amber-500 hover:text-amber-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Page Header -->
                @if(isset($pageTitle))
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $pageTitle }}</h1>
                    @if(isset($pageSubTitle))
                        <p class="mt-1 text-sm text-gray-500">{{ $pageSubTitle }}</p>
                    @endif
                </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>