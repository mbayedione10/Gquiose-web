<?php

namespace Database\Seeders;

use App\Models\QuestionEvaluation;
use Illuminate\Database\Seeder;

class QuestionEvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            [
                'question' => 'Comment évaluez-vous la qualité des informations fournies ?',
                'type' => 'rating',
                'options' => null,
                'ordre' => 1,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Les informations étaient-elles faciles à comprendre ?',
                'type' => 'yesno',
                'options' => null,
                'ordre' => 2,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Quel est votre niveau de satisfaction global ?',
                'type' => 'scale',
                'options' => json_encode(['min' => 1, 'max' => 10]),
                'ordre' => 3,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Qu\'avez-vous le plus apprécié ?',
                'type' => 'multiple_choice',
                'options' => json_encode([
                    'La clarté des explications',
                    'Les exemples concrets',
                    'Les conseils pratiques',
                    'Les illustrations',
                    'Autre'
                ]),
                'ordre' => 4,
                'obligatoire' => false,
                'status' => true,
            ],
            [
                'question' => 'Recommanderiez-vous cette application à vos proches ?',
                'type' => 'yesno',
                'options' => null,
                'ordre' => 5,
                'obligatoire' => true,
                'status' => true,
            ],
            [
                'question' => 'Avez-vous des suggestions d\'amélioration ?',
                'type' => 'text',
                'options' => null,
                'ordre' => 6,
                'obligatoire' => false,
                'status' => true,
            ],
            [
                'question' => 'Comment évaluez-vous la navigation dans l\'application ?',
                'type' => 'rating',
                'options' => null,
                'ordre' => 7,
                'obligatoire' => false,
                'status' => true,
            ],
            [
                'question' => 'Les informations vous ont-elles aidé dans votre vie quotidienne ?',
                'type' => 'yesno',
                'options' => null,
                'ordre' => 8,
                'obligatoire' => true,
                'status' => true,
            ],
        ];

        foreach ($questions as $question) {
            QuestionEvaluation::firstOrCreate(
                ['question' => $question['question']],
                $question
            );
        }

        $this->command->info('✅ ' . count($questions) . ' questions d\'évaluation créées');
    }
}
