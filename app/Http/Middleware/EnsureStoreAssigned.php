<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureStoreAssigned
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Super Admin can access everything
            if ($user->hasRole('Super Admin')) {
                return $next($request);
            }

            // Business Owner without a store is allowed — they will create one later
            if ($user->business_id) {
                return $next($request);
            }

            // No business AND no store — invalid state, log out
            if (is_null($user->business_id) && is_null($user->store_id)) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'No business assigned. Please contact support.');
            }
        }
        return $next($request);
    }
}
