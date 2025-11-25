<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\QuestionEvaluation;
use App\Models\ReponseEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class APIEvaluationController extends Controller
{
    /**
     * Récupérer les questions d'évaluation actives
     */
    public function getQuestions(Request $request)
    {
        $contexte = $request->input('contexte', 'generale');
        
        $questions = QuestionEvaluation::where('status', true)
            ->orderBy('ordre')
            ->get()
            ->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'type' => $question->type,
                    'options' => $question->options,
                    'obligatoire' => $question->obligatoire,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $questions,
        ]);
    }

    /**
     * Soumettre une évaluation
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:utilisateurs,id',
            'contexte' => 'nullable|string|in:quiz,article,structure,generale,alerte',
            'contexte_id' => 'nullable|integer',
            'reponses' => 'required|array',
            'reponses.*.question_id' => 'required|exists:question_evaluations,id',
            'reponses.*.reponse' => 'required',
            'reponses.*.valeur_numerique' => 'nullable|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ], [
            'user_id.required' => 'L\'utilisateur est requis',
            'user_id.exists' => 'L\'utilisateur n\'existe pas',
            'reponses.required' => 'Les réponses sont requises',
            'reponses.array' => 'Le format des réponses est invalide',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Calculer le score global si applicable
            $scores = array_filter(array_column($request->reponses, 'valeur_numerique'));
            $scoreGlobal = !empty($scores) ? round(array_sum($scores) / count($scores), 2) : null;

            // Créer l'évaluation
            $evaluation = Evaluation::create([
                'utilisateur_id' => $request->user_id,
                'contexte' => $request->contexte ?? 'generale',
                'contexte_id' => $request->contexte_id,
                'reponses' => $request->reponses,
                'score_global' => $scoreGlobal,
                'commentaire' => $request->commentaire,
            ]);

            // Enregistrer les réponses individuelles
            foreach ($request->reponses as $reponse) {
                ReponseEvaluation::create([
                    'evaluation_id' => $evaluation->id,
                    'question_evaluation_id' => $reponse['question_id'],
                    'reponse' => $reponse['reponse'],
                    'valeur_numerique' => $reponse['valeur_numerique'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Évaluation enregistrée avec succès',
                'data' => [
                    'evaluation_id' => $evaluation->id,
                    'score_global' => $scoreGlobal,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de l\'évaluation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques d'évaluation
     */
    public function statistics(Request $request)
    {
        $contexte = $request->input('contexte');
        $contexteId = $request->input('contexte_id');

        $query = Evaluation::query();

        if ($contexte) {
            $query->where('contexte', $contexte);
        }

        if ($contexteId) {
            $query->where('contexte_id', $contexteId);
        }

        $stats = [
            'total_evaluations' => $query->count(),
            'score_moyen' => $query->avg('score_global'),
            'evaluations_recentes' => $query->latest()->take(10)->with('utilisateur')->get(),
            'repartition_scores' => $query->selectRaw('score_global, COUNT(*) as count')
                ->groupBy('score_global')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Récupérer les évaluations d'un utilisateur
     */
    public function userEvaluations($userId)
    {
        $evaluations = Evaluation::where('utilisateur_id', $userId)
            ->with('reponsesDetails.questionEvaluation')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $evaluations,
        ]);
    }
}
