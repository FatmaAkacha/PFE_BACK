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
        return response()->json(Fournisseur::all(), Response::HTTP_OK);
    }

    /**
     * Ajouter un nouveau fournisseur.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:fournisseurs',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'matricule_fiscal' => 'nullable|string|max:50'
        ]);

        $fournisseur = Fournisseur::create($request->all());

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

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:fournisseurs,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'matricule_fiscal' => 'nullable|string|max:50'
        ]);

        $fournisseur->update($request->all());

        return response()->json($fournisseur, Response::HTTP_OK);
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

        $fournisseur->delete();

        return response()->json(['message' => 'Fournisseur supprimé avec succès'], Response::HTTP_OK);
    }
}