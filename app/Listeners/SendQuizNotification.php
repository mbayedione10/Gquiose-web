<?php

namespace App\Listeners;

use App\Events\NewQuizPublished;
use App\Services\EvaluationTriggerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendQuizNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $evaluationTriggerService;

    public function __construct(EvaluationTriggerService $evaluationTriggerService)
    {
        $this->evaluationTriggerService = $evaluationTriggerService;
    }

    public function handle(NewQuizPublished $event)
    {
        $thematique = $event->thematique;

        Log::info("Event triggered: New quiz published - {$thematique->titre}");

        // Déclencher automatiquement une évaluation 7 jours après le quiz
        // pour tous les utilisateurs qui ont répondu au quiz
        $userIds = \App\Models\Response::where('question_id', function ($query) use ($thematique) {
            $query->select('id')
                ->from('questions')
                ->where('thematique_id', $thematique->id);
        })->distinct()->pluck('utilisateur_id')->toArray();

        if (! empty($userIds)) {
            $this->evaluationTriggerService->triggerAutoEvaluation('quiz', $thematique->id, [
                'delay_days' => 7,
                'target_users' => $userIds,
                'evaluation_type' => 'satisfaction_quiz',
            ]);

            Log::info('Quiz evaluation scheduled for '.count($userIds).' users');
        }
    }
}
