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
        $actualites = Rubrique::where('name', 'Actualités Santé')->first();
        $conseils = Rubrique::where('name', 'Conseils Pratiques')->first();
        $prevention = Rubrique::where('name', 'Prévention')->first();
        $santeFemme = Rubrique::where('name', 'Santé de la Femme')->first();
        $santeEnfant = Rubrique::where('name', 'Santé de l\'Enfant')->first();

        $articles = [
            [
                'title' => 'Campagne de vaccination contre la rougeole en Guinée',
                'description' => 'Le Ministère de la Santé lance une grande campagne de vaccination contre la rougeole pour tous les enfants de 6 mois à 15 ans. Cette campagne qui se déroulera du 1er au 15 mars vise à protéger plus de 2 millions d\'enfants à travers tout le pays. Les parents sont invités à se rendre dans les centres de santé les plus proches avec leurs enfants.',
                'rubrique_id' => $actualites?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => 'Comment prévenir le paludisme pendant la saison des pluies',
                'description' => 'Le paludisme reste l\'une des principales causes de mortalité en Guinée. Pour vous protéger pendant la saison des pluies, dormez sous une moustiquaire imprégnée, éliminez les eaux stagnantes autour de votre maison, portez des vêtements longs le soir, et consultez rapidement en cas de fièvre. Un traitement précoce peut sauver des vies.',
                'rubrique_id' => $prevention?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => 'L\'importance des consultations prénatales',
                'description' => 'Les consultations prénatales sont essentielles pour la santé de la mère et du bébé. Elles permettent de détecter et prévenir les complications, de suivre le développement du fœtus, et de préparer l\'accouchement. Toute femme enceinte devrait effectuer au moins 4 consultations prénatales durant sa grossesse.',
                'rubrique_id' => $santeFemme?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => 'Vaccination des enfants : calendrier et importance',
                'description' => 'La vaccination protège vos enfants contre des maladies graves. Le calendrier vaccinal en Guinée comprend le BCG, la polio, le pentavalent, la rougeole et la fièvre jaune. Respectez les rendez-vous de vaccination et conservez précieusement le carnet de santé de votre enfant. Ces vaccins sont gratuits dans tous les centres de santé.',
                'rubrique_id' => $santeEnfant?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => '10 conseils pour une alimentation saine',
                'description' => 'Une alimentation équilibrée est la base d\'une bonne santé. Mangez varié avec des fruits et légumes locaux, consommez des protéines (poisson, viande, œufs), privilégiez les céréales complètes, limitez le sel et le sucre, buvez beaucoup d\'eau propre, évitez l\'alcool et le tabac. Mangez à heures régulières et pratiquez une activité physique.',
                'rubrique_id' => $conseils?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => 'Allaitement maternel : bienfaits et recommandations',
                'description' => 'L\'OMS recommande l\'allaitement maternel exclusif pendant les 6 premiers mois de vie. Le lait maternel contient tous les nutriments nécessaires au bébé, renforce son immunité, crée un lien affectif avec la mère, et est gratuit. Continuez l\'allaitement avec une alimentation complémentaire jusqu\'à 2 ans ou plus.',
                'rubrique_id' => $santeEnfant?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => 'Hygiène des mains : un geste qui sauve des vies',
                'description' => 'Se laver les mains avec du savon est l\'un des moyens les plus efficaces pour prévenir les maladies. Lavez-vous les mains avant de manger, avant de préparer à manger, après être allé aux toilettes, après avoir changé un bébé, et en rentrant à la maison. Frottez pendant au moins 20 secondes.',
                'rubrique_id' => $prevention?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => 'Diarrhée chez l\'enfant : quand consulter ?',
                'description' => 'La diarrhée est fréquente chez les enfants mais peut être dangereuse. Consultez immédiatement si votre enfant a moins de 6 mois, présente du sang dans les selles, vomit beaucoup, refuse de boire, a de la fièvre élevée, ou montre des signes de déshydratation (yeux creux, bouche sèche, pas de larmes). En attendant, donnez des SRO.',
                'rubrique_id' => $santeEnfant?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => 'Planning familial : méthodes contraceptives disponibles',
                'description' => 'Plusieurs méthodes contraceptives sont disponibles gratuitement dans les centres de santé : pilules, injectables, implants, DIU, préservatifs. Consultez un agent de santé pour choisir la méthode qui vous convient le mieux selon votre âge, votre santé et vos projets. Le planning familial permet d\'espacer les naissances.',
                'rubrique_id' => $santeFemme?->id,
                'user_id' => $admin?->id,
                'status' => true,
            ],
            [
                'title' => 'Diabète et hypertension : dépistage gratuit ce mois-ci',
                'description' => 'À l\'occasion de la Journée Mondiale de la Santé, des dépistages gratuits du diabète et de l\'hypertension sont organisés dans tous les centres de santé. Ces maladies silencieuses touchent de plus en plus de Guinéens. Un dépistage précoce permet une prise en charge efficace. Profitez de cette opportunité pour connaître votre état de santé.',
                'rubrique_id' => $actualites?->id,
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

        $this->command->info('✅ ' . count($articles) . ' articles créés');
    }
}
