<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
{
    try {
        $clients = Client::all();
        foreach ($clients as $client) {
            if (!mb_check_encoding($client->logo, 'UTF-8')) {
                $client->logo = utf8_encode($client->logo);
            }
        }                
        return response()->json($clients);
    } catch (\Exception $e) {
        \Log::error('Erreur lors de la récupération des clients : ' . $e->getMessage());
        return response()->json(['message' => 'Erreur interne du serveur'], 500);
    }
}


    public function show($id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        if (!mb_check_encoding($client->logo, 'UTF-8')) {
            $client->logo = utf8_encode($client->logo);
        }

        return response()->json($client);
    }

    public function store(Request $request)
{
    try {
        $validatedData = $request->validate([
            'nom'              => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:clients',
            'adresse'          => 'nullable|string|max:255',
            'numero_telephone' => 'nullable|string|max:15',
            'raison_sociale'   => 'nullable|string|max:255',
            'contact'          => 'nullable|string|max:255',
            'code'             => 'nullable|string|max:50',
            'logo'             => 'nullable|file|image|max:2048',
        ]);

        \Log::info('Validation réussie', ['data' => $validatedData]);

        $data = $validatedData;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('uploads', 'public');
            \Log::info('Fichier téléchargé', ['path' => $path]);
            $data['logo'] = $path;
        }

        $client = Client::create($data);
        return response()->json($client, 201);
    } catch (\Exception $e) {
        \Log::error('Erreur lors de l\'enregistrement du client : ' . $e->getMessage());
        return response()->json(['message' => 'Erreur interne du serveur'], 500);
    }
}


public function update(Request $request, $id)
{
    $client = Client::find($id);

    if (!$client) {
        return response()->json(['error' => 'Client non trouvé'], 404);
    }

    try {
        $validatedData = $request->validate([
            'nom'              => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:clients,email,' . $client->id,
            'adresse'          => 'nullable|string|max:255',
            'numero_telephone' => 'nullable|string|max:15',
            'raison_sociale'   => 'nullable|string|max:255',
            'contact'          => 'nullable|string|max:255',
            'code'             => 'nullable|string|max:50',
            'logo'             => 'nullable|file|image|max:2048',
        ]);

        $data = $validatedData;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('uploads', 'public');
            $data['logo'] = $path;
        }

        $client->update($data);
        return response()->json($client, 200);
    } catch (\Exception $e) {
        \Log::error('Erreur lors de la mise à jour du client : ' . $e->getMessage());
        return response()->json(['message' => 'Erreur interne du serveur'], 500);
    }
}


    public function destroy($id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        // Supprime le fichier logo s'il existe
        $logoPath = storage_path('app/public/' . $client->logo);
        if (file_exists($logoPath)) {
            unlink($logoPath);
        }

        $client->delete();
        return response()->json(['message' => 'Client supprimé avec succès.'], 200);
    }

    public function getImage($id)
    {
        $client = Client::findOrFail($id);
        $path = storage_path('app/public/' . $client->logo);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Image not found.'], 404);
        }

        return response()->file($path);
    }

    public function serveImage($id)
{
    $client = Client::find($id);

    if (!$client || !$client->logo) {
        return response()->json(['message' => 'Image non trouvée'], 404);
    }

    $path = storage_path('app/public/' . $client->logo);

    // Vérifier si le fichier existe avant de le renvoyer
    if (!file_exists($path)) {
        return response()->json(['message' => 'Image non trouvée sur le serveur'], 404);
    }

    return response()->file($path);
}

}
