<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentClassController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SecretController;
use App\Http\Controllers\LigneDocumentController;


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
    Route::get('/protected-endpoint', [SecretController::class, 'index']);
    // Routes Clients
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/{id}', [ClientController::class, 'show']);
    Route::post('/clients', [ClientController::class, 'store']);
    Route::put('/clients/{id}', [ClientController::class, 'update']);
    Route::delete('/clients/{id}', [ClientController::class, 'destroy']);

    Route::get('/clients/{id}/logo', [ClientController::class, 'getImage']);
    Route::get('/clients/{id}/serve-logo', [ClientController::class, 'serveImage']);

    // Routes Fournisseurs
    Route::get('/fournisseurs', [FournisseurController::class, 'index']);
    Route::get('/fournisseurs/{id}', [FournisseurController::class, 'show']);
    Route::post('/fournisseurs', [FournisseurController::class, 'store']);
    Route::put('/fournisseurs/{id}', [FournisseurController::class, 'update']);
    Route::delete('/fournisseurs/{id}', [FournisseurController::class, 'destroy']);

    // Routes ProduitController

    Route::get('/produits', [ProduitController::class, 'index']); // Afficher tous les produits
    Route::get('/produits/{id}', [ProduitController::class, 'show']); // Afficher un produit spécifique
    Route::post('/produits', [ProduitController::class, 'store']); // Créer un nouveau produit
    Route::put('/produits/{id}', [ProduitController::class, 'update']); // Mettre à jour un produit
    Route::delete('/produits/{id}', [ProduitController::class, 'destroy']); // Supprimer un produit
    Route::get('/produits/{id}/image', [ProduitController::class, 'getImage']);
    Route::get('/produits/{id}/image', [ProduitController::class, 'serveImage']);

    
    Route::get('/devis', [DevisController::class, 'index']);
    Route::get('/devis/{id}', [DevisController::class, 'show']);
    Route::post('/devis', [DevisController::class, 'store']);
    Route::put('/devis/{id}', [DevisController::class, 'update']);
    Route::delete('/devis/{id}', [DevisController::class, 'destroy']);

    Route::apiResource('/documents', DocumentController::class);
    Route::apiResource('/document-classes', DocumentClassController::class);
    Route::post('documents-with-lignes', [DocumentController::class, 'storeWithLignes']);


    Route::get('/devis/{id}/download-pdf', [DevisController::class, 'downloadPDF']);

    // Routes Catégories
    Route::get('/categories', [CategoryController::class, 'getCategories']);
    Route::get('/categories/{id}', [CategoryController::class, 'getCategoryById']);
    Route::post('/categories', [CategoryController::class, 'insertCategory']);
    Route::put('/categories/{id}', [CategoryController::class, 'updateCategory']);
    Route::delete('/categories/{id}', [CategoryController::class, 'deleteCategory']);

    Route::get('/lignes', [LigneDocumentController::class, 'index']);
    Route::get('/lignes/document/{documentId}', [LigneDocumentController::class, 'getByDocument']);
    Route::get('/lignes/{id}', [LigneDocumentController::class, 'show']);
    Route::post('/lignes', [LigneDocumentController::class, 'store']);
    Route::put('/lignes/{id}', [LigneDocumentController::class, 'update']);
    Route::delete('/lignes/{id}', [LigneDocumentController::class, 'destroy']);
    Route::get('documents/{documentId}/lignes', [LigneDocumentController::class, 'getByDocument']);
    Route::post('/lignes/batch', [LigneDocumentController::class, 'storeBatch']);





});

Route::get('/documents/{id}/print', [DocumentController::class, 'printBonCommande']);

