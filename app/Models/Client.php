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
        'email'
    ];

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        // Générer un UUID lors de la création d'un client
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
}
