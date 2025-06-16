<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\LigneDocument;
use App\Models\Produit;
use App\Models\Magasinier;
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
        $document = Document::with('lignes.produit', 'magasinier')->find($id);
        if (!$document) {
            return response()->json(['message' => 'Document introuvable'], 404);
        }
        return response()->json($document);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Définir les types de documents
            $clientDocuments = ['BC', 'BL', 'FC']; // Bon de commande, Bon de livraison, Facture
            $fournisseurDocuments = ['BCF', 'BR', 'FF']; // Bon de commande fournisseur, Bon de réception, Facture fournisseur

            // Validation dynamique
            $validationRules = [
                'document_class_id' => 'required|exists:document_classes,id',
                'libelle' => 'required|in:Bon de commande,Bon de livraison,Facture,Bon de commande fournisseur,Bon de réception,Facture fournisseur',
                'etat' => 'nullable|string',
                'preparateur_id' => 'required|exists:magasiniers,id',
                'devise' => 'required|string',
                'tauxEchange' => 'nullable|numeric',
                'dateDocument' => 'required|date',
                'dateLivraison' => 'nullable|date',
                'codeclassedocument' => 'required|string|in:' . implode(',', array_merge($clientDocuments, $fournisseurDocuments)),
                'produitsCommandes' => 'array|required|min:1',
                'produitsCommandes.*.produit_id' => 'required|exists:produits,id',
                'produitsCommandes.*.quantite' => 'nullable|numeric|min:1',
                'produitsCommandes.*.puht' => 'required|numeric|min:0',
                'produitsCommandes.*.prixTotal' => 'required|numeric|min:0',
            ];

            // Ajouter la validation conditionnelle pour client_id ou fournisseur_id
            $codeClasseDoc = $request->input('codeclassedocument');
            if (in_array($codeClasseDoc, $clientDocuments)) {
                $validationRules['client_id'] = 'required|exists:clients,id';
                $validationRules['fournisseur_id'] = 'nullable|exists:fournisseurs,id';
            } elseif (in_array($codeClasseDoc, $fournisseurDocuments)) {
                $validationRules['fournisseur_id'] = 'required|exists:fournisseurs,id';
                $validationRules['client_id'] = 'nullable|exists:clients,id';
            } else {
                throw new ValidationException('Code de classe de document invalide.');
            }

            $request->validate($validationRules);

            // Génération du numéro
            $dernierDocument = Document::where('codeClasseDoc', $codeClasseDoc)->latest()->first();
            if ($dernierDocument && preg_match('/(\d+)$/', $dernierDocument->numero, $matches)) {
                $numero = str_pad((int)$matches[1] + 1, 5, '0', STR_PAD_LEFT);
            } else {
                $numero = '00001';
            }

            // Vérification du stock pour BC et BL (documents clients)
            if (in_array($codeClasseDoc, ['BC', 'BL'])) {
                foreach ($request->produitsCommandes as $item) {
                    $produit = Produit::findOrFail($item['produit_id']);
                    $stock = $produit->quantitystock ?? 0;      
                    if ($produit->quantitystock < $item['quantite']) {
                        throw new Exception("Stock insuffisant pour le produit ID {$item['produit_id']}");
                    }
                }
            }

            // Création du document
            $documentData = [
                'document_class_id' => $request->document_class_id,
                'libelle' => $request->libelle,
                'etat' => $request->etat,
                'preparateur_id' => $request->preparateur_id,
                'devise' => $request->devise,
                'tauxEchange' => $request->tauxEchange,
                'dateDocument' => Carbon::parse($request->dateDocument),
                'dateLivraison' => $request->dateLivraison ? Carbon::parse($request->dateLivraison) : null,
                'codeClasseDoc' => $codeClasseDoc,
                'numero' => $numero,
            ];

            // Ajouter client_id ou fournisseur_id selon le type de document
            if (in_array($codeClasseDoc, $clientDocuments)) {
                $documentData['client_id'] = $request->client_id;
                $documentData['fournisseur_id'] = null;
            } elseif (in_array($codeClasseDoc, $fournisseurDocuments)) {
                $documentData['fournisseur_id'] = $request->fournisseur_id;
                $documentData['client_id'] = null;
            }

            $document = Document::create($documentData);

            // Enregistrement des lignes
            foreach ($request->produitsCommandes as $item) {
                $produit = Produit::findOrFail($item['produit_id']);

                LigneDocument::create([
                    'document_id' => $document->id,
                    'produit_id' => $produit->id,
                    'code' => $produit->code ?? null,
                    'designation' => $produit->designation ?? $produit->nom,
                    'stock' => $produit->quantitystock ?? 0,
                    'quantite' => $item['quantite'],
                    'puht' => $item['puht'],
                    'tva' => $item['tva'] ?? 0,
                    'ttc' => $item['prixTotal'],
                ]);

                // Gestion du stock
                if (in_array($codeClasseDoc, ['BC', 'BL'])) {
                    // Décrémenter le stock pour les documents clients
                    $produit->decrement('quantitystock', $item['quantite']);
                } elseif ($codeClasseDoc === 'BR') {
                    // Incrémenter le stock pour les bons de réception (fournisseurs)
                    $produit->increment('quantitystock', $item['quantite']);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Document créé avec succès.',
                'numero' => $numero,
                'data' => $document->load(['lignesDocument', in_array($codeClasseDoc, $clientDocuments) ? 'client' : 'fournisseur']),
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
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $document = Document::find($id);
        if (!$document) {
            return response()->json(['message' => 'Document introuvable'], 404);
        }

        $clientDocuments = ['BC', 'BL', 'FC'];
        $fournisseurDocuments = ['BCF', 'BR', 'FF'];

        $validationRules = [
            'document_class_id' => 'required|exists:document_classes,id',
            'libelle' => 'required|in:Bon de commande,Bon de livraison,Facture,Bon de commande fournisseur,Bon de réception,Facture fournisseur',
            'etat' => 'nullable|string',
            'preparateur_id' => 'required|exists:magasiniers,id',
            'devise' => 'required|string',
            'tauxEchange' => 'nullable|numeric',
            'dateDocument' => 'required|date',
            'dateLivraison' => 'nullable|date',
        ];

        // Validation conditionnelle pour client_id ou fournisseur_id
        $codeClasseDoc = $document->codeClasseDoc;
        if (in_array($codeClasseDoc, $clientDocuments)) {
            $validationRules['client_id'] = 'required|exists:clients,id';
            $validationRules['fournisseur_id'] = 'nullable|exists:fournisseurs,id';
        } elseif (in_array($codeClasseDoc, $fournisseurDocuments)) {
            $validationRules['fournisseur_id'] = 'required|exists:fournisseurs,id';
            $validationRules['client_id'] = 'nullable|exists:clients,id';
        }

        $request->validate($validationRules);

        $documentData = $request->all();
        if (in_array($codeClasseDoc, $clientDocuments)) {
            $documentData['client_id'] = $request->client_id;
            $documentData['fournisseur_id'] = null;
        } elseif (in_array($codeClasseDoc, $fournisseurDocuments)) {
            $documentData['fournisseur_id'] = $request->fournisseur_id;
            $documentData['client_id'] = null;
        }

        $document->update($documentData);
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

        return response()->json(str_pad($dernierNumero, 5, '0', STR_PAD_LEFT));
    }

    public function getDocumentByIdAndCode($id, $codeClasseDoc)
    {
        $document = Document::with(in_array($codeClasseDoc, ['BC', 'BL', 'FC']) ? 'client' : 'fournisseur', 'lignesDocument')
            ->where('id', $id)
            ->where('codeClasseDoc', $codeClasseDoc)
            ->first();

        if (!$document) {
            return response()->json(['message' => 'Document introuvable avec ce code.'], 404);
        }

        return response()->json($document);
    }
}