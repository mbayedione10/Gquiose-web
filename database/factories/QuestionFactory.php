<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique->name(),
            'reponse' => $this->faker->text(255),
            'option1' => $this->faker->text(255),
            'status' => $this->faker->boolean(),
            'thematique_id' => \App\Models\Thematique::factory(),
        ];
    }
}
