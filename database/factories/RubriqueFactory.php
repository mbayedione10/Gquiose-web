<?php

namespace Database\Factories;

use App\Models\Rubrique;
use Illuminate\Database\Eloquent\Factories\Factory;

class RubriqueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rubrique::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique->name(),
            'status' => $this->faker->boolean(),
        ];
    }
}
