<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'prix',
        'prix_achat',
        'prix_vente_ht',
        'prix_vente_ttc',
        'remise_maximale',
        'quantitystock',
        'quantite',
        'seuil',
        'image_data',
        'categorie_id',
        'fournisseur_id',
        'inventoryStatus',
        'rating',
    ];
    

    /**
     * Relation avec les clients (via la table pivot client_produit)
     */
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_produit')
                    ->withPivot('quantite', 'date_achat')  // Détails de la relation
                    ->withTimestamps();
    }

    /**
     * Relation avec la catégorie de produit
     */
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');  // Lien vers la table Categorie
    }

    /**
     * Relation avec les lignes de document (les produits dans les documents)
     */
    public function ligneDocuments()
    {
        return $this->hasMany(LigneDocument::class);  // Un produit peut apparaître dans plusieurs lignes de documents
    }

    /**
     * Méthode pour mettre à jour le stock du produit.
     *
     * @param int $quantite
     * @return void
     */
    public function updateStock(int $quantite)
    {
        // Vérifier si la quantité est valide avant de l'appliquer
        if ($this->quantitystock - $quantite >= 0) {
            $this->quantitystock -= $quantite;  // Réduire le stock
            $this->save();  // Enregistrer la modification dans la base de données
        } else {
            throw new \Exception("Stock insuffisant pour effectuer cette opération.");
        }
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

}