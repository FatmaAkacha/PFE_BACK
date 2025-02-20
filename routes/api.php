<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// protected endpoints
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/protected-endpoint', 'SecretController@index');




Route::get('/clients', [ClientController::class, 'getClients']);
Route::get('/clients/{id}', [ClientController::class, 'getClientById']);
Route::post('/clients', [ClientController::class, 'insertClient']);
Route::put('/clients/{id}', [ClientController::class, 'updateClient']);
Route::delete('/clients/{id}', [ClientController::class, 'deleteClient']);
});
