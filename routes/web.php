<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::middleware(['keycloak'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
        
    });
// Routes pour afficher les clients (interface web)
Route::get('/clients', [ClientController::class, 'index'])->name('Clients.index');
Route::get('/clients/{id}', [ClientController::class, 'show'])->name('Clients.show');
Route::post('/clients', [ClientController::class, 'store'])->name('Clients.store');
Route::put('/clients/{id}', [ClientController::class, 'update'])->name('Clients.update');
Route::delete('/clients/{id}', [ClientController::class, 'deleteClient'])->name('Clients.delete');
});