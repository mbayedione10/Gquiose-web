
<?php

namespace Database\Seeders;

use App\Models\QuestionEvaluation;
use Illuminate\Database\Seeder;

class QuestionEvaluationTypesSeeder extends Seeder
{
    public function run()
    {
        // Formulaire satisfaction quiz
        $this->createQuizQuestions();

        // Formulaire satisfaction article
        $this->createArticleQuestions();

        // Formulaire satisfaction structure
        $this->createStructureQuestions();

        // Formulaire évaluation générale
        $this->createGeneralQuestions();
    }

    private function createQuizQuestions()
    {
        $questions = [
            [
                'question' => 'Comment évaluez-vous la difficulté de ce quiz ?',
                'type' => 'scale',
                'formulaire_type' => 'satisfaction_quiz',
                'ordre' => 1,
                'obligatoire' => true,
            ],
            [
                'question' => 'Les questions étaient-elles claires ?',
                'type' => 'yesno',
                'formulaire_type' => 'satisfaction_quiz',
                'ordre' => 2,
                'obligatoire' => true,
            ],
            [
                'question' => 'Avez-vous appris quelque chose de nouveau ?',
                'type' => 'yesno',
                'formulaire_type' => 'satisfaction_quiz',
                'ordre' => 3,
                'obligatoire' => true,
            ],
            [
                'question' => 'Qu\'avez-vous appris de plus intéressant ?',
                'type' => 'text',
                'formulaire_type' => 'satisfaction_quiz',
                'ordre' => 4,
                'obligatoire' => false,
                'condition_question_id' => 3,
                'condition_operator' => 'equals',
                'condition_value' => 'Oui',
                'show_if_condition_met' => true,
            ],
        ];

        foreach ($questions as $question) {
            QuestionEvaluation::create($question);
        }
    }

    private function createArticleQuestions()
    {
        $questions = [
            [
                'question' => 'Cet article était-il utile ?',
                'type' => 'rating',
                'formulaire_type' => 'satisfaction_article',
                'ordre' => 1,
                'obligatoire' => true,
            ],
            [
                'question' => 'Le contenu était-il facile à comprendre ?',
                'type' => 'scale',
                'formulaire_type' => 'satisfaction_article',
                'ordre' => 2,
                'obligatoire' => true,
            ],
            [
                'question' => 'Recommanderiez-vous cet article ?',
                'type' => 'yesno',
                'formulaire_type' => 'satisfaction_article',
                'ordre' => 3,
                'obligatoire' => true,
            ],
        ];

        foreach ($questions as $question) {
            QuestionEvaluation::create($question);
        }
    }

    private function createStructureQuestions()
    {
        $questions = [
            [
                'question' => 'Comment évaluez-vous l\'accueil de cette structure ?',
                'type' => 'rating',
                'formulaire_type' => 'satisfaction_structure',
                'ordre' => 1,
                'obligatoire' => true,
            ],
            [
                'question' => 'Les services proposés répondaient-ils à vos besoins ?',
                'type' => 'scale',
                'formulaire_type' => 'satisfaction_structure',
                'ordre' => 2,
                'obligatoire' => true,
            ],
            [
                'question' => 'Recommanderiez-vous cette structure ?',
                'type' => 'yesno',
                'formulaire_type' => 'satisfaction_structure',
                'ordre' => 3,
                'obligatoire' => true,
            ],
        ];

        foreach ($questions as $question) {
            QuestionEvaluation::create($question);
        }
    }

    private function createGeneralQuestions()
    {
        $questions = [
            [
                'question' => 'Comment évaluez-vous globalement l\'application ?',
                'type' => 'rating',
                'formulaire_type' => 'generale',
                'ordre' => 1,
                'obligatoire' => true,
            ],
            [
                'question' => 'L\'application vous a-t-elle été utile ?',
                'type' => 'scale',
                'formulaire_type' => 'generale',
                'ordre' => 2,
                'obligatoire' => true,
            ],
        ];

        foreach ($questions as $question) {
            QuestionEvaluation::create($question);
        }
    }
}
