<?php
namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\DevisProduit;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class DevisController extends Controller
{
    public function index()
    {
        return response()->json(Devis::with('client', 'devisProduits.produit')->get());
    }

    public function show($id)
    {
        $devis = Devis::with('client', 'devisProduits.produit')->find($id);
        return $devis ? response()->json($devis) : response()->json(['message' => 'Devis introuvable'], 404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'totalHT' => 'required|numeric',
            'tva' => 'required|numeric',
            'totalTTC' => 'required|numeric',
            'date' => 'required|date',
            'produits' => 'required|array',
        ]);

        $devis = Devis::create($request->only(['client_id', 'totalHT', 'tva', 'totalTTC', 'date']));

        foreach ($request->produits as $produit) {
            DevisProduit::create([
                'devis_id' => $devis->id,
                'produit_id' => $produit['produit']['id'],
                'quantite' => $produit['quantite'],
                'prixTotal' => $produit['prixTotal'],
            ]);
        }

        return response()->json($devis->load('devisProduits.produit'), 201);
    }

    public function update(Request $request, $id)
    {
        $devis = Devis::find($id);
        if (!$devis) {
            return response()->json(['message' => 'Devis introuvable'], 404);
        }

        $request->validate([
            'totalHT' => 'required|numeric',
            'tva' => 'required|numeric',
            'totalTTC' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $devis->update($request->only(['totalHT', 'tva', 'totalTTC', 'date']));
        
        return response()->json($devis->load('devisProduits.produit'));
    }

    public function destroy($id)
    {
        $devis = Devis::find($id);
        if (!$devis) {
            return response()->json(['message' => 'Devis introuvable'], 404);
        }

        $devis->delete();
        return response()->json(['message' => 'Devis supprimé']);
    }
    public function downloadPDF($id)
{
    $devis = Devis::with('client', 'devisProduits.produit')->find($id);

    if (!$devis) {
        return response()->json(['message' => 'Devis introuvable'], 404);
    }

    // Générer le PDF à partir d'une vue Blade
    $pdf = Pdf::loadView('pdf.devis', ['devis' => $devis]);

    // Télécharger directement
    return $pdf->download("bon_de_commande_{$devis->id}.pdf");
}
    
}
