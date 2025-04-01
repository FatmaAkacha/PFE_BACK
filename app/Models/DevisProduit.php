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
}
