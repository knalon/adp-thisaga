<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckUserBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_banned) {
            // Share user ban status with all views
            Inertia::share('userBanned', true);
            
            // If attempting to perform actions that should be blocked for banned users
            if ($this->isBlockedAction($request)) {
                return redirect()->route('contact')->with('error', 'Your account has been banned. Please contact our support team for assistance.');
            }
        }
        
        return $next($request);
    }
    
    /**
     * Determine if the request is attempting a restricted action
     */
    private function isBlockedAction(Request $request): bool
    {
        // Actions that should be blocked for banned users
        $blockedRoutes = [
            'cars.store',
            'cars.update',
            'cars.create',
            'cars.edit',
            'appointments.store',
            'appointments.create',
            'appointments.submitBid',
            'transaction.pay',
        ];
        
        // Check if current route name is in the blocked list
        if (in_array($request->route()->getName(), $blockedRoutes)) {
            return true;
        }
        
        // Check for POST/PUT/PATCH/DELETE requests that might be actions
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return true;
        }
        
        return false;
    }
}
