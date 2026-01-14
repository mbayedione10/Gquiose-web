<?php

namespace App\Console\Commands;

use App\Services\EvaluationTriggerService;
use Illuminate\Console\Command;

class TriggerPeriodicEvaluations extends Command
{
    protected $signature = 'evaluations:trigger-periodic';

    protected $description = 'Déclencher les évaluations périodiques pour tous les utilisateurs actifs';

    public function handle(EvaluationTriggerService $evaluationTriggerService)
    {
        $this->info('🔄 Déclenchement des évaluations périodiques...');

        $evaluationTriggerService->triggerPeriodicEvaluation();

        $this->info('✅ Évaluations périodiques déclenchées avec succès');

        return 0;
    }
}
