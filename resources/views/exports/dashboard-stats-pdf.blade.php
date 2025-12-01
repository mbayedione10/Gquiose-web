
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques du Dashboard</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.6;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
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
            width: 25%;
            padding: 15px;
            text-align: center;
            background: #f3f4f6;
            border-radius: 8px;
            margin: 5px;
        }
        .stat-card h3 {
            margin: 0;
            font-size: 32px;
            color: #2563eb;
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
            color: #1e40af;
            border-bottom: 2px solid #2563eb;
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
            background-color: #2563eb;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
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
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Tableau de Bord - Statistiques Globales</h1>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>{{ number_format($totalUtilisateurs) }}</h3>
            <p>Utilisateurs</p>
        </div>
        <div class="stat-card">
            <h3>{{ number_format($totalAlertes) }}</h3>
            <p>Alertes</p>
        </div>
        <div class="stat-card">
            <h3>{{ number_format($totalArticles + $totalVideos) }}</h3>
            <p>Contenus</p>
        </div>
        <div class="stat-card">
            <h3>{{ number_format($totalStructures) }}</h3>
            <p>Structures</p>
        </div>
    </div>

    <div class="section">
        <h2>📈 Métriques Principales</h2>
        <table>
            <thead>
                <tr>
                    <th>Métrique</th>
                    <th>Valeur</th>
                    <th>Détails</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Utilisateurs inscrits</td>
                    <td>{{ number_format($totalUtilisateurs) }}</td>
                    <td><span class="badge badge-success">{{ $utilisateursActifs }} actifs</span></td>
                </tr>
                <tr>
                    <td>Alertes signalées</td>
                    <td>{{ number_format($totalAlertes) }}</td>
                    <td><span class="badge badge-warning">{{ $alertesConfirmees }} confirmées</span></td>
                </tr>
                <tr>
                    <td>Articles publiés</td>
                    <td>{{ number_format($totalArticles) }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Vidéos publiées</td>
                    <td>{{ number_format($totalVideos) }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Structures d'aide</td>
                    <td>{{ number_format($totalStructures) }}</td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>🚨 Alertes Récentes (7 dernières)</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>État</th>
                    <th>Utilisateur</th>
                    <th>Ville</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alertesRecentes as $alerte)
                    <tr>
                        <td>{{ $alerte->type ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $alerte->etat === 'Confirmée' ? 'badge-success' : 'badge-warning' }}">
                                {{ $alerte->etat ?? 'En attente' }}
                            </span>
                        </td>
                        <td>{{ $alerte->utilisateur?->name ?? 'Utilisateur inconnu' }}</td>
                        <td>{{ $alerte->ville?->nom ?? 'Ville inconnue' }}</td>
                        <td>{{ $alerte->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">Aucune alerte récente</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>📊 Distribution des Alertes par Type</h2>
        <table>
            <thead>
                <tr>
                    <th>Type de Violence</th>
                    <th>Nombre</th>
                    <th>Pourcentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalAlertesType = $alertesParType->sum('total');
                @endphp
                @forelse($alertesParType as $type)
                    @php
                        $percentage = $totalAlertesType > 0 ? round(($type->total / $totalAlertesType * 100), 1) : 0;
                    @endphp
                    <tr>
                        <td>{{ $type->type_name }}</td>
                        <td><strong>{{ number_format($type->total) }}</strong></td>
                        <td>{{ $percentage }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">Aucune donnée disponible</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
