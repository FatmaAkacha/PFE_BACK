<?php 
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentClass;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with('documentClass')->get();
        return response()->json($documents);
    }

    public function show($id)
    {
        $document = Document::with('documentClass')->find($id);
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
        $document->document_class_id = $request->input('document_class_id');
        $document->codeClasseDoc = 'BC';
        $document->num_seq = $num_seq;
        $document->libelle = $request->input('libelle');
        $document->save();
        $libelle = $request->input('libelle');
        if (!in_array($libelle, ['Bon de commande', 'Bon de livraison', 'Facture'])) {
            return response()->json(['message' => 'Libellé non autorisé'], 400);
        }

    
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
}
