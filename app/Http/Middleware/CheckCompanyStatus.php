<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has any approved company
        $hasApprovedCompany = $user->companies()
            ->where('companies.status', 'approved')
            ->exists();

        if (!$hasApprovedCompany && $request->routeIs('partner.*')) {
            // Don't redirect if already on pending page
            if ($request->routeIs('partner.company.pending')) {
                return $next($request);
            }
            
            return redirect()->route('partner.company.pending')
                ->with('warning', 'Your company is pending approval. You will be notified once approved.');
        }

        // If company is rejected, show rejection page
        $rejectedCompany = $user->companies()
            ->where('status', 'rejected')
            ->first();

        if ($rejectedCompany && !$request->routeIs('partner.company.rejected')) {
            return redirect()->route('partner.company.rejected')
                ->with('error', 'Your company was rejected: ' . $rejectedCompany->rejection_reason);
        }

        return $next($request);
    }
}