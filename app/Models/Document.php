<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_class_id',
        'libelle',
        'codeClasseDoc',
        'num_seq',
        'etat',
        'preparateur',
        'client_id',
        'devise',
        'tauxEchange',
        'dateDocument',
        'dateLivraison',
    ];
    

    public function documentClass()
    {
        return $this->belongsTo(DocumentClass::class);
    }
    public function lignes()
    {
        return $this->hasMany(LigneDocument::class);
    }

}
