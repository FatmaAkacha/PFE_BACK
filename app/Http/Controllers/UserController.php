<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|string|unique:users,id',            
            'username' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id' // ← now validating by role ID
        ]);
    
        $user = new User([
            'id' => $validated['id'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'], // assume hashed in model mutator
            'role_id' => $validated['role_id']
        ]);
    
        $user->save();
    
        return response()->json($user, 201);
    }
    

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'id' => 'required|string|unique:users,id,' . $id, // allow current ID
        'username' => 'required|string',
        'email' => 'required|email|unique:users,email,' . $id, // allow current email
        'password' => 'required|string|min:6',
        'role_id' => 'required|exists:roles,id' // ← role_id directly
    ]);

    $user = User::findOrFail($id);
    $user->username = $validated['username'];
    $user->email = $validated['email'];

    if (!empty($validated['password'])) {
        $user->password = $validated['password']; // assume hashed in model
    }

    $user->role_id = $validated['role_id'];
    $user->save();

    return response()->json($user);
}


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }

    public function getUserByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::with('role')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function getRolesByUserId($id)
    {
        $user = User::with('role')->findOrFail($id);
        return response()->json($user->role);
    }
}