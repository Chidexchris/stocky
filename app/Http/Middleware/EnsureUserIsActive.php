<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_active != 1) {
            Auth::logout();
            return redirect()->route('login')->with('account_deactivated', 'Your account has been frozen. Please contact support.');
        }
        return $next($request);
    }
}

