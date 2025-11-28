
<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\MenstrualCycle;
use App\Models\CycleSymptom;
use App\Models\CycleSetting;
use App\Models\CycleReminder;
use App\Models\Utilisateur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class APICycleController extends Controller
{
    /**
     * Démarrer un nouveau cycle (début des règles)
     * POST /api/v1/cycle/start
     */
    public function startPeriod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:utilisateurs,id',
            'period_start_date' => 'required|date',
            'flow_intensity' => 'nullable|in:leger,modere,abondant',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 400);
        }

        $user = Utilisateur::find($request->user_id);
        
        // Récupérer ou créer les paramètres
        $settings = CycleSetting::firstOrCreate(
            ['utilisateur_id' => $user->id],
            [
                'average_cycle_length' => 28,
                'average_period_length' => 5,
            ]
        );

        DB::beginTransaction();
        try {
            // Désactiver le cycle précédent s'il existe
            $previousCycle = MenstrualCycle::where('utilisateur_id', $user->id)
                ->where('is_active', true)
                ->first();

            if ($previousCycle) {
                // Calculer la durée du cycle si on démarre un nouveau
                $previousCycle->cycle_length = Carbon::parse($previousCycle->period_start_date)
                    ->diffInDays(Carbon::parse($request->period_start_date));
                $previousCycle->is_active = false;
                $previousCycle->save();

                // Mettre à jour la moyenne si on a assez de données
                $this->updateAverages($user->id);
            }

            // Créer le nouveau cycle
            $cycle = MenstrualCycle::create([
                'utilisateur_id' => $user->id,
                'period_start_date' => $request->period_start_date,
                'flow_intensity' => $request->flow_intensity,
                'notes' => $request->notes,
                'is_active' => true,
            ]);

            // Calculer les prédictions
            $cycle->calculatePredictions(
                $settings->average_cycle_length,
                $settings->average_period_length
            );

            DB::commit();

            return ApiResponse::success([
                'cycle' => $cycle,
                'message' => 'Cycle démarré avec succès',
                'predictions' => [
                    'next_period' => $cycle->next_period_prediction?->format('Y-m-d'),
                    'ovulation' => $cycle->ovulation_prediction?->format('Y-m-d'),
                    'fertile_window' => [
                        'start' => $cycle->fertile_window_start?->format('Y-m-d'),
                        'end' => $cycle->fertile_window_end?->format('Y-m-d'),
                    ],
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Erreur lors du démarrage du cycle: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Terminer la période (fin des règles)
     * POST /api/v1/cycle/end-period
     */
    public function endPeriod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:utilisateurs,id',
            'period_end_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 400);
        }

        $cycle = MenstrualCycle::where('utilisateur_id', $request->user_id)
            ->where('is_active', true)
            ->whereNull('period_end_date')
            ->first();

        if (!$cycle) {
            return ApiResponse::error('Aucun cycle actif trouvé', 404);
        }

        $cycle->period_end_date = $request->period_end_date;
        $cycle->period_length = Carbon::parse($cycle->period_start_date)
            ->diffInDays(Carbon::parse($request->period_end_date)) + 1;
        $cycle->save();

        return ApiResponse::success([
            'cycle' => $cycle,
            'message' => 'Période terminée',
            'period_length' => $cycle->period_length . ' jours',
        ]);
    }

    /**
     * Enregistrer les symptômes quotidiens
     * POST /api/v1/cycle/log-symptoms
     */
    public function logSymptoms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:utilisateurs,id',
            'symptom_date' => 'required|date',
            'physical_symptoms' => 'nullable|array',
            'physical_symptoms.*' => 'string|in:crampes,fatigue,maux_tete,nausee,sensibilite_seins,ballonnements,douleurs_dos,acne',
            'pain_level' => 'nullable|integer|min:0|max:10',
            'mood' => 'nullable|array',
            'mood.*' => 'string|in:joyeuse,triste,irritable,anxieuse,calme,energique,stresse',
            'discharge_type' => 'nullable|in:aucune,creamy,sticky,watery,egg_white',
            'temperature' => 'nullable|numeric|min:35|max:42',
            'sexual_activity' => 'nullable|boolean',
            'contraception_used' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 400);
        }

        // Trouver le cycle actif
        $activeCycle = MenstrualCycle::where('utilisateur_id', $request->user_id)
            ->where('is_active', true)
            ->first();

        $symptom = CycleSymptom::updateOrCreate(
            [
                'utilisateur_id' => $request->user_id,
                'symptom_date' => $request->symptom_date,
            ],
            [
                'menstrual_cycle_id' => $activeCycle?->id,
                'physical_symptoms' => $request->physical_symptoms,
                'pain_level' => $request->pain_level,
                'mood' => $request->mood,
                'discharge_type' => $request->discharge_type,
                'temperature' => $request->temperature,
                'sexual_activity' => $request->sexual_activity ?? false,
                'contraception_used' => $request->contraception_used ?? false,
                'notes' => $request->notes,
            ]
        );

        return ApiResponse::success([
            'symptom' => $symptom,
            'message' => 'Symptômes enregistrés avec succès',
        ]);
    }

    /**
     * Obtenir le cycle actuel et les prédictions
     * GET /api/v1/cycle/current/{user_id}
     */
    public function getCurrentCycle($userId)
    {
        $user = Utilisateur::find($userId);
        if (!$user) {
            return ApiResponse::error('Utilisateur introuvable', 404);
        }

        $cycle = MenstrualCycle::where('utilisateur_id', $userId)
            ->where('is_active', true)
            ->with('symptoms')
            ->first();

        $settings = CycleSetting::where('utilisateur_id', $userId)->first();

        if (!$cycle) {
            return ApiResponse::success([
                'cycle' => null,
                'settings' => $settings,
                'message' => 'Aucun cycle actif. Commence ton suivi !',
            ]);
        }

        // Statut actuel
        $today = Carbon::today();
        $status = 'normal';
        $daysUntilPeriod = $cycle->daysUntilNextPeriod();

        if ($cycle->period_start_date && !$cycle->period_end_date) {
            $status = 'period'; // En période de règles
        } elseif ($cycle->isInFertileWindow()) {
            $status = 'fertile'; // Fenêtre fertile
        } elseif ($daysUntilPeriod !== null && $daysUntilPeriod <= 3 && $daysUntilPeriod >= 0) {
            $status = 'pms'; // Syndrome prémenstruel
        }

        return ApiResponse::success([
            'cycle' => $cycle,
            'settings' => $settings,
            'status' => $status,
            'days_until_next_period' => $daysUntilPeriod,
            'in_fertile_window' => $cycle->isInFertileWindow(),
        ]);
    }

    /**
     * Obtenir l'historique des cycles
     * GET /api/v1/cycle/history/{user_id}
     */
    public function getHistory($userId, Request $request)
    {
        $limit = $request->input('limit', 6);

        $cycles = MenstrualCycle::where('utilisateur_id', $userId)
            ->orderBy('period_start_date', 'desc')
            ->limit($limit)
            ->get();

        return ApiResponse::success([
            'cycles' => $cycles,
            'count' => $cycles->count(),
        ]);
    }

    /**
     * Obtenir les symptômes d'une période
     * GET /api/v1/cycle/symptoms/{user_id}
     */
    public function getSymptoms($userId, Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $symptoms = CycleSymptom::where('utilisateur_id', $userId)
            ->whereBetween('symptom_date', [$startDate, $endDate])
            ->orderBy('symptom_date', 'desc')
            ->get();

        return ApiResponse::success([
            'symptoms' => $symptoms,
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
        ]);
    }

    /**
     * Mettre à jour les paramètres du cycle
     * POST /api/v1/cycle/settings
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:utilisateurs,id',
            'average_cycle_length' => 'nullable|integer|min:21|max:35',
            'average_period_length' => 'nullable|integer|min:2|max:10',
            'track_temperature' => 'nullable|boolean',
            'track_symptoms' => 'nullable|boolean',
            'track_mood' => 'nullable|boolean',
            'track_sexual_activity' => 'nullable|boolean',
            'notifications_enabled' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 400);
        }

        $settings = CycleSetting::updateOrCreate(
            ['utilisateur_id' => $request->user_id],
            $request->only([
                'average_cycle_length',
                'average_period_length',
                'track_temperature',
                'track_symptoms',
                'track_mood',
                'track_sexual_activity',
                'notifications_enabled',
            ])
        );

        // Recalculer les prédictions du cycle actif
        $activeCycle = MenstrualCycle::where('utilisateur_id', $request->user_id)
            ->where('is_active', true)
            ->first();

        if ($activeCycle) {
            $activeCycle->calculatePredictions(
                $settings->average_cycle_length,
                $settings->average_period_length
            );
        }

        return ApiResponse::success([
            'settings' => $settings,
            'message' => 'Paramètres mis à jour',
        ]);
    }

    /**
     * Configurer les rappels
     * POST /api/v1/cycle/reminders
     */
    public function configureReminders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:utilisateurs,id',
            'reminders' => 'required|array',
            'reminders.*.type' => 'required|in:period_approaching,period_today,ovulation_approaching,fertile_window,log_symptoms,pill_reminder',
            'reminders.*.time' => 'required|date_format:H:i',
            'reminders.*.enabled' => 'required|boolean',
            'reminders.*.days_before' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 400);
        }

        DB::beginTransaction();
        try {
            // Supprimer les anciens rappels
            CycleReminder::where('utilisateur_id', $request->user_id)->delete();

            // Créer les nouveaux
            foreach ($request->reminders as $reminder) {
                CycleReminder::create([
                    'utilisateur_id' => $request->user_id,
                    'reminder_type' => $reminder['type'],
                    'reminder_time' => $reminder['time'],
                    'enabled' => $reminder['enabled'],
                    'days_before' => $reminder['days_before'] ?? null,
                ]);
            }

            DB::commit();

            return ApiResponse::success([
                'message' => 'Rappels configurés avec succès',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Erreur: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Mettre à jour les moyennes du cycle
     */
    protected function updateAverages($userId)
    {
        $recentCycles = MenstrualCycle::where('utilisateur_id', $userId)
            ->where('is_active', false)
            ->whereNotNull('cycle_length')
            ->whereNotNull('period_length')
            ->orderBy('period_start_date', 'desc')
            ->limit(3)
            ->get();

        if ($recentCycles->count() >= 2) {
            $avgCycleLength = round($recentCycles->avg('cycle_length'));
            $avgPeriodLength = round($recentCycles->avg('period_length'));

            CycleSetting::updateOrCreate(
                ['utilisateur_id' => $userId],
                [
                    'average_cycle_length' => $avgCycleLength,
                    'average_period_length' => $avgPeriodLength,
                ]
            );
        }
    }
}
