<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\QuestionEvaluation;
use App\Models\ReponseEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    public function index()
    {
        $evaluations = Evaluation::where('utilisateur_id', auth()->id())
            ->with('reponsesDetails.questionEvaluation')
            ->latest()
            ->paginate(15);

        return view('app.evaluations.index', compact('evaluations'));
    }

    public function create(Request $request)
    {
        $contexte = $request->input('contexte', 'generale');
        
        $questions = QuestionEvaluation::where('status', true)
            ->orderBy('ordre')
            ->get();

        return view('app.evaluations.form', compact('questions', 'contexte'));
    }

    public function submit(Request $request)
    {
        // Validate basic structure
        $validated = $request->validate([
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
            'reponses.required' => 'Les réponses sont requises',
            'reponses.*.question_id.required' => 'Question ID manquant',
            'reponses.*.reponse.required' => 'Réponse requise',
        ]);

        // Verify all required questions are answered
        $questionIds = array_column($request->reponses, 'question_id');
        $requiredQuestions = QuestionEvaluation::where('status', true)
            ->where('obligatoire', true)
            ->pluck('id')
            ->toArray();
        
        $missingRequired = array_diff($requiredQuestions, $questionIds);
        if (!empty($missingRequired)) {
            return back()->withInput()
                ->withErrors(['error' => 'Toutes les questions obligatoires doivent être répondues.']);
        }

        try {
            DB::beginTransaction();

            // Calculer le score global
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
                    'reponse' => $reponse['reponse'] ?? '',
                    'valeur_numerique' => $reponse['valeur_numerique'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('evaluations.index')
                ->with('success', 'Évaluation soumise avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->withErrors(['error' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()]);
        }
    }

    public function show(Evaluation $evaluation)
    {
        // Vérifier que l'utilisateur peut voir cette évaluation
        if ($evaluation->utilisateur_id !== auth()->id()) {
            abort(403);
        }

        $evaluation->load('reponsesDetails.questionEvaluation');

        return view('app.evaluations.show', compact('evaluation'));
    }
}
