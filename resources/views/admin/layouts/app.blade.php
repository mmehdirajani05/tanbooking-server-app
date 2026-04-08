<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — TanBooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' },
                        sidebar: { DEFAULT:'#0f172a', hover:'#1e293b', active:'#334155' }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link.active { background: #1e293b; border-left: 3px solid #3b82f6; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 3px; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 h-screen w-64 bg-sidebar text-white z-50 transition-transform duration-300"
    x-data="{ open: true }"
    :class="{ '-translate-x-full': !open, 'translate-x-0': open }">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-700">
        <div class="w-9 h-9 bg-primary-500 rounded-lg flex items-center justify-center">
            <i class="fas fa-hotel text-white text-sm"></i>
        </div>
        <div>
            <h1 class="text-lg font-bold tracking-wide">TanBooking</h1>
            <p class="text-[10px] text-gray-400 uppercase tracking-wider">Admin Panel</p>
        </div>
        <button @click="open = !open" class="ml-auto text-gray-400 hover:text-white lg:hidden">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Nav Links -->
    <nav class="mt-4 px-0">
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-sidebar-hover hover:text-white transition @if(request()->routeIs('admin.dashboard')) active @endif">
            <i class="fas fa-chart-pie w-5 text-center"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.hotels.index') }}"
           class="sidebar-link flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-sidebar-hover hover:text-white transition @if(request()->routeIs('admin.hotels.*')) active @endif">
            <i class="fas fa-building w-5 text-center"></i>
            <span>Hotels</span>
            @if($pendingCount ?? 0 > 0)
                <span class="ml-auto bg-amber-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
            @endif
        </a>

        <a href="{{ route('admin.bookings.index') }}"
           class="sidebar-link flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-sidebar-hover hover:text-white transition @if(request()->routeIs('admin.bookings.*')) active @endif">
            <i class="fas fa-calendar-check w-5 text-center"></i>
            <span>Bookings</span>
        </a>

        <a href="{{ route('admin.chats.index') }}"
           class="sidebar-link flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-sidebar-hover hover:text-white transition @if(request()->routeIs('admin.chats.*')) active @endif">
            <i class="fas fa-comments w-5 text-center"></i>
            <span>Support Chat</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="absolute bottom-0 w-full px-6 py-4 border-t border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-sm font-bold">
                A
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">Admin</p>
                <p class="text-xs text-gray-400 truncate">admin@tanbooking.com</p>
            </div>
        </div>
    </div>
</aside>

<!-- Main Content -->
<div class="lg:ml-64 min-h-screen">
    <!-- Top Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-4">
                <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')"
                        class="lg:hidden text-gray-500 hover:text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">{{ now()->format('M d, Y') }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700 ml-3">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="p-6">
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>
