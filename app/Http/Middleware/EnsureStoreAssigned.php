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
            if (!$user->hasRole('Super Admin') && is_null($user->store_id)) {
                return redirect()->route('home')->with('error', 'No store assigned to your account.');
            }
        }
        return $next($request);
    }
}
