<!DOCTYPE html>
<html>
<head>
    <title>Bon de Commande</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
    </style>
</head>
<body>
    <h1>Bon de commande N°{{ $devis->id }}</h1>
    <p>Client : {{ $devis->client->nom }}</p>
    <p>Date : {{ $devis->date }}</p>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($devis->devisProduits as $dp)
                <tr>
                    <td>{{ $dp->produit->nom }}</td>
                    <td>{{ $dp->quantite }}</td>
                    <td>{{ $dp->produit->prix }} DT</td>
                    <td>{{ $dp->prixTotal }} DT</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Total HT : {{ $devis->totalHT }} DT</p>
    <p>TVA : {{ $devis->tva }} %</p>
    <p>Total TTC : {{ $devis->totalTTC }} DT</p>
</body>
</html>