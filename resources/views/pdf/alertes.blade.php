<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Alertes</title>
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
        <h1>Liste des Alertes</h1>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Type</th>
                <th>Description</th>
                <th>État</th>
                <th>Ville</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alertes as $alerte)
            <tr>
                <td>{{ $alerte->ref }}</td>
                <td>{{ $alerte->typeAlerte?->name ?? 'N/A' }}</td>
                <td>{{ Str::limit($alerte->description, 50) }}</td>
                <td>{{ $alerte->etat }}</td>
                <td>{{ $alerte->ville?->name ?? 'N/A' }}</td>
                <td>{{ $alerte->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 30px; text-align: center; color: #666;">
        Total: {{ count($alertes) }} alertes
    </div>
</body>
</html>
