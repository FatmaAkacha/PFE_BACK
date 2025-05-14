<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\LigneDocument;
use App\Models\Produit;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

    public function storeOld(Request $request)
    {
        $request->validate([
            'document_class_id' => 'required|exists:document_classes,id',
            'libelle' => 'required|in:Bon de commande,Bon de livraison,Facture',
            'etat' => 'nullable|string',
            'preparateur_id' => 'nullable|exists:users,id',
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
                'preparateur_id',
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
    public function store(Request $request)
{

    DB::beginTransaction(); // Démarre une transaction DB

    try {
        // Validation des données
        $request->validate([
            'document_class_id' => 'required|exists:document_classes,id',
            'libelle' => 'required|in:Bon de commande,Bon de livraison,Facture',
            'etat' => 'nullable|string',
            'preparateur_id' => 'nullable|exists:users,id',
            'client_id' => 'required|exists:clients,id',
            'devise' => 'required|string',
            'tauxEchange' => 'nullable|numeric',
            'dateDocument' => 'required|date',
            'dateLivraison' => 'nullable|date',
            'codeclassedocument' => 'required|string',
        ]);
        
        $lastDocument = Document::where('codeClasseDoc', $request->input('codeclassedocument'))->orderBy('id', 'desc')->first();
        $num_seq = $lastDocument ? $lastDocument->num_seq + 1 : 1;   

        // Parser les dates
        $dateDocument = Carbon::parse($request->dateDocument);
        $dateLivraison = $request->dateLivraison ? Carbon::parse($request->dateLivraison) : null;

        // Création du document
        $document = Document::create([
            'document_class_id' => $request->document_class_id,
            'libelle' => $request->libelle,
            'etat' => $request->etat,
            'preparateur_id' => $request->preparateur_id,
            'client_id' => $request->client_id,
            'devise' => $request->devise,
            'tauxEchange' => $request->tauxEchange,
            'dateDocument' => $dateDocument,
            'dateLivraison' => $dateLivraison,
            'codeClasseDoc' =>  $request->input('codeclassedocument'),
            'num_seq' => $num_seq,
        ]);

        if ( $request->input('codeclassedocument') == 'BC') { 
            foreach ($request->produitsCommandes as $item) {
                $produit = Produit::findOrFail($item['produit_id']);
    
                // Empêcher stock négatif (optionnel)
                if ($produit->quantitystock < $item['quantite']) {
                    throw new Exception("Stock insuffisant pour le produit ID {$item['produit_id']}");
                }
    
                $produit->quantitystock -= $item['quantite'];
                $produit->save();
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'Document créé avec succès.',
            'data' => $document
        ], 201);

    } catch (ValidationException $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Les données fournies sont invalides.',
            'errors' => $e->errors()
        ], 422);
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Erreur lors de la création du document : ' . $e->getMessage());
    
        return response()->json([
            'message' => 'Une erreur est survenue lors de la création du document.',
            'error' => $e->getMessage()
        ], 500);
    }
    
}

    public function storeWithLignes(Request $request)
    {
        $validated = $request->validate([
            'document_class_id' => 'required|exists:document_classes,id',
            'libelle' => 'required|string|in:Bon de commande,Bon de livraison,Facture',
            'etat' => 'nullable|string',
            'preparateur_id' => 'nullable|exists:users,id',
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
            'preparateur_id' => $validated['preparateur_id'] ?? null,
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
            'preparateur_id' => 'nullable|exists:users,id',
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
