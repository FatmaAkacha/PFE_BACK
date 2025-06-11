<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Magasinier;


class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_class_id',
        'libelle',
        'codeClasseDoc',
        'num_seq',
        'etat',
        'preparateur_id',
        'client_id',
        'devise',
        'tauxEchange',
        'dateDocument',
        'dateLivraison',
        'numero'
    ];
    

    public function documentClass()
    {
        return $this->belongsTo(DocumentClass::class);
    }
   public function lignesDocument()
{
    return $this->hasMany(LigneDocument::class);
}

    public function preparateur()
    {
        return $this->belongsTo(Magasinier::class, 'preparateur_id');
    }
     public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

}
