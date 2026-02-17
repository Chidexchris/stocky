<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminIpWhitelist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = config('app.superadmin_allowed_ips');

        // If no IPs are configured, allow all (to prevent accidental lockout)
        if (empty($allowedIps)) {
            return $next($request);
        }

        $ips = is_array($allowedIps) ? $allowedIps : explode(',', $allowedIps);
        $ips = array_map('trim', $ips);

        if (!in_array($request->ip(), $ips)) {
            abort(403, 'Your IP address (' . $request->ip() . ') is not authorized to access the Super Admin dashboard.');
        }

        return $next($request);
    }
}
