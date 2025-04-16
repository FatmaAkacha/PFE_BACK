<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['document_class_id', 'codeclassedocument', 'libelle', 'code', 'num_seq'];


    public function documentClass()
    {
        return $this->belongsTo(DocumentClass::class);
    }
}
