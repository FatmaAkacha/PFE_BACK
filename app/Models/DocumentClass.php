<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentClass extends Model
{
    use HasFactory;

    protected $fillable = ['libelle', 'code', 'isvent', 'isachat', 'actif'];

    // Une classe de document peut avoir plusieurs documents
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
