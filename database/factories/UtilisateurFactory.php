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
        $nomsGuineens = ['Diallo', 'Bah', 'Sylla', 'Condé', 'Camara', 'Keita', 'Soumaré', 'Touré', 'Barry', 'Baldé'];
        $prenomsGuineensFeminins = ['Fatou', 'Mariama', 'Aissatou', 'Kadiatou', 'Fatoumata', 'Aminata', 'Hawa', 'Ramata', 'Bintu', 'Aïcha'];

        return [
            'nom' => $this->faker->randomElement($nomsGuineens),
            'prenom' => $this->faker->randomElement($prenomsGuineensFeminins),
            'email' => $this->faker->unique->email(),
            'phone' => '+224' . $this->faker->unique->numerify('6########'),
            'sexe' => 'F',
            'status' => true,
            'anneedenaissance' => $this->faker->numberBetween(now()->year - 40, now()->year - 15),
            'password' => bcrypt('password'),
        ];
    }
}
