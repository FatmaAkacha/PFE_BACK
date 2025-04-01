<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'nom',
        'adresse',
        'numero_telephone',
        'logo',
        'email','code' , 'raison_sociale', 'contact'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'client_produit')
                    ->withPivot('quantite', 'date_achat')
                    ->withTimestamps();
    }


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
}
