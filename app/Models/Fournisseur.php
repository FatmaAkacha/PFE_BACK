<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'matricule_fiscal'];
}
