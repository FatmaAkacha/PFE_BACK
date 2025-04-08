<?php

namespace App\Http\Controllers;

use App\Models\DocumentClass;
use Illuminate\Http\Request;

class DocumentClassController extends Controller
{
    // ✅ Récupérer toutes les classes de documents
    public function index()
    {
        return response()->json(DocumentClass::all());
    }

    // ✅ Récupérer une classe de document par son ID
    public function show($id)
    {
        $documentClass = DocumentClass::find($id);
        if (!$documentClass) {
            return response()->json(['message' => 'Class not found'], 404);
        }
        return response()->json($documentClass);
    }

    // ✅ Ajouter une nouvelle classe de document
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'prefixe' => 'required|string|max:10',
            'isvent' => 'required|boolean',
            'isachat' => 'required|boolean',
            'actif' => 'required|boolean',
        ]);

        $documentClass = DocumentClass::create($request->all());
        return response()->json($documentClass, 201);
    }

    // ✅ Mettre à jour une classe de document
    public function update(Request $request, $id)
    {
        $documentClass = DocumentClass::find($id);
        if (!$documentClass) {
            return response()->json(['message' => 'Class not found'], 404);
        }

        $request->validate([
            'libelle' => 'required|string|max:255',
            'prefixe' => 'required|string|max:10',
            'isvent' => 'required|boolean',
            'isachat' => 'required|boolean',
            'actif' => 'required|boolean',
        ]);

        $documentClass->update($request->all());
        return response()->json($documentClass);
    }

    // ✅ Supprimer une classe de document
    public function destroy($id)
    {
        $documentClass = DocumentClass::find($id);
        if (!$documentClass) {
            return response()->json(['message' => 'Class not found'], 404);
        }

        $documentClass->delete();
        return response()->json(['message' => 'Class deleted successfully']);
    }
}
