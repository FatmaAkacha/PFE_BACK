<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FournisseurController extends Controller
{
    /**
     * Afficher la liste des fournisseurs.
     */
    public function index()
    {
        try {
            $fournisseurs = Fournisseur::all();
            foreach ($fournisseurs as $fournisseur) {
                if (!mb_check_encoding($fournisseur->logo, 'UTF-8')) {
                    $fournisseur->logo = utf8_encode($fournisseur->logo);
                }
            }                
            return response()->json($fournisseurs);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des fournisseurs : ' . $e->getMessage());
            return response()->json(['message' => 'Erreur interne du serveur'], 500);
        }
    }
    /**
     * Ajouter un nouveau fournisseur.
     */
    public function store(Request $request)
    {
        $validatedData =$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:fournisseurs',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'matricule_fiscal' => 'nullable|string|max:50',
            'logo' => 'nullable|file|image|max:2048',
        ]);
        $data = $validatedData;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('uploads', 'public');
            \Log::info('Fichier téléchargé', ['path' => $path]);
            $data['logo'] = $path;
        }

        $fournisseur = Fournisseur::create($data);
        return response()->json($fournisseur , 201);

        return response()->json($fournisseur, Response::HTTP_CREATED);
    }

    /**
     * Afficher un fournisseur spécifique.
     */
    public function show($id)
    {
        $fournisseur = Fournisseur::find($id);

        if (!$fournisseur) {
            return response()->json(['message' => 'Fournisseur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($fournisseur, Response::HTTP_OK);
    }

    /**
     * Mettre à jour un fournisseur.
     */
    public function update(Request $request, $id)
    {
        $fournisseur = Fournisseur::find($id);

        if (!$fournisseur) {
            return response()->json(['message' => 'Fournisseur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:fournisseurs,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'matricule_fiscal' => 'nullable|string|max:50',
            'logo'=> 'nullable|file|image|max:2048',
        ]);
        $data = $validatedData;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('uploads', 'public');
            \Log::info('Fichier téléchargé', ['path' => $path]);
            $data['logo'] = $path;
        }

        $fournisseur->update($data);
        return response()->json($fournisseur , 201);
    }

    /**
     * Supprimer un fournisseur.
     */
    public function destroy($id)
    {
        $fournisseur = Fournisseur::find($id);

        if (!$fournisseur) {
            return response()->json(['message' => 'Fournisseur non trouvé'], Response::HTTP_NOT_FOUND);
        }

            // Supprime le fichier logo s'il existe
            $logoPath = storage_path('app/public/' . $fournisseur->logo);
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
        $fournisseur->delete();

        return response()->json(['message' => 'Fournisseur supprimé avec succès'], Response::HTTP_OK);
    }

    public function getImage($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $path = storage_path('app/public/' . $fournisseur->logo);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Image not found.'], 404);
        }

        return response()->file($path);
    }

    public function serveImage($id)
{
    $fournisseur = Fournisseur::find($id);

    if (!$fournisseur || !$fournisseur->logo) {
        return response()->json(['message' => 'Image non trouvée'], 404);
    }

    $path = storage_path('app/public/' . $fournisseur->logo);

    // Vérifier si le fichier existe avant de le renvoyer
    if (!file_exists($path)) {
        return response()->json(['message' => 'Image non trouvée sur le serveur'], 404);
    }

    return response()->file($path);
}

}
