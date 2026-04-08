<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — TanBooking Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/30">
            <i class="fas fa-hotel text-white text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-white">TanBooking</h1>
        <p class="text-blue-200 text-sm mt-1">Admin Panel</p>
    </div>

    <!-- Login Form -->
    <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20">
        <h2 class="text-xl font-semibold text-white mb-6">Sign in to your account</h2>

        @if($errors->any())
        <div class="mb-4 bg-red-500/20 border border-red-500/30 text-red-200 px-4 py-3 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-blue-100 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-blue-300 text-sm"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:ring-2 focus:ring-blue-400 focus:border-transparent outline-none transition"
                               placeholder="admin@tanbooking.com">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-blue-100 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-blue-300 text-sm"></i>
                        </div>
                        <input type="password" name="password" required
                               class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:ring-2 focus:ring-blue-400 focus:border-transparent outline-none transition"
                               placeholder="••••••••">
                    </div>
                </div>
            </div>
            <button type="submit" class="w-full mt-6 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl transition shadow-lg shadow-blue-500/30">
                <i class="fas fa-sign-in-alt mr-2"></i>Sign In
            </button>
        </form>
    </div>

    <p class="text-center text-blue-300/60 text-xs mt-6">Admin access only · Protected area</p>
</div>

</body>
</html>
