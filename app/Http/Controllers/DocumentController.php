<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\LigneDocument;
use App\Models\Produit;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with('documentClass')->get();
        return response()->json($documents);
    }

    public function show($id)
    {
        $document = Document::with('lignes.produit')->find($id);
        if (!$document) {
            return response()->json(['message' => 'Document introuvable'], 404);
        }
        return response()->json($document);
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_class_id' => 'required|exists:document_classes,id',
            'libelle' => 'required|in:Bon de commande,Bon de livraison,Facture',
            'etat' => 'nullable|string',
            'preparateur' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'devise' => 'required|string',
            'tauxEchange' => 'nullable|numeric',
            'dateDocument' => 'required|date',
            'dateLivraison' => 'nullable|date',
        ]);

        $lastDocument = Document::where('codeClasseDoc', 'BC')->orderBy('id', 'desc')->first();
        $num_seq = $lastDocument ? $lastDocument->num_seq + 1 : 1;

        $document = Document::create(array_merge(
            $request->only([
                'document_class_id',
                'libelle',
                'etat',
                'preparateur',
                'client_id',
                'devise',
                'tauxEchange',
                'dateDocument',
                'dateLivraison'
            ]),
            ['codeClasseDoc' => 'BC', 'num_seq' => $num_seq]
        ));

        return response()->json($document, 201);
    }

    public function storeWithLignes(Request $request)
    {
        $validated = $request->validate([
            'document_class_id' => 'required|exists:document_classes,id',
            'libelle' => 'required|string|in:Bon de commande,Bon de livraison,Facture',
            'etat' => 'nullable|string',
            'preparateur' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'devise' => 'required|string',
            'tauxEchange' => 'nullable|numeric',
            'dateDocument' => 'required|date',
            'dateLivraison' => 'nullable|date',
            'lignes' => 'required|array|min:1',
            'lignes.*.produit_id' => 'required|exists:produits,id',
            'lignes.*.designation' => 'required|string',
            'lignes.*.stock' => 'required|integer',
            'lignes.*.quantite' => 'required|integer|min:1',
            'lignes.*.puht' => 'required|numeric',
            'lignes.*.tva' => 'required|numeric',
            'lignes.*.ttc' => 'required|numeric',
        ]);

        $lastDocument = Document::where('codeClasseDoc', 'BC')->orderBy('id', 'desc')->first();
        $num_seq = $lastDocument ? $lastDocument->num_seq + 1 : 1;

        $document = Document::create([
            'document_class_id' => $validated['document_class_id'],
            'codeClasseDoc' => 'BC',
            'libelle' => $validated['libelle'],
            'num_seq' => $num_seq,
            'etat' => $validated['etat'] ?? null,
            'preparateur' => $validated['preparateur'] ?? null,
            'client_id' => $validated['client_id'],
            'devise' => $validated['devise'],
            'tauxEchange' => $validated['tauxEchange'] ?? null,
            'dateDocument' => $validated['dateDocument'],
            'dateLivraison' => $validated['dateLivraison'] ?? null,
        ]);

        foreach ($validated['lignes'] as $ligneData) {
            $ligneData['document_id'] = $document->id;
            LigneDocument::create($ligneData);

            // Mise à jour du stock
            Produit::where('id', $ligneData['produit_id'])->decrement('quantitystock', $ligneData['quantite']);
        }

        return response()->json($document->load('lignes.produit'), 201);
    }

    public function update(Request $request, $id)
    {
        $document = Document::find($id);
        if (!$document) {
            return response()->json(['message' => 'Document introuvable'], 404);
        }

        $request->validate([
            'document_class_id' => 'required|exists:document_classes,id',
            'libelle' => 'required|in:Bon de commande,Bon de livraison,Facture',
            'etat' => 'nullable|string',
            'preparateur' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'devise' => 'required|string',
            'tauxEchange' => 'nullable|numeric',
            'dateDocument' => 'required|date',
            'dateLivraison' => 'nullable|date',
        ]);

        $document->update($request->all());

        return response()->json($document);
    }

    public function destroy($id)
    {
        $document = Document::find($id);
        if (!$document) {
            return response()->json(['message' => 'Document introuvable'], 404);
        }

        $document->delete();
        return response()->json(['message' => 'Document supprimé']);
    }

    public function printBonCommande($id)
    {
        $document = Document::with('lignes.produit')->findOrFail($id);
        $pdf = PDF::loadView('documents.print', compact('document'));
        return $pdf->stream('document.pdf');
    }
}
