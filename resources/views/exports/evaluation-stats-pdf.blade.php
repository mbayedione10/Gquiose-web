<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Évaluations</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .stat-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            background: #f3f4f6;
            border-radius: 8px;
            margin: 5px;
        }
        .stat-card h3 {
            margin: 0;
            font-size: 32px;
            color: #3b82f6;
        }
        .stat-card p {
            margin: 5px 0 0 0;
            color: #6b7280;
            font-size: 14px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #1e40af;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #3b82f6;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport Statistiques des Évaluations</h1>
        <p>Période: {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</p>
        @if($contexte !== 'all')
            <p>Filtre: {{ ucfirst($contexte) }}</p>
        @endif
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>{{ $stats['total'] }}</h3>
            <p>Total Évaluations</p>
        </div>
        <div class="stat-card">
            <h3>{{ $stats['score_moyen'] }}/5</h3>
            <p>Score Moyen</p>
        </div>
        <div class="stat-card">
            <h3>{{ count($stats['par_contexte']) }}</h3>
            <p>Types de Formulaires</p>
        </div>
    </div>

    <div class="section">
        <h2>Répartition par Type de Formulaire</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Nombre</th>
                    <th>Pourcentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['par_contexte'] as $type => $count)
                    <tr>
                        <td>{{ ucfirst($type) }}</td>
                        <td>{{ $count }}</td>
                        <td>{{ $stats['total'] > 0 ? round(($count / $stats['total']) * 100, 1) : 0 }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Détail des Évaluations</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Score</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluations->take(50) as $evaluation)
                    <tr>
                        <td>#{{ $evaluation->id }}</td>
                        <td><span class="badge badge-blue">{{ ucfirst($evaluation->contexte) }}</span></td>
                        <td>{{ $evaluation->score_global }}/5</td>
                        <td>{{ $evaluation->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($evaluations->count() > 50)
            <p style="text-align: center; color: #6b7280; margin-top: 10px;">
                Affichage des 50 premières évaluations sur {{ $evaluations->count() }} au total
            </p>
        @endif
    </div>

    <div class="footer">
        <p>Document généré automatiquement par le système GquiOse</p>
    </div>
</body>
</html>
