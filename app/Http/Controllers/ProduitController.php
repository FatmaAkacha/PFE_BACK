<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ProduitController extends Controller
{
    // Afficher tous les produits
    public function index()
    {
        $produits = Produit::all(); // Récupère tous les produits
        return response()->json($produits); // Retourne les produits au format JSON
    }

    // Afficher un produit spécifique
    public function show($id)
    {
        $produit = Produit::find($id); // Récupère un produit par son ID

        if (!$produit) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        return response()->json($produit); // Retourne le produit spécifique
    }

    // Créer un nouveau produit
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|integer',
            'quantitystock' => 'required|integer',
            'seuil' => 'required|integer',
        ]);

        $produit = Produit::create($request->all()); // Crée un nouveau produit

        return response()->json($produit, 201); // Retourne le produit créé avec un code 201 (créé)
    }

    // Mettre à jour un produit existant
    public function update(Request $request, $id)
    {
        $produit = Produit::find($id);

        if (!$produit) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|integer',
            'quantitystock' => 'required|integer',
            'seuil' => 'required|integer',
        ]);

        $produit->update($request->all()); // Met à jour le produit

        return response()->json($produit); // Retourne le produit mis à jour
    }

    // Supprimer un produit
    public function destroy($id)
    {
        $produit = Produit::find($id);

        if (!$produit) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        $produit->delete(); // Supprime le produit

        return response()->json(['message' => 'Produit supprimé avec succès'], 200); // Message de succès
    }
}
