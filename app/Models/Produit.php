<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'prix',
        'quantitystock',
        'seuil',
        'image_data',
        'categorie_id',
        'inventoryStatus',
        'rating',
    ];

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_produit')
                    ->withPivot('quantite', 'date_achat')
                    ->withTimestamps();
    }
    public function categorie()
{
    return $this->belongsTo(Categorie::class,'categorie_id');
}

}
