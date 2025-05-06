<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Fournisseur extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'matricule_fiscal','logo'];

    public $incrementing = false; 
    protected $keyType = 'string'; 
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
}