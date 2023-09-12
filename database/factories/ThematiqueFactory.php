<?php

namespace Database\Factories;

use App\Models\Thematique;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThematiqueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Thematique::class;

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
