<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::with('categorie', 'fournisseur')->get();
        foreach ($produits as $produit) {
            if (!mb_check_encoding($produit->image_data, 'UTF-8')) {
                $produit->image_data = utf8_encode($produit->image_data);
            }
        }
    
        return response()->json($produits);
    }
    

    public function show($id)
    {
        $produit = Produit::find($id);
        if (!$produit) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }
        
        if (!mb_check_encoding($produit->image_data, 'UTF-8')) {
            $produit->image_data = utf8_encode($produit->image_data);
        }
        
        return response()->json($produit);
        
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom'              => 'required|string|max:255',
            'description'      => 'required|string',
            'prix_achat'       => 'required|numeric',
            'prix_vente_ht'    => 'required|numeric',
            'prix_vente_ttc'   => 'required|numeric',
            'remise_maximale'  => 'nullable|numeric|min:0|max:100',
            'quantitystock'    => 'required|integer',
            'quantite'         => 'required|integer',
            'seuil'            => 'required|integer',
            'image_data'       => 'nullable|file|image|max:2048',
            'categorie_id'     => 'required|integer|exists:categories,id',
            'fournisseur_id'   => 'required|uuid|exists:fournisseurs,id',
            'inventoryStatus'  => 'nullable|string',
            'rating'           => 'nullable|numeric',
        ]);
        

        $data = $validatedData;
        if ($request->hasFile('image_data')) {
            $file = $request->file('image_data');
            // Stocke l'image dans le dossier 'uploads' du disk 'public'
            $path = $file->store('uploads', 'public');
            $data['image_data'] = $path; // juste le chemin

        }
        

        $produit = Produit::create($data);
        return response()->json($produit, 201);
    }

    public function update(Request $request, $id)
    {
        $produit = Produit::find($id);
        if (!$produit) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        $validatedData = $request->validate([
            'nom'              => 'required|string|max:255',
            'description'      => 'required|string',
            'prix_achat'       => 'required|numeric',
            'prix_vente_ht'    => 'required|numeric',
            'prix_vente_ttc'   => 'required|numeric',
            'remise_maximale'  => 'nullable|numeric|min:0|max:100',
            'quantitystock'    => 'required|integer',
            'quantite'         => 'required|integer',
            'seuil'            => 'required|integer',
            'image_data'       => 'nullable|file|image|max:2048',
            'categorie_id'     => 'required|integer|exists:categories,id',
            'fournisseur_id'   => 'required|uuid|exists:fournisseurs,id',
            'inventoryStatus'  => 'nullable|string',
            'rating'           => 'nullable|numeric',
        ]);
        

        $data = $validatedData;

        if ($request->hasFile('image_data')) {
            $file = $request->file('image_data');
            $path = $file->store('uploads', 'public');
            $data['image_data'] = $path; 

        }
        

        $produit->update($data);
        return response()->json($produit);
    }

    public function destroy($id) {
        $produit = Produit::findOrFail($id);
        
        $imageData = storage_path('app/public/' . $produit->image_data);
        if (file_exists($imageData)) {
            unlink($imageData);  // supprimer l'image
        }
    
        $produit->delete();
    
        return response()->json(['message' => 'Produit supprimé avec succès.'], 200);
    }
    

    public function getImage($id) {
        $produit = Produit::findOrFail($id);
        $path = storage_path('app/public/' . $produit->image_data);
        if (!file_exists($path)) {
            return response()->json(['message' => 'Image not found.'], 404);
        }
        return response()->file($path);
    }
    

    public function serveImage($id)
    {
        $produit = Produit::find($id);
        if (!$produit || !$produit->image_data) {
            return response()->json(['message' => 'Image non trouvée'], 404);
        }
    
        // Le chemin complet du fichier
        $path = storage_path('app/public/' . $produit->image_data);
        if (!file_exists($path)) {
            return response()->json(['message' => 'Image non trouvée'], 404);
        }
    
        return response()->file($path);
    }
    
}
