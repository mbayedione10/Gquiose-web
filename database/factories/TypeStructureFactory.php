<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\TypeStructure;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeStructureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TypeStructure::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique->name(),
            'icon' => $this->faker->text(255),
            'status' => $this->faker->boolean(),
        ];
    }
}
