<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'totalHT', 'tva', 'totalTTC', 'date','etat','preparateur','dateDocument'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function devisProduits()
    {
        return $this->hasMany(DevisProduit::class);
    }
}
