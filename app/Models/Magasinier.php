<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Magasinier extends Model
{
    use HasFactory;

    public $incrementing = false; 
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nom',
        'adresse',
        'numero_telephone',
        'email',
    ];
    public function magasinier()
{
    return $this->belongsTo(Magasinier::class, 'preparateur_id');
}

}
