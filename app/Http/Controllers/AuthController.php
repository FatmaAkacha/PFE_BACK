<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'This is a protected endpoint']);
    }

    public function getRolesFromToken(Request $request)
    {
        // Récupération du token JWT
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Missing or invalid Authorization header'], 401);
        }

        $token = substr($authHeader, 7); // Supprime "Bearer "

        // Décode le payload du JWT
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return response()->json(['error' => 'Invalid JWT token'], 400);
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        if (!$payload || !isset($payload['realm_access']['roles'])) {
            return response()->json(['error' => 'Roles not found in token'], 400);
        }

        $roles = $payload['realm_access']['roles'];
        return response()->json(['roles' => $roles]);
    }
}