<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Rubrique;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();

        // Rubriques
        $jeDécouvreMonCorps = Rubrique::where('name', 'Je Découvre Mon Corps')->first();
        $sexualiteRelations = Rubrique::where('name', 'Sexualité et Relations')->first();
        $santeRepro = Rubrique::where('name', 'Ma Santé Reproductive')->first();
        $contraception = Rubrique::where('name', 'Contraception : Mes Options')->first();
        $prevIST = Rubrique::where('name', 'Prévention IST/VIH')->first();
        $direNon = Rubrique::where('name', 'Dire Non aux Violences')->first();
        $droits = Rubrique::where('name', 'Mes Droits, Mon Pouvoir')->first();
        $aide = Rubrique::where('name', 'Où Trouver de l\'Aide ?')->first();

        $articles = [
            // SSR - Corps et Puberté
            [
                'title' => 'La puberté chez les filles : tout ce que tu dois savoir',
                'description' => 'Entre 10 et 15 ans, ton corps change ! Développement des seins, premières règles, poils pubiens... Ces changements sont normaux et naturels. Chaque fille vit sa puberté à son propre rythme. Tes règles peuvent être irrégulières au début, c\'est normal. Si tu as des questions ou des inquiétudes, n\'hésite pas à en parler à un adulte de confiance ou à consulter un professionnel de santé. Tu n\'es pas seule !',
                'rubrique_id' => $jeDécouvreMonCorps?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => 'La puberté chez les garçons : les changements expliqués',
                'description' => 'Voix qui mue, croissance rapide, développement musculaire, poils qui poussent, érections spontanées... La puberté chez les garçons démarre généralement entre 11 et 16 ans. Ces transformations sont dues aux hormones et sont complètement normales. Les érections involontaires et les éjaculations nocturnes font partie du processus naturel. Parle-en sans gêne à un adulte de confiance ou un professionnel de santé.',
                'rubrique_id' => $jeDécouvreMonCorps?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],

            // SSR - Cycle et Règles
            [
                'title' => 'Tout sur les règles : c\'est quoi, comment ça marche ?',
                'description' => 'Les règles, ou menstruations, c\'est l\'écoulement de sang qui sort du vagin chaque mois. Cela dure entre 3 et 7 jours et se répète environ tous les 28 jours (mais ça peut varier !). C\'est le signe que ton corps fonctionne normalement et qu\'il se prépare à une éventuelle grossesse. Utilise des serviettes hygiéniques ou des tampons, change-les régulièrement (toutes les 3-4h). Si tu as des douleurs fortes, consulte un professionnel de santé.',
                'rubrique_id' => $santeRepro?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],

            // SSR - Contraception
            [
                'title' => 'Les méthodes de contraception adaptées aux jeunes',
                'description' => 'Tu peux avoir une vie sexuelle sans tomber enceinte ! Les préservatifs (masculins et féminins) protègent à la fois contre la grossesse ET les IST. La pilule contraceptive est efficace contre la grossesse (mais pas les IST). L\'implant et l\'injection sont des méthodes longue durée. Toutes ces méthodes sont disponibles GRATUITEMENT dans les centres de santé pour jeunes. N\'aie pas honte de demander !',
                'rubrique_id' => $contraception?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => 'Le préservatif : mode d\'emploi et où en trouver gratuitement',
                'description' => 'Le préservatif est la SEULE méthode qui protège à la fois contre la grossesse et les IST/VIH. Utilise-le dès le premier rapport et à CHAQUE rapport. Comment ? Ouvre délicatement le sachet, déroule-le sur le pénis en érection, laisse un petit espace au bout. Après l\'éjaculation, retire-le en le tenant à la base. Tu peux en trouver GRATUITEMENT dans les centres de santé, les pharmacies participantes et les associations jeunesse.',
                'rubrique_id' => $contraception?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],

            // SSR - IST et VIH
            [
                'title' => 'IST et VIH : comment se protéger ?',
                'description' => 'Les Infections Sexuellement Transmissibles (IST) se transmettent lors de rapports sexuels non protégés. Le VIH/SIDA en fait partie. BONNE NOUVELLE : tu peux t\'en protéger ! Utilise TOUJOURS un préservatif, fais-toi dépister régulièrement (c\'est gratuit et confidentiel), et si tu as eu un rapport à risque, consulte dans les 72h pour un traitement d\'urgence. Le dépistage précoce sauve des vies !',
                'rubrique_id' => $prevIST?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],

            // VBG - Consentement
            [
                'title' => 'C\'est quoi le consentement ? Apprends à dire NON',
                'description' => 'Le consentement, c\'est dire OUI de manière libre, éclairée et enthousiaste. Si tu n\'es pas sûr.e, si on te met la pression, si tu as bu de l\'alcool, si tu as peur, CE N\'EST PAS un consentement. Tu as le DROIT de dire NON à tout moment, même si tu as dit oui avant. Ton corps t\'appartient. Si quelqu\'un ne respecte pas ton NON, c\'est une violence et tu peux porter plainte.',
                'rubrique_id' => $direNon?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],

            // VBG - Violences
            [
                'title' => 'Reconnaître les signes de violence dans une relation',
                'description' => 'Une relation amoureuse doit être basée sur le respect, la confiance et l\'égalité. ATTENTION aux signes de violence : ton/ta partenaire te contrôle (vérifie ton téléphone, t\'isole de tes ami.e.s), t\'insulte, te rabaisse, te frappe, te force à des actes sexuels, menace de diffuser des photos intimes. CE N\'EST PAS DE L\'AMOUR, c\'est de la VIOLENCE. Tu mérites mieux ! Parle-en à un adulte de confiance ou appelle une ligne d\'aide.',
                'rubrique_id' => $direNon?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => 'Harcèlement sexuel : comment réagir ?',
                'description' => 'Le harcèlement sexuel, ce sont des comportements non désirés à caractère sexuel : remarques, gestes déplacés, attouchements, messages intimes non sollicités, chantage... Que ce soit dans la rue, à l\'école, au travail ou en ligne, c\'est INTERDIT et PUNISSABLE. Tu n\'es PAS responsable. Réagis : dis NON fermement, éloigne-toi, parles-en à un adulte de confiance, garde les preuves (screenshots), porte plainte si nécessaire.',
                'rubrique_id' => $direNon?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],

            // Droits et Soutien
            [
                'title' => 'Tes droits sexuels et reproductifs en tant que jeune',
                'description' => 'En tant que jeune, tu as des DROITS ! Le droit à l\'information sur la sexualité et la contraception, le droit d\'accéder aux services de santé de manière confidentielle et gratuite, le droit de refuser un mariage forcé, le droit de choisir si et quand avoir des enfants, le droit de dire NON à toute violence. Personne ne peut te forcer à faire quoi que ce soit avec ton corps. Ces droits sont protégés par la loi !',
                'rubrique_id' => $droits?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => 'Où trouver de l\'aide en cas de violence ou de grossesse non désirée ?',
                'description' => 'Tu n\'es pas seul.e ! Des professionnels sont là pour t\'aider : les Centres de Santé pour Jeunes (gratuits et confidentiels), les lignes d\'écoute (116 en Guinée), les associations de soutien aux victimes de VBG, les travailleurs sociaux à l\'école. En cas de viol ou agression, consulte dans les 72h pour soins médicaux et traitement d\'urgence. Porte plainte à la police si tu te sens prêt.e. L\'aide existe, ose en parler !',
                'rubrique_id' => $aide?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
        ];

        foreach ($articles as $article) {
            $slug = Str::slug($article['title']);
            Article::firstOrCreate(
                ['slug' => $slug],
                array_merge($article, ['slug' => $slug])
            );
        }

        $this->command->info('✅ '.count($articles).' articles SSR/VBG pour jeunes créés');
    }
}
