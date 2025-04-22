<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de commande</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Bon de commande #{{ $document->id }}</h2>
    <p><strong>Client ID:</strong> {{ $document->client_id }}</p>
    <p><strong>Date:</strong> {{ $document->dateDocument }}</p>
    <p><strong>Préparateur:</strong> {{ $document->preparateur }}</p>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Quantité</th>
                <th>PUHT</th>
                <th>TVA %</th>
                <th>TTC</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($document->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->code }}</td>
                    <td>{{ $ligne->designation }}</td>
                    <td>{{ $ligne->quantite }}</td>
                    <td>{{ number_format($ligne->puht, 2) }}</td>
                    <td>{{ $ligne->tva }}</td>
                    <td>{{ number_format($ligne->ttc, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total TTC:</strong> 
        {{ number_format($document->lignes->sum('ttc'), 2) }} {{ $document->devise }}
    </p>
</body>
</html>