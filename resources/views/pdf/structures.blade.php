<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Structures Sanitaires</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { color: #333; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f0f0f0; padding: 10px; border: 1px solid #ddd; font-weight: bold; }
        td { padding: 8px; border: 1px solid #ddd; }
        .header { text-align: center; margin-bottom: 30px; }
        .date { text-align: right; color: #666; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="date">Généré le {{ date('d/m/Y à H:i') }}</div>
    <div class="header">
        <h1>Liste des Structures Sanitaires</h1>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Type</th>
                <th>Ville</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($structures as $structure)
            <tr>
                <td>{{ $structure->name }}</td>
                <td>{{ $structure->typeStructure?->name ?? 'N/A' }}</td>
                <td>{{ $structure->ville?->name ?? 'N/A' }}</td>
                <td>{{ $structure->adresse }}</td>
                <td>{{ $structure->phone }}</td>
                <td>{{ $structure->status ? 'Actif' : 'Inactif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 30px; text-align: center; color: #666;">
        Total: {{ count($structures) }} structures
    </div>
</body>
</html>
