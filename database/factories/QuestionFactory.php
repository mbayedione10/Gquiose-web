<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Thematique;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        // Utilise une thématique existante ou en crée une si aucune n'existe
        $thematique = Thematique::inRandomOrder()->first();

        return [
            'name' => $this->faker->unique()->sentence(6).' ?',
            'reponse' => $this->faker->sentence(4),
            'option1' => $this->faker->sentence(4),
            'option2' => $this->faker->sentence(4),
            'option3' => $this->faker->optional(0.5)->sentence(4),
            'option4' => $this->faker->optional(0.3)->sentence(4),
            'status' => $this->faker->boolean(80),
            'thematique_id' => $thematique?->id ?? Thematique::factory(),
        ];
    }
}
