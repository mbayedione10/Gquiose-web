<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\QuestionEvaluation;
use App\Models\ReponseEvaluation;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

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

        // Types de formulaires disponibles
        $formulaireTypes = ['generale', 'satisfaction_quiz', 'satisfaction_article', 'satisfaction_structure'];

        // Créer 100 évaluations de test
        $evaluationsCreated = 0;
        
        foreach (range(1, 100) as $index) {
            $utilisateur = $utilisateurs->random();
            $formulaireType = $formulaireTypes[array_rand($formulaireTypes)];
            
            // Questions pour ce type de formulaire
            $questionsFormulaire = $questions->where('formulaire_type', $formulaireType);
            
            if ($questionsFormulaire->isEmpty()) {
                continue;
            }

            // Créer l'évaluation avec une date aléatoire dans les 60 derniers jours
            $evaluation = Evaluation::create([
                'utilisateur_id' => $utilisateur->id,
                'contexte' => $this->getContexte($formulaireType),
                'contexte_id' => rand(1, 10),
                'reponses' => [],
                'score_global' => 0,
                'commentaire' => $this->getFaker()->optional(0.3)->sentence(),
                'created_at' => Carbon::now()->subDays(rand(0, 60)),
            ]);

            $reponses = [];
            $totalScore = 0;
            $scoreCount = 0;

            // Créer des réponses pour chaque question du formulaire
            foreach ($questionsFormulaire as $question) {
                $reponseData = $this->generateReponse($question);
                
                ReponseEvaluation::create([
                    'evaluation_id' => $evaluation->id,
                    'question_evaluation_id' => $question->id,
                    'reponse' => $reponseData['reponse'],
                    'valeur_numerique' => $reponseData['valeur_numerique'],
                    'created_at' => $evaluation->created_at,
                ]);

                $reponses[] = [
                    'question_id' => $question->id,
                    'reponse' => $reponseData['reponse'],
                    'valeur_numerique' => $reponseData['valeur_numerique'],
                ];

                if ($reponseData['valeur_numerique'] !== null) {
                    // Normaliser le score sur 5
                    if ($question->type === 'scale') {
                        $options = is_array($question->options) ? $question->options : json_decode($question->options, true);
                        $max = $options['max'] ?? 10;
                        $normalizedScore = ($reponseData['valeur_numerique'] / $max) * 5;
                    } else {
                        $normalizedScore = $reponseData['valeur_numerique'];
                    }
                    
                    $totalScore += $normalizedScore;
                    $scoreCount++;
                }
            }

            // Mettre à jour l'évaluation avec le score global
            $evaluation->update([
                'reponses' => $reponses,
                'score_global' => $scoreCount > 0 ? round($totalScore / $scoreCount, 2) : null,
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
                $options = is_array($question->options) ? $question->options : json_decode($question->options, true);
                $max = $options['max'] ?? 10;
                $valeur = rand(1, $max);
                return [
                    'reponse' => $valeur . '/' . $max,
                    'valeur_numerique' => $valeur,
                ];

            case 'yesno':
                $reponse = rand(0, 1) === 1 ? 'oui' : 'non';
                return [
                    'reponse' => $reponse,
                    'valeur_numerique' => $reponse === 'oui' ? 1 : 0,
                ];

            case 'multiple_choice':
                $options = is_array($question->options) ? $question->options : json_decode($question->options, true);
                if (!empty($options)) {
                    $reponse = $options[array_rand($options)];
                } else {
                    $reponse = 'Option ' . rand(1, 3);
                }
                return [
                    'reponse' => $reponse,
                    'valeur_numerique' => null,
                ];

            case 'text':
                $phrases = [
                    'Très satisfait de cette expérience',
                    'Bon service, à améliorer',
                    'Excellente plateforme',
                    'Quelques bugs à corriger',
                    'Interface intuitive et facile à utiliser',
                    'Contenu de qualité',
                    'Besoin de plus de fonctionnalités',
                    'Service rapide et efficace',
                    'Très utile pour ma situation',
                    'Merci pour cette initiative',
                ];
                return [
                    'reponse' => $phrases[array_rand($phrases)],
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

    private function getFaker()
    {
        return \Faker\Factory::create('fr_FR');
    }
}
