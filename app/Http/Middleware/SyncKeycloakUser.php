<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class SyncKeycloakUser
{
    public function handle(Request $request, Closure $next)
    {
        $kcUser = auth()->user();

        if ($kcUser && is_object($kcUser)) {
            User::syncFromToken((array) $kcUser);
        }

        return $next($request);
    }
}
