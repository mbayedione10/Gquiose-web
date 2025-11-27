<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // SSR - Puberté
            [
                'question' => 'À quel âge vais-je avoir mes premières règles ?',
                'reponse' => 'L\'âge des premières règles varie d\'une fille à l\'autre, généralement entre 10 et 15 ans. Il n\'y a pas d\'âge "normal". Cela dépend de ton corps, de ta génétique et de ta santé. Si tu as 16 ans et que tu n\'as pas encore eu tes règles, consulte un professionnel de santé.',
                'status' => true,
            ],
            [
                'question' => 'Est-ce que les érections spontanées sont normales ?',
                'reponse' => 'Oui, c\'est totalement normal ! Pendant la puberté, les garçons ont souvent des érections involontaires, même sans pensées sexuelles. C\'est dû aux hormones. Ça arrive à tous les garçons et ça diminue avec l\'âge.',
                'status' => true,
            ],
            
            // SSR - Première fois
            [
                'question' => 'Peut-on tomber enceinte dès le premier rapport sexuel ?',
                'reponse' => 'OUI ! Tu peux tomber enceinte dès le premier rapport, même pendant tes règles. Le mythe du "la première fois on ne risque rien" est FAUX. Utilise toujours un préservatif ou une autre méthode contraceptive dès le premier rapport.',
                'status' => true,
            ],
            [
                'question' => 'La première fois, est-ce que ça fait toujours mal ?',
                'reponse' => 'Pas forcément. Avec de la détente, de la lubrification (naturelle ou avec un gel) et de la communication, la première fois peut se passer sans douleur. Si ça fait très mal, arrête et consulte. L\'important est de se sentir prêt.e, en confiance et de ne jamais se forcer.',
                'status' => true,
            ],
            
            // SSR - Contraception
            [
                'question' => 'Où puis-je obtenir des préservatifs gratuitement ?',
                'reponse' => 'Tu peux obtenir des préservatifs GRATUITEMENT dans les Centres de Santé pour Jeunes, certaines pharmacies partenaires, les associations de lutte contre le VIH, et parfois dans les infirmeries scolaires. N\'aie pas honte de demander, c\'est ton droit !',
                'status' => true,
            ],
            [
                'question' => 'Ai-je besoin de l\'autorisation de mes parents pour la contraception ?',
                'reponse' => 'Non ! En Guinée, les jeunes ont accès aux services de contraception de manière confidentielle et gratuite, même sans l\'accord des parents. Les professionnels de santé sont tenus au secret. Tu as le droit de protéger ta santé.',
                'status' => true,
            ],
            
            // SSR - Cycle menstruel
            [
                'question' => 'Comment utiliser l\'app GquiOse pour suivre mon cycle ?',
                'reponse' => 'Dans l\'app GquiOse, va dans la section "Mon Cycle". Note la date de tes premières règles, puis l\'app calculera automatiquement tes prochaines règles et ta période de fertilité. Tu recevras des notifications pour te rappeler quand tes règles arrivent. C\'est pratique et confidentiel !',
                'status' => true,
            ],
            
            // VBG - Consentement
            [
                'question' => 'Si je suis en couple, suis-je obligé.e d\'accepter tous les rapports sexuels ?',
                'reponse' => 'NON ! Être en couple ne t\'oblige à rien. Tu as le droit de dire NON à tout moment, même à ton/ta partenaire. Le viol conjugal existe et c\'est un crime. Une relation saine respecte toujours le consentement des deux personnes.',
                'status' => true,
            ],
            [
                'question' => 'C\'est quoi exactement le consentement ?',
                'reponse' => 'Le consentement c\'est dire OUI de manière : 1) Libre (sans pression ni menace), 2) Éclairée (tu sais ce qui va se passer), 3) Enthousiaste (tu en as vraiment envie), 4) Révocable (tu peux changer d\'avis à tout moment). Si l\'une de ces conditions manque, ce n\'est PAS un consentement.',
                'status' => true,
            ],
            
            // VBG - Violences
            [
                'question' => 'Mon petit ami vérifie mon téléphone, est-ce grave ?',
                'reponse' => 'OUI, c\'est un signe de VIOLENCE psychologique et de contrôle. Dans une relation saine, il y a de la confiance et du respect de la vie privée. Si ton partenaire te contrôle, t\'isole de tes ami.e.s, vérifie ton téléphone ou tes réseaux sociaux, c\'est de la violence. Parle-en à un adulte de confiance.',
                'status' => true,
            ],
            [
                'question' => 'J\'ai été victime de viol, que faire ?',
                'reponse' => 'D\'abord, sache que CE N\'EST PAS TA FAUTE. Ensuite : 1) Consulte dans les 72h pour soins médicaux et traitement d\'urgence (IST, grossesse), 2) Garde les preuves (vêtements, messages), 3) Porte plainte si tu te sens prêt.e, 4) Parle-en à un adulte de confiance ou appelle le 116. Des professionnels t\'aideront gratuitement et en toute confidentialité.',
                'status' => true,
            ],
            
            // VBG - Cyberharcèlement
            [
                'question' => 'Quelqu\'un menace de diffuser mes photos intimes, que faire ?',
                'reponse' => 'C\'est du CHANTAGE et c\'est INTERDIT par la loi ! 1) NE CÈDE PAS au chantage, 2) Fais des captures d\'écran des menaces (preuves), 3) Bloque la personne, 4) Signale sur la plateforme (Facebook, Instagram, etc.), 5) Porte PLAINTE à la police - c\'est un délit grave. Tu peux aussi utiliser la fonction d\'alerte dans GquiOse.',
                'status' => true,
            ],
            
            // Droits
            [
                'question' => 'Mes parents veulent me marier, quels sont mes droits ?',
                'reponse' => 'Le mariage forcé est INTERDIT et PUNISSABLE par la loi guinéenne. Tu as le droit de REFUSER. L\'âge légal du mariage est 18 ans. Si on te force, c\'est une VIOLENCE. Contacte immédiatement : la police, les services sociaux, une association de défense des droits (numéro dans GquiOse), ou utilise la fonction d\'alerte de l\'app. Tu seras protégé.e !',
                'status' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }

        $this->command->info('✅ ' . count($faqs) . ' FAQs SSR/VBG pour jeunes créées');
    }
}
