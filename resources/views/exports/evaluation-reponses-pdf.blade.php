
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réponses des Évaluations</title>
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
            border-bottom: 3px solid #3b82f6;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0;
        }
        .evaluation-card {
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            background: #f9fafb;
            page-break-inside: avoid;
        }
        .evaluation-header {
            background: #3b82f6;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .evaluation-header h3 {
            margin: 0;
            font-size: 16px;
        }
        .evaluation-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 30%;
            padding: 5px 10px 5px 0;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
        }
        .reponses-section {
            margin-top: 15px;
        }
        .reponses-section h4 {
            color: #1e40af;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #3b82f6;
            padding-bottom: 5px;
        }
        .reponse-item {
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border-left: 3px solid #3b82f6;
            border-radius: 3px;
        }
        .question {
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .reponse {
            color: #374151;
            margin-left: 10px;
        }
        .note {
            color: #059669;
            font-weight: bold;
            margin-left: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Réponses des Évaluations</h1>
        <p>Total: {{ $evaluations->count() }} évaluation(s)</p>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    @foreach($evaluations as $evaluation)
        <div class="evaluation-card">
            <div class="evaluation-header">
                <h3>Évaluation #{{ $evaluation->id }}</h3>
            </div>

            <div class="evaluation-info">
                <div class="info-row">
                    <div class="info-label">Utilisateur:</div>
                    <div class="info-value">
                        {{ $evaluation->utilisateur ? $evaluation->utilisateur->nom . ' ' . $evaluation->utilisateur->prenom : 'N/A' }}
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Contexte:</div>
                    <div class="info-value">
                        <span class="badge badge-blue">{{ ucfirst($evaluation->contexte) }}</span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Score Global:</div>
                    <div class="info-value">
                        {{ $evaluation->score_global ? number_format($evaluation->score_global, 2) . '/5' : 'N/A' }}
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Date:</div>
                    <div class="info-value">{{ $evaluation->created_at->format('d/m/Y à H:i') }}</div>
                </div>
                @if($evaluation->commentaire)
                    <div class="info-row">
                        <div class="info-label">Commentaire:</div>
                        <div class="info-value">{{ $evaluation->commentaire }}</div>
                    </div>
                @endif
            </div>

            @if($evaluation->reponsesDetails->count() > 0)
                <div class="reponses-section">
                    <h4>Réponses Détaillées</h4>
                    @foreach($evaluation->reponsesDetails as $reponse)
                        <div class="reponse-item">
                            <div class="question">{{ $reponse->questionEvaluation->question }}</div>
                            <div class="reponse">
                                Réponse: {{ $reponse->reponse }}
                                @if($reponse->valeur_numerique)
                                    <span class="note">({{ $reponse->valeur_numerique }}/5)</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        <p>Document généré automatiquement par le système GquiOse</p>
    </div>
</body>
</html>
