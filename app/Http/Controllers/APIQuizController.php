<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Response;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class APIQuizController extends Controller
{
    /**
     * Trouve un utilisateur par user_id, email ou phone
     *
     * @param Request $request
     * @return Utilisateur|null
     */
    private function findUser(Request $request): ?Utilisateur
    {
        if ($request->filled('user_id')) {
            return Utilisateur::find($request->user_id);
        }

        if ($request->filled('email')) {
            return Utilisateur::where('email', $request->email)->first();
        }

        if ($request->filled('phone')) {
            return Utilisateur::where('phone', $request->phone)->first();
        }

        return null;
    }

    /**
     * Synchronise les réponses envoyées par le client
     *
     * Exemples d'entrée:
     * {
     *   "email": "nioulboy@gmail.com",
     *   "responses": [
     *     {"questionId": 6, "reponse": "option1"},
     *     {"questionId": 17, "reponse": "reponse"}
     *   ]
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(Request $request)
    {
        // Validation minimale
        $validator = Validator::make($request->all(), [
            'responses' => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422);
        }

        // Vérifier qu'au moins un identifiant est fourni
        if (!$request->filled('user_id') && !$request->filled('email') && !$request->filled('phone')) {
            return ApiResponse::error('Au moins un identifiant est requis: user_id, email ou phone', 422);
        }

        // Trouver l'utilisateur
        $user = $this->findUser($request);

        if (!$user) {
            return ApiResponse::error('Utilisateur non trouvé', 404);
        }

        // Accepter array ou JSON string
        $responsesData = $request->input('responses');
        if (is_string($responsesData)) {
            $data = json_decode($responsesData, true);
        } else {
            $data = $responsesData;
        }

        if (!is_array($data)) {
            return ApiResponse::error('Format de réponses invalide', 422);
        }

        // Préparer
        $savedCount = 0;
        $errors = [];

        // Extraire et normaliser les questionId en une seule passe
        $questionIds = array_values(array_unique(array_filter(array_map(function ($item) {
            return isset($item['questionId']) ? (int) $item['questionId'] : null;
        }, $data))));

        // Charger en bulk les questions existantes et les indexer par id
        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');

        // Commencer transaction pour garantir atomicité (optionnel mais recommandé)
        DB::beginTransaction();

        try {
            foreach ($data as $index => $item) {
                $questionId = $item['questionId'] ?? null;
                $rawUserResponse = $item['reponse'] ?? null;

                if ($questionId === null) {
                    $errors[] = [
                        'index' => $index,
                        'error' => 'questionId manquant'
                    ];
                    continue;
                }

                if ($rawUserResponse === null) {
                    $errors[] = [
                        'questionId' => $questionId,
                        'error' => 'reponse manquante'
                    ];
                    continue;
                }

                $question = $questions->get((int)$questionId);

                if (!$question) {
                    $errors[] = [
                        'questionId' => $questionId,
                        'error' => 'Question introuvable'
                    ];
                    continue;
                }

                // Normaliser la réponse envoyée (option1, reponse, etc.)
                $userResponse = strtolower(trim($rawUserResponse));
                $actualResponseValue = null;

                if ($userResponse === 'reponse') {
                    $actualResponseValue = $question->reponse;
                } elseif (Str::startsWith($userResponse, 'option')) {
                    // Récupérer les attributs Eloquent : plus fiable que property_exists
                    $attrs = $question->getAttributes();

                    Log::info("Checking option: {$userResponse} for question {$question->id}");
                    Log::debug('Question attributes keys: ' . implode(', ', array_keys($attrs)));

                    if (array_key_exists($userResponse, $attrs) && $attrs[$userResponse] !== null && $attrs[$userResponse] !== '') {
                        $actualResponseValue = $attrs[$userResponse];
                        Log::info("Valid option, value: {$actualResponseValue}");
                    } else {
                        $errors[] = [
                            'questionId' => $questionId,
                            'error' => "Option invalide: {$userResponse} n'existe pas ou est vide pour cette question"
                        ];
                        continue;
                    }
                } else {
                    $errors[] = [
                        'questionId' => $questionId,
                        'error' => "Format de réponse invalide: {$rawUserResponse}. Utilisez 'option1', 'option2', etc., ou 'reponse'"
                    ];
                    continue;
                }

                // Enregistrer la réponse : prévenir doublons (utilisateur + question)
                try {
                    // on utilise updateOrCreate pour éviter les doublons et permettre la resynchronisation
                    $saved = Response::updateOrCreate(
                        [
                            'utilisateur_id' => $user->id,
                            'question_id' => $question->id,
                        ],
                        [
                            'reponse' => $actualResponseValue,
                            // comparaison permissive (==) pour tolérer types différents
                            'isValid' => ($actualResponseValue == $question->reponse)
                        ]
                    );

                    // Si updateOrCreate a créé ou mis à jour, compter comme sauvegarde réussie
                    if ($saved) {
                        $savedCount++;
                    }
                } catch (\Exception $e) {
                    Log::error('Erreur lors de la sauvegarde de la réponse: ' . $e->getMessage(), [
                        'questionId' => $questionId,
                        'userId' => $user->id,
                        'payload' => $item
                    ]);
                    $errors[] = [
                        'questionId' => $questionId,
                        'error' => 'Erreur serveur lors de la sauvegarde'
                    ];
                    continue;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur globale lors de la synchronisation des réponses: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return ApiResponse::error('Erreur serveur lors de la synchronisation', 500);
        }

        $response = [
            'message' => 'Réponses synchronisées avec succès',
            'saved_count' => $savedCount
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return ApiResponse::success($response);
    }
}
