<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SecretController extends Controller
{
    public function index()
    {
        // Votre logique ici (par exemple, retourner un message ou des données)
        return response()->json(['message' => 'This is a protected endpoint']);
    }
}
