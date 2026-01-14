<?php

namespace Database\Seeders;

use App\Models\CycleReminder;
use App\Models\CycleSetting;
use App\Models\CycleSymptom;
use App\Models\MenstrualCycle;
use App\Models\Utilisateur;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MenstrualCycleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer des utilisatrices (sexe féminin)
        $utilisatrices = Utilisateur::where('sexe', 'F')->limit(5)->get();

        if ($utilisatrices->isEmpty()) {
            $this->command->warn('Aucune utilisatrice trouvée. Créez d\'abord des utilisatrices.');

            return;
        }

        foreach ($utilisatrices as $utilisatrice) {
            // 1. Créer les paramètres du cycle
            CycleSetting::create([
                'utilisateur_id' => $utilisatrice->id,
                'average_cycle_length' => rand(26, 32),
                'average_period_length' => rand(4, 7),
                'track_temperature' => true,
                'track_symptoms' => true,
                'track_mood' => true,
                'track_sexual_activity' => rand(0, 1),
                'notifications_enabled' => true,
            ]);

            // 2. Créer des cycles passés (3 derniers cycles)
            for ($i = 3; $i > 0; $i--) {
                $periodStart = Carbon::now()->subMonths($i)->subDays(rand(0, 5));
                $cycleLength = rand(26, 32);
                $periodLength = rand(4, 7);
                $periodEnd = $periodStart->copy()->addDays($periodLength - 1);

                $cycle = MenstrualCycle::create([
                    'utilisateur_id' => $utilisatrice->id,
                    'period_start_date' => $periodStart,
                    'period_end_date' => $periodEnd,
                    'cycle_length' => $cycleLength,
                    'period_length' => $periodLength,
                    'flow_intensity' => ['leger', 'modere', 'abondant'][rand(0, 2)],
                    'is_active' => false,
                    'notes' => $this->getRandomNote(),
                ]);

                // Calculer les prédictions
                $cycle->next_period_prediction = $periodStart->copy()->addDays($cycleLength);
                $cycle->ovulation_prediction = $cycle->next_period_prediction->copy()->subDays(14);
                $cycle->fertile_window_start = $cycle->ovulation_prediction->copy()->subDays(5);
                $cycle->fertile_window_end = $cycle->ovulation_prediction->copy()->addDay();
                $cycle->save();

                // Ajouter des symptômes pour ce cycle
                $this->createSymptomsForCycle($cycle);
            }

            // 3. Créer le cycle actif
            $currentPeriodStart = Carbon::now()->subDays(rand(2, 5));
            $activeCycle = MenstrualCycle::create([
                'utilisateur_id' => $utilisatrice->id,
                'period_start_date' => $currentPeriodStart,
                'period_end_date' => null,
                'cycle_length' => null,
                'period_length' => null,
                'flow_intensity' => ['leger', 'modere', 'abondant'][rand(0, 2)],
                'is_active' => true,
                'notes' => $this->getRandomNote(),
            ]);

            $activeCycle->calculatePredictions(
                CycleSetting::where('utilisateur_id', $utilisatrice->id)->first()->average_cycle_length,
                CycleSetting::where('utilisateur_id', $utilisatrice->id)->first()->average_period_length
            );

            // Ajouter des symptômes pour le cycle actif
            $this->createSymptomsForCycle($activeCycle, true);

            // 4. Créer des rappels
            $this->createReminders($utilisatrice->id);
        }

        $this->command->info('Cycles menstruels créés avec succès!');
    }

    /**
     * Créer des symptômes pour un cycle
     */
    private function createSymptomsForCycle(MenstrualCycle $cycle, bool $isActive = false)
    {
        $startDate = Carbon::parse($cycle->period_start_date);
        $endDate = $isActive ? Carbon::now() : Carbon::parse($cycle->period_end_date ?? $startDate->copy()->addDays(5));

        $physicalSymptoms = ['crampes', 'fatigue', 'maux_tete', 'nausee', 'sensibilite_seins', 'ballonnements', 'douleurs_dos', 'acne'];
        $moods = ['joyeuse', 'triste', 'irritable', 'anxieuse', 'calme', 'energique', 'stresse'];
        $dischargeTypes = ['aucune', 'creamy', 'sticky', 'watery', 'egg_white'];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            // Pas de symptôme tous les jours (70% de chance)
            if (rand(1, 10) <= 7) {
                $selectedSymptoms = [];
                $selectedMoods = [];

                // Sélectionner 2-4 symptômes physiques aléatoires
                $numSymptoms = rand(2, 4);
                for ($i = 0; $i < $numSymptoms; $i++) {
                    $selectedSymptoms[] = $physicalSymptoms[array_rand($physicalSymptoms)];
                }
                $selectedSymptoms = array_unique($selectedSymptoms);

                // Sélectionner 1-3 humeurs aléatoires
                $numMoods = rand(1, 3);
                for ($i = 0; $i < $numMoods; $i++) {
                    $selectedMoods[] = $moods[array_rand($moods)];
                }
                $selectedMoods = array_unique($selectedMoods);

                CycleSymptom::create([
                    'utilisateur_id' => $cycle->utilisateur_id,
                    'menstrual_cycle_id' => $cycle->id,
                    'symptom_date' => $currentDate->format('Y-m-d'),
                    'physical_symptoms' => $selectedSymptoms,
                    'pain_level' => rand(0, 8),
                    'mood' => $selectedMoods,
                    'discharge_type' => $dischargeTypes[array_rand($dischargeTypes)],
                    'temperature' => rand(0, 1) ? round(36 + (rand(0, 15) / 10), 2) : null,
                    'sexual_activity' => rand(0, 10) > 7,
                    'contraception_used' => rand(0, 1),
                    'notes' => rand(0, 10) > 7 ? $this->getRandomSymptomNote() : null,
                ]);
            }

            $currentDate->addDay();
        }
    }

    /**
     * Créer des rappels pour une utilisatrice
     */
    private function createReminders($utilisateurId)
    {
        $reminders = [
            [
                'reminder_type' => 'period_approaching',
                'reminder_time' => '09:00',
                'enabled' => true,
                'days_before' => [2, 1],
            ],
            [
                'reminder_type' => 'period_today',
                'reminder_time' => '08:00',
                'enabled' => true,
                'days_before' => null,
            ],
            [
                'reminder_type' => 'ovulation_approaching',
                'reminder_time' => '10:00',
                'enabled' => true,
                'days_before' => [2],
            ],
            [
                'reminder_type' => 'fertile_window',
                'reminder_time' => '09:00',
                'enabled' => rand(0, 1),
                'days_before' => null,
            ],
            [
                'reminder_type' => 'log_symptoms',
                'reminder_time' => '20:00',
                'enabled' => true,
                'days_before' => null,
            ],
        ];

        foreach ($reminders as $reminder) {
            CycleReminder::create([
                'utilisateur_id' => $utilisateurId,
                'reminder_type' => $reminder['reminder_type'],
                'reminder_time' => $reminder['reminder_time'],
                'enabled' => $reminder['enabled'],
                'days_before' => $reminder['days_before'],
            ]);
        }
    }

    /**
     * Notes aléatoires pour les cycles
     */
    private function getRandomNote()
    {
        $notes = [
            'Cycle normal',
            'Flux plus abondant que d\'habitude',
            'Légères crampes',
            'Beaucoup de fatigue',
            'Cycle régulier',
            null,
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Notes aléatoires pour les symptômes
     */
    private function getRandomSymptomNote()
    {
        $notes = [
            'Journée difficile',
            'Douleurs intenses',
            'Sensation de ballonnement',
            'Beaucoup d\'énergie aujourd\'hui',
            'Humeur changeante',
            'Bonne journée',
        ];

        return $notes[array_rand($notes)];
    }
}
