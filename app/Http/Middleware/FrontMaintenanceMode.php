<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class FrontMaintenanceMode
{
    private const CACHE_KEY = 'cms.settings';

    public function handle(Request $request, Closure $next): Response
    {
        $maintenanceModeEnabled = (bool) (Cache::get(self::CACHE_KEY)['maintenance_mode'] ?? false);

        if (! $maintenanceModeEnabled) {
            return $next($request);
        }

        // Keep frontend reachable for admins while maintenance mode is enabled.
        $user = $request->user();

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return $next($request);
        }

        abort(503, 'The site is currently in maintenance mode.');
    }
}

