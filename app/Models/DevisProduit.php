<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevisProduit extends Model
{
    use HasFactory;

    protected $fillable = ['devis_id', 'produit_id', 'quantite', 'prixTotal'];

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
    public function download($id)
    {
        $devis = Devis::with('client', 'devisProduits.produit')->find($id);

        if (!$devis) {
            return response()->json(['message' => 'Devis introuvable'], 404);
        }

        $pdf = Pdf::loadView('devis.pdf', ['devis' => $devis]);

        return $pdf->download('devis_'.$devis->id.'.pdf');
    }

}
