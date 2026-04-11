<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $module  The module type to check (hotel, tourism, event, esim)
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has approved company with this module
        $hasAccess = $user->companies()
            ->where('companies.status', 'approved')
            ->whereHas('modules', function ($query) use ($module) {
                $query->where('module_type', $module)
                      ->where('status', 'approved');
            })
            ->exists();

        if (!$hasAccess) {
            abort(403, "You do not have access to the {$module} module. Please contact support.");
        }

        return $next($request);
    }
}