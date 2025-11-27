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
            'Utilise un préservatif à CHAQUE rapport pour te protéger contre les IST et les grossesses non désirées.',
            'Les préservatifs sont gratuits dans les centres de santé. N\'hésite pas à en demander!',
            'La pilule contraceptive ne protège PAS contre les IST. Utilise aussi un préservatif.',
            'En cas de rapport non protégé, consulte dans les 72h pour une contraception d\'urgence (pilule du lendemain).',
            'Fais-toi dépister régulièrement pour les IST, c\'est gratuit, rapide et confidentiel.',
            
            // SSR - Cycle et Règles
            'Utilise l\'app GquiOse pour suivre ton cycle menstruel et anticiper tes prochaines règles.',
            'Change de serviette hygiénique toutes les 3-4 heures pour éviter les infections.',
            'Pendant tes règles, tu peux faire du sport, te baigner et mener une vie normale.',
            'Des douleurs intenses pendant les règles ne sont pas normales. Consulte un professionnel.',
            
            // SSR - Information et Droits
            'Tu as le droit d\'accéder aux services de santé sexuelle GRATUITEMENT et en toute CONFIDENTIALITÉ.',
            'Informe-toi sur la contraception AVANT ta première fois, pas après.',
            'Pose toutes tes questions sur la sexualité aux professionnels de santé, aucune question n\'est bête.',
            
            // VBG - Consentement
            'Ton corps t\'appartient. Tu as le droit de dire NON à tout moment.',
            'Le consentement doit être libre, éclairé et enthousiaste. Si tu doutes, c\'est NON.',
            'Un.e partenaire qui respecte ne te force jamais et accepte ton NON sans discuter.',
            
            // VBG - Relations Saines
            'Une relation amoureuse saine est basée sur le respect, la confiance et l\'égalité.',
            'Si ton/ta partenaire te contrôle, t\'isole ou te rabaisse, c\'est de la violence, pas de l\'amour.',
            'La jalousie excessive n\'est PAS une preuve d\'amour, c\'est un signe de violence.',
            
            // VBG - Violences et Aide
            'Si tu subis des violences (physiques, sexuelles, psychologiques), CE N\'EST PAS TA FAUTE.',
            'Parle de ce que tu vis à un adulte de confiance ou appelle le 116 (ligne d\'aide gratuite).',
            'Utilise la fonction ALERTE de l\'app GquiOse pour signaler une violence en toute sécurité.',
            'En cas de viol ou agression sexuelle, consulte dans les 72h pour soins médicaux urgents.',
            'Garde les preuves en cas de violence : captures d\'écran, messages, vêtements, témoins.',
            
            // VBG - Cybersécurité
            'Ne partage JAMAIS de photos intimes, même à ton/ta partenaire de confiance.',
            'Protège tes comptes avec des mots de passe forts et ne les partage avec personne.',
            'Bloque et signale toute personne qui te harcèle en ligne.',
            
            // Autonomisation
            'Connais tes droits sexuels et reproductifs. Tu as le droit de choisir pour ton corps.',
            'Le mariage avant 18 ans est INTERDIT. Tu as le droit de refuser un mariage forcé.',
            'Tu as le droit d\'aller à l\'école et de poursuivre tes rêves, même si tu es une fille.',
            'Ne laisse personne te dire que ton corps est honteux. Il est parfait tel qu\'il est.',
        ];

        foreach ($conseils as $message) {
            Conseil::firstOrCreate(['message' => $message]);
        }

        $this->command->info('✅ ' . count($conseils) . ' conseils SSR/VBG pour jeunes créés');
    }
}
