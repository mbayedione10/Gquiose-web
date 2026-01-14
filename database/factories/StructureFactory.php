<?php

namespace Database\Factories;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\Factory;

class StructureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Structure::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(15),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'phone' => $this->faker->unique->phoneNumber(),
            'status' => $this->faker->boolean(),
            'adresse' => $this->faker->text(255),
            'type_structure_id' => \App\Models\TypeStructure::factory(),
            'ville_id' => \App\Models\Ville::factory(),
        ];
    }
}
