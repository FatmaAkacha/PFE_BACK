<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckKeycloakRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $token = $request->user(); // utilisateur injecté par Passport/JWT

        if (!$token || !isset($token->token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Lire les rôles Keycloak dans le token
        $kcRoles = $token->token['realm_access']['roles'] ?? [];

        // Vérifie si l'utilisateur a un des rôles demandés
        foreach ($roles as $role) {
            if (in_array($role, $kcRoles)) {
                return $next($request);
            }
        }

        return response()->json(['error' => 'Forbidden'], 403);
    }
}