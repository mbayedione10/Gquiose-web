
<?php

namespace Database\Seeders;

use App\Models\Conseil;
use Illuminate\Database\Seeder;

class ConseilSeeder extends Seeder
{
    public function run(): void
    {
        $conseils = [
            // SSR - Contraception et Protection
            ['message' => 'Utilise un préservatif à CHAQUE rapport pour te protéger contre les IST et les grossesses non désirées.', 'categorie' => 'SSR'],
            ['message' => 'Les préservatifs sont gratuits dans les centres de santé. N\'hésite pas à en demander!', 'categorie' => 'SSR'],
            ['message' => 'La pilule contraceptive ne protège PAS contre les IST. Utilise aussi un préservatif.', 'categorie' => 'SSR'],
            ['message' => 'En cas de rapport non protégé, consulte dans les 72h pour une contraception d\'urgence (pilule du lendemain).', 'categorie' => 'SSR'],
            ['message' => 'Fais-toi dépister régulièrement pour les IST, c\'est gratuit, rapide et confidentiel.', 'categorie' => 'SSR'],
            
            // SSR - Cycle et Règles
            ['message' => 'Utilise l\'app GquiOse pour suivre ton cycle menstruel et anticiper tes prochaines règles.', 'categorie' => 'SSR'],
            ['message' => 'Change de serviette hygiénique toutes les 3-4 heures pour éviter les infections.', 'categorie' => 'SSR'],
            ['message' => 'Pendant tes règles, tu peux faire du sport, te baigner et mener une vie normale.', 'categorie' => 'SSR'],
            ['message' => 'Des douleurs intenses pendant les règles ne sont pas normales. Consulte un professionnel.', 'categorie' => 'SSR'],
            
            // SSR - Information et Droits
            ['message' => 'Tu as le droit d\'accéder aux services de santé sexuelle GRATUITEMENT et en toute CONFIDENTIALITÉ.', 'categorie' => 'SSR'],
            ['message' => 'Informe-toi sur la contraception AVANT ta première fois, pas après.', 'categorie' => 'SSR'],
            ['message' => 'Pose toutes tes questions sur la sexualité aux professionnels de santé, aucune question n\'est bête.', 'categorie' => 'SSR'],
            
            // VBG - Consentement
            ['message' => 'Ton corps t\'appartient. Tu as le droit de dire NON à tout moment.', 'categorie' => 'VBG'],
            ['message' => 'Le consentement doit être libre, éclairé et enthousiaste. Si tu doutes, c\'est NON.', 'categorie' => 'VBG'],
            ['message' => 'Un.e partenaire qui respecte ne te force jamais et accepte ton NON sans discuter.', 'categorie' => 'VBG'],
            
            // VBG - Relations Saines
            ['message' => 'Une relation amoureuse saine est basée sur le respect, la confiance et l\'égalité.', 'categorie' => 'VBG'],
            ['message' => 'Si ton/ta partenaire te contrôle, t\'isole ou te rabaisse, c\'est de la violence, pas de l\'amour.', 'categorie' => 'VBG'],
            ['message' => 'La jalousie excessive n\'est PAS une preuve d\'amour, c\'est un signe de violence.', 'categorie' => 'VBG'],
            
            // VBG - Violences et Aide
            ['message' => 'Si tu subis des violences (physiques, sexuelles, psychologiques), CE N\'EST PAS TA FAUTE.', 'categorie' => 'VBG'],
            ['message' => 'Parle de ce que tu vis à un adulte de confiance ou appelle le 116 (ligne d\'aide gratuite).', 'categorie' => 'VBG'],
            ['message' => 'Utilise la fonction ALERTE de l\'app GquiOse pour signaler une violence en toute sécurité.', 'categorie' => 'VBG'],
            ['message' => 'En cas de viol ou agression sexuelle, consulte dans les 72h pour soins médicaux urgents.', 'categorie' => 'VBG'],
            ['message' => 'Garde les preuves en cas de violence : captures d\'écran, messages, vêtements, témoins.', 'categorie' => 'VBG'],
            
            // VBG - Cybersécurité
            ['message' => 'Ne partage JAMAIS de photos intimes, même à ton/ta partenaire de confiance.', 'categorie' => 'VBG'],
            ['message' => 'Protège tes comptes avec des mots de passe forts et ne les partage avec personne.', 'categorie' => 'VBG'],
            ['message' => 'Bloque et signale toute personne qui te harcèle en ligne.', 'categorie' => 'VBG'],
            
            // Autonomisation
            ['message' => 'Connais tes droits sexuels et reproductifs. Tu as le droit de choisir pour ton corps.', 'categorie' => 'Autonomisation'],
            ['message' => 'Le mariage avant 18 ans est INTERDIT. Tu as le droit de refuser un mariage forcé.', 'categorie' => 'Autonomisation'],
            ['message' => 'Tu as le droit d\'aller à l\'école et de poursuivre tes rêves, même si tu es une fille.', 'categorie' => 'Autonomisation'],
            ['message' => 'Ne laisse personne te dire que ton corps est honteux. Il est parfait tel qu\'il est.', 'categorie' => 'Autonomisation'],
        ];

        foreach ($conseils as $conseilData) {
            Conseil::updateOrCreate(
                ['message' => $conseilData['message']],
                ['categorie' => $conseilData['categorie']]
            );
        }

        $this->command->info('✅ ' . count($conseils) . ' conseils SSR/VBG pour jeunes créés avec catégories');
    }
}
