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
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LigneDocumentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MagasinierController;


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
// 'keycloak.role:admin'
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/protected-endpoint', [AuthController::class, 'index']);
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
    Route::get('/fournisseurs/{id}/logo', [FournisseurController::class, 'getImage']);
    Route::get('/fournisseurs/{id}/serve-logo', [FournisseurController::class, 'serveImage']);
    // Routes ProduitController

    Route::get('/produits', [ProduitController::class, 'index']); // Afficher tous les produits
    Route::get('/produits/{id}', [ProduitController::class, 'show']); // Afficher un produit spécifique
    Route::post('/produits', [ProduitController::class, 'store']); // Créer un nouveau produit
    Route::put('/produits/{id}', [ProduitController::class, 'update']); // Mettre à jour un produit
    Route::delete('/produits/{id}', [ProduitController::class, 'destroy']); // Supprimer un produit
    Route::get('/produits/{id}/image', [ProduitController::class, 'getImage']);
    Route::get('/produits/{id}/image', [ProduitController::class, 'serveImage']);
    Route::post('/produits/fournisseur/{fournisseur_id}', [ProduitController::class, 'storePourFournisseur']);

    
    Route::get('/devis', [DevisController::class, 'index']);
    Route::get('/devis/{id}', [DevisController::class, 'show']);
    Route::post('/devis', [DevisController::class, 'store']);
    Route::put('/devis/{id}', [DevisController::class, 'update']);
    Route::delete('/devis/{id}', [DevisController::class, 'destroy']);

    Route::apiResource('/documents', DocumentController::class);
    Route::apiResource('/document-classes', DocumentClassController::class);
    Route::post('documents-with-lignes', [DocumentController::class, 'storeWithLignes']);
    Route::get('/documents/dernier-code/{classId}', [DocumentController::class, 'getDernierCode']);
    Route::get('/documents/{id}/{codeClasseDoc}', [DocumentController::class, 'getDocumentByIdAndCode']);



    Route::get('/devis/{id}/download-pdf', [DevisController::class, 'downloadPDF']);

    // Routes Catégories
    Route::apiResource('/categories', CategoryController::class);

    Route::get('/lignes', [LigneDocumentController::class, 'index']);
    Route::get('/lignes/document/{documentId}', [LigneDocumentController::class, 'getByDocument']);
    Route::get('/lignes/{id}', [LigneDocumentController::class, 'show']);
    Route::post('/lignes', [LigneDocumentController::class, 'store']);
    Route::put('/lignes/{id}', [LigneDocumentController::class, 'update']);
    Route::delete('/lignes/{id}', [LigneDocumentController::class, 'destroy']);
    Route::get('documents/{documentId}/lignes', [LigneDocumentController::class, 'getByDocument']);
    Route::post('/lignes/batch', [LigneDocumentController::class, 'storeBatch']);


    Route::post('/sendemail', [ContactController::class, 'send']);

    Route::apiResource('/magasinier', MagasinierController::class);


});

Route::get('/documents/{id}/print', [DocumentController::class, 'printBonCommande']);

Route::apiResource('users', UserController::class);
Route::post('/users/by-email', [UserController::class, 'getUserByEmail']);
Route::get('/users/{id}/roles', [UserController::class, 'getRolesByUserId']);

Route::apiResource('roles', RoleController::class);




