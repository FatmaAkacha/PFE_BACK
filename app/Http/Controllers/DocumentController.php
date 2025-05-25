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
        DB::beginTransaction();

        try {
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

            $code = $request->input('codeclassedocument');
           // $lastSeq = Document::where('codeClasseDoc', $code)->max('num_seq');
           // $num_seq = $lastSeq ? $lastSeq + 1 : 1;

            $document = Document::create([
                'document_class_id' => $request->document_class_id,
                'libelle' => $request->libelle,
                'etat' => $request->etat,
                'preparateur_id' => $request->preparateur_id,
                'client_id' => $request->client_id,
                'devise' => $request->devise,
                'tauxEchange' => $request->tauxEchange,
                'dateDocument' => Carbon::parse($request->dateDocument),
                'dateLivraison' => $request->dateLivraison ? Carbon::parse($request->dateLivraison) : null,
                'codeClasseDoc' => $code,
                'num_seq' => $num_seq,
                'numero' => $numero,
                ]);

            if ($code == 'BC' && $request->has('produitsCommandes')) {
                foreach ($request->produitsCommandes as $item) {
                    $produit = Produit::findOrFail($item['produit_id']);

                    if ($produit->quantitystock < $item['quantite']) {
                        throw new Exception("Stock insuffisant pour le produit ID {$item['produit_id']}");
                    }

                    $produit->decrement('quantitystock', $item['quantite']);
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
                'message' => 'Une erreur est survenue.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
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
                'produitsCommandes' => 'array|required|min:1',
                'produitsCommandes.*.produit_id' => 'required|exists:produits,id',
                'produitsCommandes.*.quantite' => 'required|numeric|min:1',
                'produitsCommandes.*.puht' => 'required|numeric|min:0',
                'produitsCommandes.*.prixTotal' => 'required|numeric|min:0',
            ]);

            $code = $request->input('codeclassedocument');

            // 🔢 GÉNÉRATION DU NUMÉRO
            $dernierDocument = Document::where('codeClasseDoc', $code)->latest()->first();
            if ($dernierDocument && preg_match('/(\d+)$/', $dernierDocument->numero, $matches)) {
                $numero = str_pad((int)$matches[1] + 1, 5, '0', STR_PAD_LEFT);
            } else {
                $numero = '00001';
            }

            // 🧮 VÉRIFICATION STOCK POUR BC et BL
            if (in_array($code, ['BC', 'BL'])) {
                foreach ($request->produitsCommandes as $item) {
                    $produit = Produit::findOrFail($item['produit_id']);
                    if ($produit->quantitystock < $item['quantite']) {
                        throw new \Exception("Stock insuffisant pour le produit ID {$item['produit_id']}");
                    }
                }
            }

            // 📄 CRÉATION DU DOCUMENT
            $document = Document::create([
                'document_class_id' => $request->document_class_id,
                'libelle' => $request->libelle,
                'etat' => $request->etat,
                'preparateur_id' => $request->preparateur_id,
                'client_id' => $request->client_id,
                'devise' => $request->devise,
                'tauxEchange' => $request->tauxEchange,
                'dateDocument' => Carbon::parse($request->dateDocument),
                'dateLivraison' => $request->dateLivraison ? Carbon::parse($request->dateLivraison) : null,
                'codeClasseDoc' => $code,
                'numero' => $numero,
            ]);

            // ➕ ENREGISTREMENT DES LIGNES
            foreach ($request->produitsCommandes as $item) {
                $produit = Produit::findOrFail($item['produit_id']);

                LigneDocument::create([
                    'document_id' => $document->id,
                    'produit_id' => $produit->id,
                    'code' => $produit->code ?? null,
                    'designation' => $produit->designation ?? $produit->nom,
                    'stock' => $produit->quantitystock,
                    'quantite' => $item['quantite'],
                    'puht' => $item['puht'],
                    'tva' => $item['tva'] ?? 0,
                    'ttc' => $item['prixTotal'],
                ]);

                // ➖ DÉCRÉMENTATION DU STOCK
                if (in_array($code, ['BC', 'BL'])) {
                    $produit->decrement('quantitystock', $item['quantite']);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Document créé avec succès.',
                'numero' => $numero,
                'data' => $document->load(['lignesDocument', 'client']),
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Les données fournies sont invalides.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du document : ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur est survenue.',
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ], 500);
        }
    }




    public function storeWithLignes(Request $request)
    {
        DB::beginTransaction();

        try {
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

           // $num_seq = Document::where('codeClasseDoc', 'BC')->max('num_seq') + 1 ?? 1;

            $document = Document::create([
                'document_class_id' => $validated['document_class_id'],
                'codeClasseDoc' => 'BC',
                'libelle' => $validated['libelle'],
                'num_seq' => $num_seq,
                'numero' => 100,
                'etat' => $validated['etat'] ?? null,
                'preparateur_id' => $validated['preparateur_id'] ?? null,
                'client_id' => $validated['client_id'],
                'devise' => $validated['devise'],
                'tauxEchange' => $validated['tauxEchange'] ?? null,
                'dateDocument' => $validated['dateDocument'],
                'dateLivraison' => $validated['dateLivraison'] ?? null,
            ]);

            foreach ($validated['lignes'] as $ligne) {
                LigneDocument::create(array_merge($ligne, [
                    'document_id' => $document->id,
                ]));

                Produit::where('id', $ligne['produit_id'])->decrement('quantitystock', $ligne['quantite']);
            }

            DB::commit();
            return response()->json($document->load('lignes.produit'), 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Données invalides.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la création du document avec lignes : " . $e->getMessage());
            return response()->json([
                'message' => 'Erreur serveur.',
                'error' => $e->getMessage()
            ], 500);
        }
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

    public function getDernierCode($codeClasseDoc)
    {
        $dernierDocument = Document::where('codeClasseDoc', $codeClasseDoc)->latest()->first();

        if ($dernierDocument) {
            preg_match('/(\d+)$/', $dernierDocument->numero, $matches);
            $dernierNumero = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
        } else {
            $dernierNumero = 1;
        }

        return response()->json(str_pad($dernierNumero, 4, '0', STR_PAD_LEFT));
    }

    public function getDocumentByIdAndCode($id, $codeClasseDoc)
{
    $document = Document::with('client')
        ->where('id', $id)
        ->where('codeClasseDoc', $codeClasseDoc)
        ->first();

    if (!$document) {
        return response()->json(['message' => 'Document introuvable avec ce code.'], 404);
    }

    return response()->json($document);
}

}
