
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
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4f46e5;
        }
        .header h1 {
            color: #4338ca;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0;
            font-size: 12px;
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
            color: #4f46e5;
        }
        .stat-card p {
            margin: 5px 0 0 0;
            color: #6b7280;
            font-size: 12px;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section h2 {
            color: #4338ca;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #4f46e5;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-purple {
            background-color: #e9d5ff;
            color: #6b21a8;
        }
        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-green {
            background-color: #d1fae5;
            color: #065f46;
        }
        .chart-placeholder {
            background: #f3f4f6;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin: 15px 0;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Rapport Statistiques des Évaluations</h1>
        <p><strong>Période:</strong> {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</p>
        @if($contexte !== 'all')
            <p><strong>Filtre:</strong> <span class="badge badge-purple">{{ ucfirst($contexte) }}</span></p>
        @endif
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>{{ number_format($stats['total']) }}</h3>
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
        <h2>📈 Répartition par Type de Formulaire</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Nombre</th>
                    <th>Pourcentage</th>
                    <th>Score Moyen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['par_contexte'] as $type => $count)
                    @php
                        $percentage = $stats['total'] > 0 ? round(($count / $stats['total']) * 100, 1) : 0;
                        $scoreMoyen = $evaluations->where('contexte', $type)->avg('score_global');
                    @endphp
                    <tr>
                        <td><span class="badge badge-blue">{{ ucfirst($type) }}</span></td>
                        <td><strong>{{ number_format($count) }}</strong></td>
                        <td>{{ $percentage }}%</td>
                        <td>{{ $scoreMoyen ? number_format($scoreMoyen, 2) . '/5' : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(isset($evolution) && $evolution->count() > 0)
    <div class="section">
        <h2>📊 Évolution Temporelle</h2>
        <div class="chart-placeholder">
            📉 Graphique d'évolution (voir version interactive)
        </div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Nombre</th>
                    <th>Score Moyen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evolution->take(10) as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                        <td>{{ $item->total }}</td>
                        <td>{{ $item->avg_score ? number_format($item->avg_score, 2) . '/5' : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($evolution->count() > 10)
            <p style="text-align: center; color: #6b7280; margin-top: 10px;">
                Affichage des 10 premiers jours sur {{ $evolution->count() }} au total
            </p>
        @endif
    </div>
    @endif

    <div class="section">
        <h2>📝 Détail des Évaluations</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Type</th>
                    <th>Score</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluations->take(50) as $evaluation)
                    <tr>
                        <td>#{{ $evaluation->id }}</td>
                        <td>{{ $evaluation->utilisateur?->name ?? 'N/A' }}</td>
                        <td><span class="badge badge-blue">{{ ucfirst($evaluation->contexte) }}</span></td>
                        <td><span class="badge badge-green">{{ $evaluation->score_global ?? 'N/A' }}/5</span></td>
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
        <p>© {{ now()->year }} - Tous droits réservés</p>
        <p style="text-align: center; margin-top: 20px;">
            <a href="https://mbayedione.xyz/" target="_blank">#NioulBoy</a>
        </p>
    </div>
</body>
</html>
