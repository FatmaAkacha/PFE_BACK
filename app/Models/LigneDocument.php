<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'produit_id',
        'code',
        'designation',
        'stock',
        'quantite',
        'puht',
        'tva',
        'ttc',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
