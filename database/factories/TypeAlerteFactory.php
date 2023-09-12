<?php

namespace Database\Factories;

use App\Models\TypeAlerte;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeAlerteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TypeAlerte::class;

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
