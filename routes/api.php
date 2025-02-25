<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ProduitController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ces routes sont chargées par RouteServiceProvider et seront préfixées par "/api".
| Profitez du développement de votre API !
|
*/

// Routes protégées nécessitant une authentification
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/protected-endpoint', 'SecretController@index');

    // Routes Clients
    Route::get('/clients', [ClientController::class, 'getClients']);
    Route::get('/clients/{id}', [ClientController::class, 'getClientById']);
    Route::post('/clients', [ClientController::class, 'insertClient']);
    Route::put('/clients/{id}', [ClientController::class, 'updateClient']);
    Route::delete('/clients/{id}', [ClientController::class, 'deleteClient']);

    // Routes Fournisseurs
    Route::get('/fournisseurs', [FournisseurController::class, 'index']);
    Route::get('/fournisseurs/{id}', [FournisseurController::class, 'show']);
    Route::post('/fournisseurs', [FournisseurController::class, 'store']);
    Route::put('/fournisseurs/{id}', [FournisseurController::class, 'update']);
    Route::delete('/fournisseurs/{id}', [FournisseurController::class, 'destroy']);

    // Routes Produits

    Route::get('/produits', [ProduitController::class, 'index']); // Afficher tous les produits
    Route::get('/produits/{id}', [ProduitController::class, 'show']); // Afficher un produit spécifique
    Route::post('/produits', [ProduitController::class, 'store']); // Créer un nouveau produit
    Route::put('/produits/{id}', [ProduitController::class, 'update']); // Mettre à jour un produit
    Route::delete('/produits/{id}', [ProduitController::class, 'destroy']); // Supprimer un produit
});
