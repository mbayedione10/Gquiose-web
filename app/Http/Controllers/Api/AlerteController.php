<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerte;
use App\Services\VBG\EvidenceSecurityService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AlerteController extends Controller
{
    protected $evidenceService;

    public function __construct(EvidenceSecurityService $evidenceService)
    {
        $this->evidenceService = $evidenceService;
    }

    /**
     * Téléchargement sécurisé d'une preuve
     * GET /api/v1/alertes/{alerte}/evidence/{index}
     */
    public function downloadEvidence(Request $request, Alerte $alerte, int $index): Response|StreamedResponse
    {
        // Vérifier que l'utilisateur a le droit d'accéder à cette preuve
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        // Autoriser uniquement : propriétaire de l'alerte ou admin
        if ($alerte->utilisateur_id !== $user->id && !$user->hasRole('super-admin')) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        // Vérifier que l'index existe
        $preuves = $alerte->preuves ?? [];
        
        if (!isset($preuves[$index])) {
            return response()->json(['error' => 'Preuve introuvable'], 404);
        }

        $preuve = $preuves[$index];
        
        // Enregistrer l'accès dans les logs (audit trail)
        \Log::info('Accès preuve', [
            'alerte_id' => $alerte->id,
            'user_id' => $user->id,
            'evidence_index' => $index,
            'timestamp' => now()->toDateTimeString()
        ]);

        // Déchiffrer et retourner le fichier
        $decryptedContent = $this->evidenceService->retrieveEvidence($preuve['path']);
        
        if (!$decryptedContent) {
            return response()->json(['error' => 'Erreur de déchiffrement'], 500);
        }

        return response()->streamDownload(function () use ($decryptedContent) {
            echo $decryptedContent;
        }, $preuve['original_name'], [
            'Content-Type' => $preuve['type'],
            'Content-Length' => strlen($decryptedContent),
        ]);
    }

    /**
     * Marquer les conseils comme lus
     * POST /api/v1/alertes/{alerte}/mark-advice-read
     */
    public function markAdviceAsRead(Request $request, Alerte $alerte)
    {
        $user = $request->user();

        if (!$user || $alerte->utilisateur_id !== $user->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $alerte->conseils_lus = true;
        $alerte->save();

        return response()->json([
            'success' => true,
            'message' => 'Conseils marqués comme lus'
        ]);
    }
}
