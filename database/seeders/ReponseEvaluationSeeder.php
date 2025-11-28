<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\QuestionEvaluation;
use App\Models\ReponseEvaluation;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;

class ReponseEvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $utilisateurs = Utilisateur::all();
        
        if ($utilisateurs->isEmpty()) {
            $this->command->warn('Aucun utilisateur trouvé. Création impossible.');
            return;
        }

        $questions = QuestionEvaluation::where('status', true)->get();
        
        if ($questions->isEmpty()) {
            $this->command->warn('Aucune question d\'évaluation trouvée.');
            return;
        }

        // Créer 30 évaluations de test
        $evaluationsCreated = 0;
        
        foreach (range(1, 30) as $index) {
            $utilisateur = $utilisateurs->random();
            $formulaireType = ['generale', 'satisfaction_quiz', 'satisfaction_article', 'satisfaction_structure'][array_rand(['generale', 'satisfaction_quiz', 'satisfaction_article', 'satisfaction_structure'])];
            
            // Questions pour ce type de formulaire
            $questionsFormulaire = $questions->where('formulaire_type', $formulaireType);
            
            if ($questionsFormulaire->isEmpty()) {
                continue;
            }

            // Créer l'évaluation
            $evaluation = Evaluation::create([
                'utilisateur_id' => $utilisateur->id,
                'contexte' => $this->getContexte($formulaireType),
                'contexte_id' => rand(1, 10),
                'reponses' => [],
                'score_global' => 0,
                'commentaire' => $this->faker()->optional()->sentence(),
            ]);

            $reponses = [];
            $totalScore = 0;
            $scoreCount = 0;

            // Créer des réponses pour chaque question
            foreach ($questionsFormulaire as $question) {
                $reponseData = $this->generateReponse($question);
                
                ReponseEvaluation::create([
                    'evaluation_id' => $evaluation->id,
                    'question_evaluation_id' => $question->id,
                    'reponse' => $reponseData['reponse'],
                    'valeur_numerique' => $reponseData['valeur_numerique'],
                ]);

                $reponses[] = [
                    'question_id' => $question->id,
                    'reponse' => $reponseData['reponse'],
                    'valeur_numerique' => $reponseData['valeur_numerique'],
                ];

                if ($reponseData['valeur_numerique'] !== null) {
                    $totalScore += $reponseData['valeur_numerique'];
                    $scoreCount++;
                }
            }

            // Mettre à jour l'évaluation avec le score global
            $evaluation->update([
                'reponses' => $reponses,
                'score_global' => $scoreCount > 0 ? $totalScore / $scoreCount : null,
            ]);

            $evaluationsCreated++;
        }

        $this->command->info("✅ {$evaluationsCreated} évaluations avec réponses créées");
        $this->command->info('   - Total réponses: ' . ReponseEvaluation::count());
    }

    private function generateReponse(QuestionEvaluation $question): array
    {
        switch ($question->type) {
            case 'rating':
                $valeur = rand(1, 5);
                return [
                    'reponse' => $valeur . '/5',
                    'valeur_numerique' => $valeur,
                ];

            case 'scale':
                $options = json_decode($question->options, true);
                $max = $options['max'] ?? 10;
                $valeur = rand(1, $max);
                return [
                    'reponse' => $valeur . '/' . $max,
                    'valeur_numerique' => $valeur,
                ];

            case 'yesno':
                $reponse = ['oui', 'non'][array_rand(['oui', 'non'])];
                return [
                    'reponse' => $reponse,
                    'valeur_numerique' => $reponse === 'oui' ? 1 : 0,
                ];

            case 'multiple_choice':
                $options = json_decode($question->options, true);
                $reponse = $options[array_rand($options)];
                return [
                    'reponse' => $reponse,
                    'valeur_numerique' => null,
                ];

            case 'text':
                return [
                    'reponse' => $this->faker()->sentence(),
                    'valeur_numerique' => null,
                ];

            default:
                return [
                    'reponse' => 'N/A',
                    'valeur_numerique' => null,
                ];
        }
    }

    private function getContexte(string $formulaireType): string
    {
        return match($formulaireType) {
            'satisfaction_quiz' => 'quiz',
            'satisfaction_article' => 'article',
            'satisfaction_structure' => 'structure',
            default => 'generale',
        };
    }

    private function faker()
    {
        return \Faker\Factory::create('fr_FR');
    }
}
