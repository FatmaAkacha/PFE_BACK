<?php 
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentClass;
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
        $document = Document::with('lignes.produit')->findOrFail($id);
        if ($document) {
            return response()->json($document);
        }
        return response()->json(['message' => 'Document introuvable'], 404);
    }

    public function store(Request $request)
    {
        $lastDocument = Document::where('codeClasseDoc', 'BC')
            ->orderBy('id', 'desc')
            ->first();
    
        $num_seq = $lastDocument->num_seq + 1 ;
    
        $document = new Document();
        $libelle = $request->input('libelle');
        if (!in_array($libelle, ['Bon de commande', 'Bon de livraison', 'Facture'])) {
            return response()->json(['message' => 'Libellé non autorisé'], 400);
        }
        
        $document = Document::create([
            'document_class_id' => $request->input('document_class_id'),
            'codeclassedocument' => 'BC',
            'libelle' => $libelle,
            'num_seq' => $num_seq,
            'etat' => $request->input('etat'),
            'preparateur' => $request->input('preparateur'),
            'client_id' => $request->input('client_id'),
            'devise' => $request->input('devise'),
            'tauxEchange' => $request->input('tauxEchange'),
            'dateDocument' => $request->input('dateDocument'),
            'dateLivraison' => $request->input('dateLivraison'),
        ]);
        return response()->json($document, 201);
    }
    

    public function update(Request $request, $id)
    {
        $document = Document::find($id);
        if (!$document) {
            return response()->json(['message' => 'Document introuvable'], 404);
        }

        $request->validate([
            'document_class_id' => 'required|exists:document_classes,id',
            'codeclassedocument' => 'required|string',
            'libelle' => 'required|string',
            'code' => 'required|string',
        ]);
        $libelle = $request->input('libelle');
        if (!in_array($libelle, ['Bon de commande', 'Bon de livraison', 'Facture'])) {
            return response()->json(['message' => 'Libellé non autorisé'], 400);
        }



        $document->update($request->only('document_class_id', 'codeclassedocument', 'libelle', 'code'));
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
            'lignes.*.code' => 'nullable|string',
            'lignes.*.designation' => 'required|string',
            'lignes.*.stock' => 'required|integer',
            'lignes.*.quantite' => 'required|integer',
            'lignes.*.puht' => 'required|numeric',
            'lignes.*.tva' => 'required|numeric',
            'lignes.*.ttc' => 'required|numeric',
        ]);
        dd($validated);
    
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
            dd($ligneData);
            // Valider ou modifier les données de ligne avant de les insérer
            if (is_array($ligneData['produit_id'])) {
                return response()->json(['message' => 'Le produit_id ne doit pas être un tableau'], 400);
            }
    
            // Enregistre la ligne
            \App\Models\LigneDocument::create($ligneData);
    
            // Met à jour le stock du produit
            $produit = \App\Models\Produit::find($ligneData['produit_id']);
            if ($produit) {
                $produit->quantitystock -= $ligneData['quantite'];
                $produit->save();
            }
        }
    
        return response()->json($document->load('lignes'), 201);
    }

    public function printBonCommande($id)
    {
    $document = Document::findOrFail($id);

    $pdf = PDF::loadView('documents.print', compact('document'));
    return $pdf->stream('document.pdf');
    }
}
