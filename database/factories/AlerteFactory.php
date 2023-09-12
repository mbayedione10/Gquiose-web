<?php

namespace Database\Factories;

use App\Models\Alerte;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlerteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Alerte::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ref' => $this->faker->unique->text(255),
            'description' => $this->faker->sentence(15),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'etat' => $this->faker->word(),
            'type_alerte_id' => \App\Models\TypeAlerte::factory(),
            'ville_id' => \App\Models\Ville::factory(),
        ];
    }
}
