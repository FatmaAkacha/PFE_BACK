<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Magasinier;
use App\Models\Fournisseur;


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
        'numero',
        'fournisseur_id'
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
         public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

}
