<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UtilisateurFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Utilisateur::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->text(255),
            'prenom' => $this->faker->text(255),
            'email' => $this->faker->unique->email(),
            'phone' => $this->faker->unique->phoneNumber(),
            'sexe' => $this->faker->text(255),
            'status' => $this->faker->boolean(),
        ];
    }
}
