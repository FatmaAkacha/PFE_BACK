<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategories()
    {
        return response()->json(Categorie::all(), 200);
    }

    public function getCategoryById($id)
    {
        $categorie = Categorie::find($id);
        if (is_null($categorie)) {
            return response()->json(["message" => "Catégorie non trouvée"], 404);
        }
        return response()->json($categorie, 200);
    }

    public function show($id)
    {
        $categorie = Categorie::find($id);
        if ($categorie) {
            return response()->json($categorie, 200);
        } else {
            return response()->json(['error' => 'Catégorie non trouvée'], 404);
        }
    }

    public function index()
    {
        $categories = Categorie::all();
        return view('categories.index', compact('categories'));
    }

    public function insertCategory(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $categorie = Categorie::create($request->all());
        return response($categorie, 201);
    }

    public function store(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $categorie = new Categorie();
    $categorie->nom = $request->nom;
    $categorie->description = $request->description ?? null;
    $categorie->save();

    return response()->json($categorie, 201);
}


    public function updateCategory(Request $request, $id)
    {
        $categorie = Categorie::find($id);
        if (is_null($categorie)) {
            return response()->json(['error' => 'Catégorie non trouvée'], 404);
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $categorie->update($request->all());
        return response($categorie, 200);
    }

    public function update(Request $request, Categorie $categorie)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $categorie->update([
            'nom' => $request->nom,
            'description' => 'nullable|string',
        ]);

        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function deleteCategory($id)
    {
        $categorie = Categorie::find($id);
        if (is_null($categorie)) {
            return response()->json(['error' => 'Catégorie non trouvée'], 404);
        }
        $categorie->delete();
        return response(null, 204);
    }
}
