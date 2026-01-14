<?php

namespace App\Services;

use App\Models\Evaluation;
use App\Models\NotificationSchedule;
use App\Models\Utilisateur;

class EvaluationTriggerService
{
    /**
     * Déclencher une évaluation automatique
     */
    public function triggerAutoEvaluation(string $contexte, int $contexteId, array $options = [])
    {
        $delayDays = $options['delay_days'] ?? 7;
        $targetUsers = $options['target_users'] ?? null;
        $evaluationType = $options['evaluation_type'] ?? 'generale';

        // Déterminer les utilisateurs cibles
        $users = $targetUsers ?? $this->getTargetUsers($contexte, $contexteId);

        foreach ($users as $userId) {
            // Vérifier si l'utilisateur n'a pas déjà été évalué
            $existingEvaluation = Evaluation::where('utilisateur_id', $userId)
                ->where('contexte', $contexte)
                ->where('contexte_id', $contexteId)
                ->where('created_at', '>', now()->subDays(30))
                ->exists();

            if (! $existingEvaluation) {
                NotificationSchedule::create([
                    'template_id' => $this->getEvaluationTemplateId($evaluationType),
                    'utilisateur_id' => $userId,
                    'type' => 'evaluation',
                    'contexte' => $contexte,
                    'contexte_id' => $contexteId,
                    'scheduled_at' => now()->addDays($delayDays),
                    'status' => 'pending',
                    'metadata' => [
                        'auto_triggered' => true,
                        'evaluation_type' => $evaluationType,
                        'trigger_context' => $contexte,
                    ],
                ]);
            }
        }
    }

    /**
     * Déclencher évaluation après quiz
     */
    public function triggerPostQuizEvaluation(int $quizId, int $userId)
    {
        $this->triggerAutoEvaluation('quiz', $quizId, [
            'delay_days' => 7,
            'target_users' => [$userId],
            'evaluation_type' => 'satisfaction_quiz',
        ]);
    }

    /**
     * Déclencher évaluation après lecture d'article
     */
    public function triggerPostArticleEvaluation(int $articleId, int $userId)
    {
        $this->triggerAutoEvaluation('article', $articleId, [
            'delay_days' => 1,
            'target_users' => [$userId],
            'evaluation_type' => 'satisfaction_article',
        ]);
    }

    /**
     * Déclencher évaluation structure après visite
     */
    public function triggerPostStructureEvaluation(int $structureId, int $userId)
    {
        $this->triggerAutoEvaluation('structure', $structureId, [
            'delay_days' => 3,
            'target_users' => [$userId],
            'evaluation_type' => 'satisfaction_structure',
        ]);
    }

    /**
     * Déclencher évaluation périodique générale
     */
    public function triggerPeriodicEvaluation()
    {
        $users = Utilisateur::where('status', true)->pluck('id');

        foreach ($users as $userId) {
            // Évaluation générale tous les 30 jours
            $lastEvaluation = Evaluation::where('utilisateur_id', $userId)
                ->where('contexte', 'generale')
                ->latest()
                ->first();

            if (! $lastEvaluation || $lastEvaluation->created_at->lt(now()->subDays(30))) {
                NotificationSchedule::create([
                    'template_id' => $this->getEvaluationTemplateId('generale'),
                    'utilisateur_id' => $userId,
                    'type' => 'evaluation',
                    'contexte' => 'generale',
                    'contexte_id' => null,
                    'scheduled_at' => now(),
                    'status' => 'pending',
                    'metadata' => [
                        'auto_triggered' => true,
                        'evaluation_type' => 'periodic',
                    ],
                ]);
            }
        }
    }

    private function getTargetUsers(string $contexte, int $contexteId)
    {
        // Logique pour déterminer les utilisateurs selon le contexte
        return Utilisateur::where('status', true)->pluck('id');
    }

    private function getEvaluationTemplateId(string $type)
    {
        // Retourner l'ID du template de notification approprié
        return 1; // À adapter selon vos templates
    }
}
