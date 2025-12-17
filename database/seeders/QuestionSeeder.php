<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Thematique;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Questions de quiz sur la santé sexuelle et reproductive
     */
    protected array $questions = [
        // Puberté et Changements Corporels (thematique_id: 1)
        [
            'thematique' => 'Puberté et Changements Corporels',
            'name' => 'À quel âge commence généralement la puberté chez les filles ?',
            'reponse' => 'Entre 8 et 13 ans',
            'option1' => 'Entre 5 et 7 ans',
            'option2' => 'Entre 15 et 18 ans',
            'option3' => 'Après 20 ans',
        ],
        [
            'thematique' => 'Puberté et Changements Corporels',
            'name' => 'Quel est le premier signe de la puberté chez les filles ?',
            'reponse' => 'Le développement des seins',
            'option1' => 'Les premières règles',
            'option2' => 'La croissance des poils',
            'option3' => 'La voix qui change',
        ],

        // Contraception et Planning Familial (thematique_id: 2)
        [
            'thematique' => 'Contraception et Planning Familial',
            'name' => 'Quelle méthode contraceptive protège également contre les IST ?',
            'reponse' => 'Le préservatif',
            'option1' => 'La pilule',
            'option2' => 'Le stérilet',
            'option3' => 'L\'implant',
        ],
        [
            'thematique' => 'Contraception et Planning Familial',
            'name' => 'Qu\'est-ce que la contraception d\'urgence ?',
            'reponse' => 'Une méthode utilisée après un rapport non protégé',
            'option1' => 'Une méthode de contraception quotidienne',
            'option2' => 'Un test de grossesse',
            'option3' => 'Un vaccin contre les IST',
        ],

        // IST et VIH/SIDA (thematique_id: 3)
        [
            'thematique' => 'IST et VIH/SIDA',
            'name' => 'Comment se transmet principalement le VIH ?',
            'reponse' => 'Par les rapports sexuels non protégés et le sang',
            'option1' => 'Par les moustiques',
            'option2' => 'Par la poignée de main',
            'option3' => 'Par la salive',
        ],
        [
            'thematique' => 'IST et VIH/SIDA',
            'name' => 'Peut-on vivre normalement avec le VIH sous traitement ?',
            'reponse' => 'Oui, avec un traitement antirétroviral adapté',
            'option1' => 'Non, c\'est toujours fatal',
            'option2' => 'Seulement pendant quelques mois',
            'option3' => 'Uniquement si on est jeune',
        ],

        // Cycle Menstruel et Hygiène Menstruelle (thematique_id: 5)
        [
            'thematique' => 'Cycle Menstruel et Hygiène Menstruelle',
            'name' => 'Quelle est la durée moyenne d\'un cycle menstruel ?',
            'reponse' => '28 jours',
            'option1' => '7 jours',
            'option2' => '14 jours',
            'option3' => '45 jours',
        ],
        [
            'thematique' => 'Cycle Menstruel et Hygiène Menstruelle',
            'name' => 'À quelle fréquence doit-on changer de protection hygiénique ?',
            'reponse' => 'Toutes les 4 à 6 heures',
            'option1' => 'Une fois par jour',
            'option2' => 'Toutes les 12 heures',
            'option3' => 'Uniquement quand elle est pleine',
        ],

        // Grossesse et Maternité Précoce (thematique_id: 4)
        [
            'thematique' => 'Grossesse et Maternité Précoce',
            'name' => 'Pourquoi la grossesse précoce est-elle risquée pour la santé ?',
            'reponse' => 'Le corps n\'est pas encore prêt physiquement',
            'option1' => 'Ce n\'est pas risqué',
            'option2' => 'Uniquement pour des raisons sociales',
            'option3' => 'Seulement si on est malade',
        ],

        // Violences Conjugales et Domestiques (thematique_id: 8)
        [
            'thematique' => 'Violences Conjugales et Domestiques',
            'name' => 'Que faire si on est victime de violence conjugale ?',
            'reponse' => 'Chercher de l\'aide auprès de structures spécialisées',
            'option1' => 'Garder le silence',
            'option2' => 'C\'est normal dans un couple',
            'option3' => 'Attendre que ça passe',
        ],

        // Harcèlement et Abus Sexuels (thematique_id: 9)
        [
            'thematique' => 'Harcèlement et Abus Sexuels',
            'name' => 'Le consentement doit être :',
            'reponse' => 'Libre, éclairé et révocable à tout moment',
            'option1' => 'Donné une fois pour toutes',
            'option2' => 'Implicite dans une relation',
            'option3' => 'Uniquement verbal',
        ],

        // Droits Sexuels et Reproductifs (thematique_id: 13)
        [
            'thematique' => 'Droits Sexuels et Reproductifs',
            'name' => 'Les droits sexuels et reproductifs incluent :',
            'reponse' => 'Le droit de décider librement de sa sexualité',
            'option1' => 'Uniquement le droit à la contraception',
            'option2' => 'Rien de spécifique',
            'option3' => 'Seulement pour les adultes mariés',
        ],

        // Égalité des Genres (thematique_id: 14)
        [
            'thematique' => 'Égalité des Genres',
            'name' => 'L\'égalité des genres signifie :',
            'reponse' => 'Les mêmes droits et opportunités pour tous',
            'option1' => 'Que les femmes deviennent comme les hommes',
            'option2' => 'Que les hommes perdent leurs droits',
            'option3' => 'Rien d\'important',
        ],

        // Autonomisation des Jeunes Filles (thematique_id: 15)
        [
            'thematique' => 'Autonomisation des Jeunes Filles',
            'name' => 'L\'éducation des filles est importante car :',
            'reponse' => 'Elle leur permet d\'être indépendantes et de faire des choix éclairés',
            'option1' => 'Ce n\'est pas vraiment important',
            'option2' => 'Uniquement pour trouver un mari',
            'option3' => 'Seulement pour les riches',
        ],
    ];

    public function run(): void
    {
        foreach ($this->questions as $questionData) {
            $thematique = Thematique::where('name', $questionData['thematique'])->first();

            if ($thematique) {
                Question::firstOrCreate(
                    ['name' => $questionData['name']],
                    [
                        'thematique_id' => $thematique->id,
                        'reponse' => $questionData['reponse'],
                        'option1' => $questionData['option1'],
                        'option2' => $questionData['option2'],
                        'option3' => $questionData['option3'] ?? null,
                        'option4' => $questionData['option4'] ?? null,
                        'status' => true,
                    ]
                );
            }
        }
    }
}
