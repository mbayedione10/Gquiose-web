
<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\QuestionEvaluation;
use App\Models\ReponseEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class APIEvaluationStatsController extends Controller
{
    /**
     * Statistiques globales par type de formulaire
     */
    public function globalStats(Request $request)
    {
        $formulaireType = $request->input('formulaire_type');
        
        $stats = [
            'total_evaluations' => Evaluation::when($formulaireType, function($q) use ($formulaireType) {
                return $q->whereHas('reponsesDetails.questionEvaluation', function($sq) use ($formulaireType) {
                    $sq->where('formulaire_type', $formulaireType);
                });
            })->count(),
            
            'score_moyen_global' => Evaluation::when($formulaireType, function($q) use ($formulaireType) {
                return $q->whereHas('reponsesDetails.questionEvaluation', function($sq) use ($formulaireType) {
                    $sq->where('formulaire_type', $formulaireType);
                });
            })->avg('score_global'),
            
            'evolution_mensuelle' => $this->getMonthlyEvolution($formulaireType),
            'repartition_par_contexte' => $this->getContextDistribution($formulaireType),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Graphiques automatiques par question
     */
    public function questionStats($questionId)
    {
        $question = QuestionEvaluation::findOrFail($questionId);
        
        $reponses = ReponseEvaluation::where('question_evaluation_id', $questionId)
            ->with('evaluation')
            ->get();

        $stats = [
            'question' => [
                'id' => $question->id,
                'question' => $question->question,
                'type' => $question->type,
                'formulaire_type' => $question->formulaire_type,
            ],
            'total_reponses' => $reponses->count(),
            'graphique_data' => $this->generateChartData($question, $reponses),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Graphiques pour toutes les questions d'un type de formulaire
     */
    public function formulaireStats($formulaireType)
    {
        $questions = QuestionEvaluation::where('formulaire_type', $formulaireType)
            ->where('status', true)
            ->orderBy('ordre')
            ->get();

        $stats = $questions->map(function($question) {
            $reponses = ReponseEvaluation::where('question_evaluation_id', $question->id)->get();
            
            return [
                'question_id' => $question->id,
                'question' => $question->question,
                'type' => $question->type,
                'total_reponses' => $reponses->count(),
                'graphique' => $this->generateChartData($question, $reponses),
            ];
        });

        return response()->json([
            'success' => true,
            'formulaire_type' => $formulaireType,
            'data' => $stats,
        ]);
    }

    /**
     * Rapport détaillé avec tous les graphiques
     */
    public function detailedReport(Request $request)
    {
        $contexte = $request->input('contexte');
        $contexteId = $request->input('contexte_id');
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');

        $evaluationsQuery = Evaluation::query();

        if ($contexte) {
            $evaluationsQuery->where('contexte', $contexte);
        }
        if ($contexteId) {
            $evaluationsQuery->where('contexte_id', $contexteId);
        }
        if ($dateDebut) {
            $evaluationsQuery->where('created_at', '>=', $dateDebut);
        }
        if ($dateFin) {
            $evaluationsQuery->where('created_at', '<=', $dateFin);
        }

        $evaluations = $evaluationsQuery->with('reponsesDetails.questionEvaluation')->get();

        // Regrouper par question
        $questionsStats = [];
        foreach ($evaluations as $evaluation) {
            foreach ($evaluation->reponsesDetails as $reponse) {
                $questionId = $reponse->question_evaluation_id;
                
                if (!isset($questionsStats[$questionId])) {
                    $questionsStats[$questionId] = [
                        'question' => $reponse->questionEvaluation,
                        'reponses' => [],
                    ];
                }
                
                $questionsStats[$questionId]['reponses'][] = $reponse;
            }
        }

        $report = [
            'periode' => [
                'debut' => $dateDebut ?? $evaluations->min('created_at'),
                'fin' => $dateFin ?? $evaluations->max('created_at'),
            ],
            'total_evaluations' => $evaluations->count(),
            'score_moyen' => $evaluations->avg('score_global'),
            'questions' => collect($questionsStats)->map(function($data, $questionId) {
                return [
                    'question_id' => $questionId,
                    'question' => $data['question']->question,
                    'type' => $data['question']->type,
                    'total_reponses' => count($data['reponses']),
                    'graphique' => $this->generateChartData($data['question'], collect($data['reponses'])),
                ];
            })->values(),
        ];

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    private function generateChartData($question, $reponses)
    {
        switch ($question->type) {
            case 'rating':
            case 'scale':
                return $this->generateNumericChart($reponses);
                
            case 'yesno':
                return $this->generateBinaryChart($reponses);
                
            case 'multiple_choice':
                return $this->generateMultipleChoiceChart($reponses, $question->options);
                
            case 'text':
                return $this->generateTextAnalysis($reponses);
                
            default:
                return null;
        }
    }

    private function generateNumericChart($reponses)
    {
        $distribution = $reponses->groupBy('valeur_numerique')
            ->map->count()
            ->sortKeys();

        $moyenne = $reponses->avg('valeur_numerique');

        return [
            'type' => 'bar',
            'labels' => $distribution->keys()->toArray(),
            'values' => $distribution->values()->toArray(),
            'moyenne' => round($moyenne, 2),
            'total' => $reponses->count(),
        ];
    }

    private function generateBinaryChart($reponses)
    {
        $distribution = $reponses->groupBy('reponse')->map->count();

        return [
            'type' => 'pie',
            'labels' => $distribution->keys()->toArray(),
            'values' => $distribution->values()->toArray(),
            'percentages' => $distribution->map(function($count) use ($reponses) {
                return round(($count / $reponses->count()) * 100, 1);
            })->toArray(),
        ];
    }

    private function generateMultipleChoiceChart($reponses, $options)
    {
        $distribution = $reponses->groupBy('reponse')->map->count();

        return [
            'type' => 'bar',
            'labels' => $distribution->keys()->toArray(),
            'values' => $distribution->values()->toArray(),
            'total' => $reponses->count(),
        ];
    }

    private function generateTextAnalysis($reponses)
    {
        $totalReponses = $reponses->count();
        $moyenneLongueur = $reponses->avg(function($r) {
            return strlen($r->reponse);
        });

        return [
            'type' => 'text',
            'total_reponses' => $totalReponses,
            'moyenne_longueur' => round($moyenneLongueur),
            'sample_reponses' => $reponses->take(5)->pluck('reponse')->toArray(),
        ];
    }

    private function getMonthlyEvolution($formulaireType)
    {
        return Evaluation::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mois, COUNT(*) as total, AVG(score_global) as score_moyen')
            ->when($formulaireType, function($q) use ($formulaireType) {
                return $q->whereHas('reponsesDetails.questionEvaluation', function($sq) use ($formulaireType) {
                    $sq->where('formulaire_type', $formulaireType);
                });
            })
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();
    }

    private function getContextDistribution($formulaireType)
    {
        return Evaluation::selectRaw('contexte, COUNT(*) as total')
            ->when($formulaireType, function($q) use ($formulaireType) {
                return $q->whereHas('reponsesDetails.questionEvaluation', function($sq) use ($formulaireType) {
                    $sq->where('formulaire_type', $formulaireType);
                });
            })
            ->groupBy('contexte')
            ->get();
    }
}
