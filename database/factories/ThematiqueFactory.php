<?php

namespace Database\Factories;

use App\Models\Thematique;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThematiqueFactory extends Factory
{
    protected $model = Thematique::class;

    protected static array $thematiques = [
        'Puberté et Changements Corporels',
        'Contraception et Planning Familial',
        'IST et VIH/SIDA',
        'Grossesse et Maternité Précoce',
        'Cycle Menstruel et Hygiène Menstruelle',
        'Santé Reproductive des Jeunes',
        'Éducation Sexuelle Complète',
        'Violences Conjugales et Domestiques',
        'Harcèlement et Abus Sexuels',
        'Mariages Précoces et Forcés',
        'Mutilations Génitales Féminines (MGF)',
        'Cyberharcèlement et Violences en Ligne',
        'Droits Sexuels et Reproductifs',
        'Égalité des Genres',
        'Autonomisation des Jeunes Filles',
    ];

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(self::$thematiques),
            'status' => true,
        ];
    }
}
