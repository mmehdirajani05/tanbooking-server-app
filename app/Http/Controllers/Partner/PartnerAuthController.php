<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PartnerAuthController extends Controller
{
    /**
     * Show partner login form
     */
    public function showLogin()
    {
        return view('partner.auth.login');
    }

    /**
     * Handle partner login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Check if user is a partner
            if ($user->global_role !== 'partner') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have partner access. Please contact support.',
                ]);
            }

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact support.',
                ]);
            }

            $request->session()->regenerate();
            $user->update(['last_login_at' => now()]);

            return redirect()->intended(route('partner.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show partner registration form
     */
    public function showRegister()
    {
        return view('partner.auth.register');
    }

    /**
     * Handle partner registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'global_role' => 'partner',
            'registration_source' => 'web',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('partner.company.register')
            ->with('success', 'Account created successfully! Now let\'s set up your company.');
    }

    /**
     * Logout partner
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('partner.login');
    }
}