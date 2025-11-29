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
        $this->command->info('Création d\'évaluations avec réponses...');

        // Créer des évaluations avec réponses pour différents contextes
        $contextes = [
            'quiz' => 35,
            'article' => 40,
            'structure' => 25,
            'generale' => 30,
            'alerte' => 20,
        ];

        $utilisateurs = Utilisateur::all();
        $evaluationsCreated = 0;

        foreach ($contextes as $contexte => $count) {
            $formulaireType = $this->mapContexteToFormulaireType($contexte);
            $questions = QuestionEvaluation::where('status', true)
                ->where('formulaire_type', $formulaireType)
                ->orderBy('ordre')
                ->get();

            if ($questions->isEmpty()) {
                $this->command->warn("Aucune question trouvée pour le formulaire de type: {$formulaireType}");
                continue;
            }

            for ($i = 0; $i < $count; $i++) {
                // Vérifier si des utilisateurs existent
                if ($utilisateurs->isEmpty()) {
                    $this->command->warn('Aucun utilisateur trouvé. Impossible de créer des évaluations.');
                    return;
                }
                $utilisateur = $utilisateurs->random();

                // Distribution réaliste des dates (plus récent = plus d'évaluations)
                $daysAgo = $this->getRealisticDaysAgo();

                // Créer l'évaluation
                $evaluation = Evaluation::create([
                    'utilisateur_id' => $utilisateur->id,
                    'contexte' => $contexte,
                    'contexte_id' => rand(1, 20),
                    'reponses' => [],
                    'score_global' => 0,
                    'commentaire' => $this->getRealisticComment($formulaireType),
                    'created_at' => Carbon::now()->subDays($daysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                ]);

                $reponses = [];
                $totalScore = 0;
                $scoreCount = 0;

                // Créer des réponses pour chaque question du formulaire
                foreach ($questions as $question) {
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
                            $max = $options['max'] ?? 10; // Valeur par défaut si 'max' n'est pas défini
                            $normalizedScore = ($reponseData['valeur_numerique'] / $max) * 5;
                        } else {
                            // Pour les autres types numériques, on suppose qu'ils sont déjà sur une échelle compatible
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
        }

        $this->command->info("✅ {$evaluationsCreated} évaluation avec réponses créées");
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

    /**
     * Mappe le contexte au type de formulaire correspondant.
     *
     * @param string $contexte Le contexte de l'évaluation (ex: 'quiz', 'article', 'alerte').
     * @return string Le type de formulaire associé (ex: 'satisfaction_quiz', 'generale').
     */
    private function mapContexteToFormulaireType(string $contexte): string
    {
        $mapping = [
            'quiz' => 'satisfaction_quiz',
            'article' => 'satisfaction_article',
            'structure' => 'satisfaction_structure',
            'generale' => 'generale',
            'alerte' => 'generale',  // Les alertes utilisent le formulaire général
        ];

        return $mapping[$contexte] ?? 'generale';
    }

    private function getFaker()
    {
        return \Faker\Factory::create('fr_FR');
    }

    /**
     * Génère une distribution réaliste des dates (plus d'évaluations récentes)
     */
    private function getRealisticDaysAgo(): int
    {
        $random = rand(1, 100);

        // 40% dans les 7 derniers jours
        if ($random <= 40) {
            return rand(0, 7);
        }
        // 30% entre 8 et 30 jours
        elseif ($random <= 70) {
            return rand(8, 30);
        }
        // 20% entre 31 et 60 jours
        elseif ($random <= 90) {
            return rand(31, 60);
        }
        // 10% entre 61 et 90 jours
        else {
            return rand(61, 90);
        }
    }

    /**
     * Génère un commentaire réaliste selon le type de formulaire
     */
    private function getRealisticComment(string $formulaireType): ?string
    {
        // 40% de chance d'avoir un commentaire
        if (rand(1, 100) > 40) {
            return null;
        }

        $comments = match($formulaireType) {
            'generale' => [
                'Application très utile et facile à utiliser',
                'Interface intuitive, je recommande',
                'Quelques bugs à corriger mais bon dans l\'ensemble',
                'Excellente initiative pour informer les jeunes',
                'Très satisfait du service',
                'Besoin de plus de contenu',
                'Application pratique au quotidien',
            ],
            'satisfaction_quiz' => [
                'Quiz très instructif, j\'ai appris beaucoup',
                'Questions pertinentes et bien formulées',
                'Bon niveau de difficulté',
                'Quiz trop facile à mon goût',
                'Excellente façon d\'apprendre',
                'Manque d\'explications sur certaines réponses',
                'Format ludique et éducatif',
            ],
            'satisfaction_article' => [
                'Article très clair et informatif',
                'Contenu de qualité, merci',
                'Manque d\'exemples concrets',
                'Informations utiles et bien présentées',
                'Article trop long',
                'Excellentes explications',
                'Bon article, bien documenté',
            ],
            'satisfaction_structure' => [
                'Personnel très accueillant et professionnel',
                'Service de qualité',
                'Temps d\'attente un peu long',
                'Très bon accueil, je recommande',
                'Structure propre et bien équipée',
                'Besoin de plus de personnel',
                'Excellente prise en charge',
            ],
            'satisfaction_alerte' => [
                'Réponse rapide et efficace',
                'Service d\'alerte très rassurant',
                'Je me sens plus en sécurité',
                'Excellente initiative',
                'Délai de réponse acceptable',
                'Service indispensable',
                'Merci pour votre aide',
            ],
            default => ['Merci', 'Bon service', 'À améliorer'],
        };

        return $comments[array_rand($comments)];
    }
}