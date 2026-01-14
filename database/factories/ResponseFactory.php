<?php

namespace Database\Factories;

use App\Models\Response;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResponseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Response::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reponse' => $this->faker->text(255),
            'isValid' => $this->faker->boolean(),
            'question_id' => \App\Models\Question::factory(),
            'utilisateur_id' => \App\Models\Utilisateur::factory(),
        ];
    }
}
