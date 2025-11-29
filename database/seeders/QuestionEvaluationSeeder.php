<?php

namespace Database\Seeders;

use App\Models\QuestionEvaluation;
use Illuminate\Database\Seeder;

class QuestionEvaluationSeeder extends Seeder
{
    public function run(): void
    {
        // Questions pour formulaire GÉNÉRAL
        $questionsGenerales = [
            [
                'question' => 'Comment évaluez-vous votre expérience globale avec l\'application ?',
                'type' => 'rating',
                'formulaire_type' => 'generale',
                'options' => null,
                'ordre' => 1,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'L\'application répond-elle à vos besoins ?',
                'type' => 'yesno',
                'formulaire_type' => 'generale',
                'options' => null,
                'ordre' => 2,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Sur une échelle de 1 à 10, recommanderiez-vous cette application ?',
                'type' => 'scale',
                'formulaire_type' => 'generale',
                'options' => json_encode(['min' => 1, 'max' => 10]),
                'ordre' => 3,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Quelles fonctionnalités utilisez-vous le plus ?',
                'type' => 'multiple_choice',
                'formulaire_type' => 'generale',
                'options' => json_encode([
                    'Quiz éducatifs',
                    'Articles',
                    'Structures de santé',
                    'Alertes VBG',
                    'Suivi menstruel',
                    'Forum'
                ]),
                'ordre' => 4,
                'obligatoire' => false,
                'status' => true,
            ],
            [
                'question' => 'Avez-vous des suggestions pour améliorer l\'application ?',
                'type' => 'text',
                'formulaire_type' => 'generale',
                'options' => null,
                'ordre' => 5,
                'obligatoire' => false,
                'status' => true,
            ],
        ];

        // Questions pour SATISFACTION QUIZ
        $questionsQuiz = [
            [
                'question' => 'Le quiz était-il facile à comprendre ?',
                'type' => 'yesno',
                'formulaire_type' => 'satisfaction_quiz',
                'options' => null,
                'ordre' => 1,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Comment évaluez-vous la difficulté du quiz ?',
                'type' => 'scale',
                'formulaire_type' => 'satisfaction_quiz',
                'options' => json_encode(['min' => 1, 'max' => 5, 'labels' => ['Très facile', 'Facile', 'Moyen', 'Difficile', 'Très difficile']]),
                'ordre' => 2,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Avez-vous appris de nouvelles informations grâce à ce quiz ?',
                'type' => 'yesno',
                'formulaire_type' => 'satisfaction_quiz',
                'options' => null,
                'ordre' => 3,
                'obligatoire' => true,
                'status' => true,
                'condition_question_id' => null, // Sera défini après création
                'condition_operator' => 'equals',
                'condition_value' => 'oui',
                'show_if_condition_met' => true,
            ],
            [
                'question' => 'Qu\'avez-vous particulièrement apprécié dans ce quiz ?',
                'type' => 'text',
                'formulaire_type' => 'satisfaction_quiz',
                'options' => null,
                'ordre' => 4,
                'obligatoire' => false,
                'status' => true,
            ],
            [
                'question' => 'Notez la qualité du quiz',
                'type' => 'rating',
                'formulaire_type' => 'satisfaction_quiz',
                'options' => null,
                'ordre' => 5,
                'obligatoire' => true,
                'status' => true,
            ],
        ];

        // Questions pour SATISFACTION ARTICLE
        $questionsArticle = [
            [
                'question' => 'L\'article était-il clair et facile à comprendre ?',
                'type' => 'yesno',
                'formulaire_type' => 'satisfaction_article',
                'options' => null,
                'ordre' => 1,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Comment évaluez-vous la qualité des informations ?',
                'type' => 'rating',
                'formulaire_type' => 'satisfaction_article',
                'options' => null,
                'ordre' => 2,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Les informations vous ont-elles été utiles ?',
                'type' => 'yesno',
                'formulaire_type' => 'satisfaction_article',
                'options' => null,
                'ordre' => 3,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Qu\'avez-vous aimé dans cet article ?',
                'type' => 'multiple_choice',
                'formulaire_type' => 'satisfaction_article',
                'options' => json_encode([
                    'Clarté des explications',
                    'Conseils pratiques',
                    'Exemples concrets',
                    'Images/illustrations',
                    'Longueur appropriée'
                ]),
                'ordre' => 4,
                'obligatoire' => false,
                'status' => true,
            ],
            [
                'question' => 'Commentaires additionnels',
                'type' => 'text',
                'formulaire_type' => 'satisfaction_article',
                'options' => null,
                'ordre' => 5,
                'obligatoire' => false,
                'status' => true,
            ],
        ];

        // Questions pour SATISFACTION ALERTE
        $questionsAlerte = [
            [
                'question' => 'L\'alerte vous a-t-elle aidé ?',
                'type' => 'yesno',
                'formulaire_type' => 'satisfaction_alerte',
                'options' => null,
                'ordre' => 1,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Comment évaluez-vous la rapidité de la réponse ?',
                'type' => 'rating',
                'formulaire_type' => 'satisfaction_alerte',
                'options' => null,
                'ordre' => 2,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Vous êtes-vous senti(e) en sécurité après avoir utilisé le service d\'alerte ?',
                'type' => 'yesno',
                'formulaire_type' => 'satisfaction_alerte',
                'options' => null,
                'ordre' => 3,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Recommanderiez-vous ce service à d\'autres personnes ?',
                'type' => 'scale',
                'formulaire_type' => 'satisfaction_alerte',
                'options' => json_encode(['min' => 1, 'max' => 10]),
                'ordre' => 4,
                'obligatoire' => false,
                'status' => true,
            ],
            [
                'question' => 'Suggestions d\'amélioration',
                'type' => 'text',
                'formulaire_type' => 'satisfaction_alerte',
                'options' => null,
                'ordre' => 5,
                'obligatoire' => false,
                'status' => true,
            ],
        ];

        // Questions pour SATISFACTION STRUCTURE
        $questionsStructure = [
            [
                'question' => 'Avez-vous visité cette structure de santé ?',
                'type' => 'yesno',
                'formulaire_type' => 'satisfaction_structure',
                'options' => null,
                'ordre' => 1,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Comment évaluez-vous la qualité des services ?',
                'type' => 'rating',
                'formulaire_type' => 'satisfaction_structure',
                'options' => null,
                'ordre' => 2,
                'obligatoire' => true,
                'status' => true,
                'condition_question_id' => null, // Sera lié à la question 1
                'condition_operator' => 'equals',
                'condition_value' => 'oui',
                'show_if_condition_met' => true,
            ],
            [
                'question' => 'Le personnel était-il accueillant et respectueux ?',
                'type' => 'yesno',
                'formulaire_type' => 'satisfaction_structure',
                'options' => null,
                'ordre' => 3,
                'obligatoire' => true,
                'status' => true,
                'condition_question_id' => null, // Sera lié à la question 1
                'condition_operator' => 'equals',
                'condition_value' => 'oui',
                'show_if_condition_met' => true,
            ],
            [
                'question' => 'Sur une échelle de 1 à 10, recommanderiez-vous cette structure ?',
                'type' => 'scale',
                'formulaire_type' => 'satisfaction_structure',
                'options' => json_encode(['min' => 1, 'max' => 10]),
                'ordre' => 4,
                'obligatoire' => false,
                'status' => true,
                'condition_question_id' => null, // Sera lié à la question 1
                'condition_operator' => 'equals',
                'condition_value' => 'oui',
                'show_if_condition_met' => true,
            ],
            [
                'question' => 'Commentaires ou suggestions',
                'type' => 'text',
                'formulaire_type' => 'satisfaction_structure',
                'options' => null,
                'ordre' => 5,
                'obligatoire' => false,
                'status' => true,
            ],
        ];

        // Créer toutes les questions
        $allQuestions = array_merge(
            $questionsGenerales,
            $questionsQuiz,
            $questionsArticle,
            $questionsAlerte,
            $questionsStructure
        );

        $createdQuestions = [];
        foreach ($allQuestions as $questionData) {
            $question = QuestionEvaluation::firstOrCreate(
                [
                    'question' => $questionData['question'],
                    'formulaire_type' => $questionData['formulaire_type']
                ],
                $questionData
            );
            $createdQuestions[] = $question;
        }

        // Mettre à jour les conditions pour les questions quiz (question 3 dépend de question 3)
        $quizQ3 = QuestionEvaluation::where('formulaire_type', 'satisfaction_quiz')
            ->where('ordre', 3)
            ->first();
        $quizQ3Condition = QuestionEvaluation::where('formulaire_type', 'satisfaction_quiz')
            ->where('ordre', 3)
            ->first();
        
        if ($quizQ3 && $quizQ3Condition) {
            $quizQ3->update(['condition_question_id' => $quizQ3Condition->id]);
        }

        // Mettre à jour les conditions pour les questions structure (questions 2,3,4 dépendent de question 1)
        $structureQ1 = QuestionEvaluation::where('formulaire_type', 'satisfaction_structure')
            ->where('ordre', 1)
            ->first();
        
        if ($structureQ1) {
            QuestionEvaluation::where('formulaire_type', 'satisfaction_structure')
                ->whereIn('ordre', [2, 3, 4])
                ->update(['condition_question_id' => $structureQ1->id]);
        }

        $this->command->info('✅ ' . count($createdQuestions) . ' questions d\'évaluation créées avec logique conditionnelle');
        $this->command->info('   - ' . count($questionsGenerales) . ' questions générales');
        $this->command->info('   - ' . count($questionsQuiz) . ' questions quiz');
        $this->command->info('   - ' . count($questionsArticle) . ' questions article');
        $this->command->info('   - ' . count($questionsAlerte) . ' questions alerte');
        $this->command->info('   - ' . count($questionsStructure) . ' questions structure');
    }
}
