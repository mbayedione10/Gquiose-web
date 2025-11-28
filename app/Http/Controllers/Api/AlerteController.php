<?php

namespace App\Http\Controllers\Api;

use App\Models\Alerte;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\AlerteResource;
use App\Http\Resources\AlerteCollection;
use App\Http\Requests\AlerteStoreRequest;
use App\Http\Requests\AlerteUpdateRequest;
use Illuminate\Support\Facades\Storage;

class AlerteController extends Controller
{
    public function index(Request $request): AlerteCollection
    {
        $this->authorize('view-any', Alerte::class);

        $search = $request->get('search', '');

        $alertes = Alerte::search($search)
            ->latest()
            ->paginate();

        return new AlerteCollection($alertes);
    }

    public function store(AlerteStoreRequest $request): AlerteResource
    {
        $this->authorize('create', Alerte::class);

        $validated = $request->validated();

        // Gérer l'upload sécurisé des preuves
        if ($request->hasFile('preuves')) {
            $preuvePaths = [];

            foreach ($request->file('preuves') as $file) {
                // Stockage sécurisé dans un répertoire privé
                $path = $file->store('alertes/preuves', 'private');
                $preuvePaths[] = $path;
            }

            $validated['preuves'] = $preuvePaths;
        }

        // Générer automatiquement les conseils de sécurité
        $safetyAdviceService = new \App\Services\VBG\SafetyAdviceService();
        $validated['conseils_securite'] = $safetyAdviceService->generateSafetyAdvice(
            $validated['type_alerte_id'] ?? null,
            $validated['sous_type_violence_numerique_id'] ?? null
        );

        $alerte = Alerte::create($validated);

        return new AlerteResource($alerte);
    }

    public function show(Request $request, Alerte $alerte): AlerteResource
    {
        $this->authorize('view', $alerte);

        return new AlerteResource($alerte);
    }

    public function update(
        AlerteUpdateRequest $request,
        Alerte $alerte
    ): AlerteResource {
        $this->authorize('update', $alerte);

        $validated = $request->validated();

        $alerte->update($validated);

        return new AlerteResource($alerte);
    }

    public function destroy(Request $request, Alerte $alerte): Response
    {
        $this->authorize('delete', $alerte);

        $alerte->delete();

        return response()->noContent();
    }

    /**
     * Télécharger une preuve de manière sécurisée
     * Seul l'auteur de l'alerte et les admins peuvent télécharger les preuves
     */
    public function downloadEvidence(Request $request, Alerte $alerte, int $index)
    {
        $this->authorize('view', $alerte);

        // Vérifier que l'utilisateur est l'auteur ou un admin
        $user = $request->user();
        if ($user->id !== $alerte->utilisateur_id && !$user->hasRole('super-admin')) {
            abort(403, 'Accès non autorisé aux preuves');
        }

        if (!$alerte->preuves || !isset($alerte->preuves[$index])) {
            abort(404, 'Preuve non trouvée');
        }

        $filePath = $alerte->preuves[$index];

        if (!Storage::disk('private')->exists($filePath)) {
            abort(404, 'Fichier non trouvé');
        }

        return Storage::disk('private')->download($filePath);
    }

    /**
     * Marquer les conseils de sécurité comme lus
     */
    public function markAdviceAsRead(Request $request, Alerte $alerte)
    {
        $this->authorize('update', $alerte);

        $alerte->update(['conseils_lus' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Conseils marqués comme lus',
        ]);
    }
}
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
