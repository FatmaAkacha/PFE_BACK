<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LigneDocument;
use App\Models\Document;
use App\Models\Produit;
use Illuminate\Http\Request;

class LigneDocumentController extends Controller
{
   // ✅ Récupérer toutes les lignes de tous les documents
   public function index()
   {
       $lignes = LigneDocument::with('document', 'produit')->get();
       return response()->json($lignes);
   }

   // ✅ Récupérer les lignes d’un document spécifique
   public function getByDocument($documentId)
   {
       $lignes = LigneDocument::where('document_id', $documentId)->with('produit')->get();
       return response()->json($lignes);
   }

   // ✅ Récupérer une ligne spécifique
   public function show($id)
   {
       $ligne = LigneDocument::with('document', 'produit')->find($id);
       if (!$ligne) {
           return response()->json(['message' => 'Ligne introuvable'], 404);
       }
       return response()->json($ligne);
   }

   // ✅ Créer une nouvelle ligne
   public function store(Request $request)
   {
       $validated = $request->validate([
           'document_id' => 'required|exists:documents,id',
           'produit_id' => 'required|exists:produits,id',
           'code' => 'nullable|string|max:100',
           'designation' => 'required|string|max:255',
           'stock' => 'required|integer',
           'quantite' => 'required|integer',
           'puht' => 'required|numeric',
           'tva' => 'required|numeric',
           'ttc' => 'required|numeric',
       ]);

       $ligne = LigneDocument::create($validated);

       return response()->json($ligne, 201);
   }

   // ✅ Modifier une ligne existante
   public function update(Request $request, $id)
   {
       $ligne = LigneDocument::find($id);
       if (!$ligne) {
           return response()->json(['message' => 'Ligne introuvable'], 404);
       }

       $validated = $request->validate([
           'produit_id' => 'sometimes|exists:produits,id',
           'code' => 'nullable|string|max:100',
           'designation' => 'sometimes|string|max:255',
           'stock' => 'sometimes|integer',
           'quantite' => 'sometimes|integer',
           'puht' => 'sometimes|numeric',
           'tva' => 'sometimes|numeric',
           'ttc' => 'sometimes|numeric',
       ]);

       $ligne->update($validated);

       return response()->json($ligne);
   }

   // ✅ Supprimer une ligne
   public function destroy($id)
   {
       $ligne = LigneDocument::find($id);
       if (!$ligne) {
           return response()->json(['message' => 'Ligne introuvable'], 404);
       }

       $ligne->delete();

       return response()->json(['message' => 'Ligne supprimée avec succès']);
   }
   public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            '*.document_id' => 'required|exists:documents,id',
            '*.produit_id' => 'required|exists:produits,id',
            '*.code' => 'nullable|string|max:100',
            '*.designation' => 'required|string|max:255',
            '*.stock' => 'required|integer',
            '*.quantite' => 'required|integer',
            '*.puht' => 'required|numeric',
            '*.tva' => 'required|numeric',
            '*.ttc' => 'required|numeric',
        ]);

        $created = [];
        foreach ($validated as $data) {
            $created[] = \App\Models\LigneDocument::create($data);
        }

        return response()->json($created, 201);
    }

}