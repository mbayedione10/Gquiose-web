<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Comment traiter la fièvre chez un enfant ?',
                'reponse' => 'Donnez du paracétamol adapté à l\'âge et au poids de l\'enfant, faites-le boire beaucoup d\'eau, et consultez rapidement un médecin si la fièvre persiste plus de 24h ou dépasse 39°C.',
                'status' => true,
            ],
            [
                'question' => 'Quels sont les signes de danger pendant la grossesse ?',
                'reponse' => 'Saignements, maux de tête violents, vision trouble, gonflement des pieds et du visage, fièvre élevée, douleurs abdominales intenses. Consultez immédiatement en cas de ces symptômes.',
                'status' => true,
            ],
            [
                'question' => 'Comment reconnaître le paludisme ?',
                'reponse' => 'Fièvre, frissons, maux de tête, courbatures, nausées, vomissements. Consultez rapidement pour un test et un traitement. Le paludisme non traité peut être mortel.',
                'status' => true,
            ],
            [
                'question' => 'À quel âge commencer les vaccinations ?',
                'reponse' => 'Les vaccinations commencent dès la naissance avec le BCG et la polio. Suivez le calendrier vaccinal : 6, 10, 14 semaines pour le pentavalent, puis 9 mois pour la rougeole.',
                'status' => true,
            ],
            [
                'question' => 'Comment préparer les sels de réhydratation (SRO) ?',
                'reponse' => 'Mélangez un sachet de SRO dans 1 litre d\'eau propre. Si pas de SRO : 6 cuillères à café de sucre + 1/2 cuillère à café de sel dans 1 litre d\'eau.',
                'status' => true,
            ],
            [
                'question' => 'Quand introduire l\'alimentation complémentaire ?',
                'reponse' => 'À partir de 6 mois, tout en continuant l\'allaitement. Commencez par des bouillies enrichies, purées, puis progressivement des aliments solides adaptés.',
                'status' => true,
            ],
            [
                'question' => 'Comment se protéger contre le VIH/SIDA ?',
                'reponse' => 'Utilisez des préservatifs lors des rapports sexuels, faites-vous dépister régulièrement, évitez le partage d\'objets tranchants, et suivez le traitement ARV si séropositif.',
                'status' => true,
            ],
            [
                'question' => 'Que faire en cas de morsure de serpent ?',
                'reponse' => 'Gardez la victime calme et immobile, retirez bijoux et vêtements serrés, marquez la zone de la morsure, et amenez immédiatement la personne à l\'hôpital. N\'incisez pas et ne sucez pas la plaie.',
                'status' => true,
            ],
            [
                'question' => 'Comment éviter les infections alimentaires ?',
                'reponse' => 'Lavez-vous les mains avant de cuisiner, lavez fruits et légumes, cuisez bien les aliments, conservez-les au frais, et ne consommez pas de viande ou poisson mal cuit.',
                'status' => true,
            ],
            [
                'question' => 'Quelle méthode contraceptive choisir ?',
                'reponse' => 'Consultez un agent de santé pour choisir selon votre âge et santé : pilules, injectables (3 mois), implants (3-5 ans), DIU, ou préservatifs. Tous sont gratuits dans les centres de santé.',
                'status' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }

        $this->command->info('✅ ' . count($faqs) . ' FAQs créées');
    }
}
