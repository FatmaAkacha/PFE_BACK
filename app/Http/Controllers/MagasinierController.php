<?php

namespace App\Http\Controllers;

use App\Models\Magasinier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MagasinierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $magasiniers = Magasinier::all();
        return response()->json($magasiniers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Si tu utilises des vues Blade
        return view('magasiniers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'numero_telephone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:magasiniers,email',
        ]);

        $validated['id'] = (string) Str::uuid();

        $magasinier = Magasinier::create($validated);

        return response()->json($magasinier, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $magasinier = Magasinier::findOrFail($id);
        return response()->json($magasinier);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $magasinier = Magasinier::findOrFail($id);

        // Si tu utilises des vues Blade
        return view('magasiniers.edit', compact('magasinier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $magasinier = Magasinier::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'numero_telephone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:magasiniers,email,' . $magasinier->id,
        ]);

        $magasinier->update($validated);

        return response()->json($magasinier);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $magasinier = Magasinier::findOrFail($id);
        $magasinier->delete();

        return response()->json(['message' => 'Magasinier supprimé avec succès.']);
    }
}