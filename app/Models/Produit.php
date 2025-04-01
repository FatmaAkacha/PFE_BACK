<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $fillable = ['nom','description','prix','quantitystock','seuil'];
    
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_produit')
                    ->withPivot('quantite', 'date_achat')
                    ->withTimestamps();
    }
}
