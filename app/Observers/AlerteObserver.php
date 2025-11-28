<?php

namespace App\Observers;

use App\Models\Alerte;

class AlerteObserver
{
    /**
     * Handle the Alerte "creating" event.
     * Génère automatiquement un numéro de suivi unique avant la création
     */
    public function creating(Alerte $alerte): void
    {
        // Générer le numéro de suivi seulement s'il n'existe pas déjà
        if (empty($alerte->numero_suivi)) {
            $alerte->numero_suivi = $this->generateNumeroSuivi();
        }
    }

    /**
     * Génère un numéro de suivi unique au format VBG-YYYY-XXXXX
     * Exemple : VBG-2025-00123
     */
    private function generateNumeroSuivi(): string
    {
        $year = date('Y');
        $prefix = "VBG-{$year}-";

        // Récupérer le dernier numéro de suivi de l'année en cours
        $lastAlerte = Alerte::where('numero_suivi', 'like', "{$prefix}%")
            ->orderBy('numero_suivi', 'desc')
            ->first();

        if ($lastAlerte) {
            // Extraire le numéro incrémental du dernier signalement
            $lastNumber = (int) substr($lastAlerte->numero_suivi, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            // Premier signalement de l'année
            $nextNumber = 1;
        }

        // Format sur 5 chiffres : 00001, 00002, etc.
        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
<?php

namespace App\Observers;

use App\Models\Alerte;
use App\Services\VBG\EvidenceSecurityService;

class AlerteObserver
{
    protected $evidenceService;

    public function __construct(EvidenceSecurityService $evidenceService)
    {
        $this->evidenceService = $evidenceService;
    }

    /**
     * Suppression sécurisée des preuves lors de la suppression d'une alerte
     */
    public function deleting(Alerte $alerte)
    {
        // Supprimer toutes les preuves chiffrées de manière sécurisée
        if ($alerte->preuves && is_array($alerte->preuves)) {
            $this->evidenceService->deleteAllEvidences($alerte->preuves);
            
            \Log::info('Preuves supprimées pour alerte', [
                'alerte_id' => $alerte->id,
                'ref' => $alerte->ref,
                'nombre_preuves' => count($alerte->preuves),
                'timestamp' => now()->toDateTimeString()
            ]);
        }
    }
}
