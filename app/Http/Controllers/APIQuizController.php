<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Response;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Validator;

class APIQuizController extends Controller
{
    /**
     * Trouve un utilisateur par user_id, email ou phone
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

    public function sync(Request $request)
    {
        // Validation
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

        $savedCount = 0;

        for ($i = 0; $i < count($data); $i++)
        {
            $question = Question::whereId($data[$i]['questionId'])->first();

            if ($question != null)
            {
                $reponse = new Response();
                $reponse->question_id = $question->id;
                $reponse->utilisateur_id = $user->id;
                $reponse->reponse = $data[$i]['reponse'];
                $reponse->isValid = $data[$i]['valid'];
                $reponse->save();
                $savedCount++;
            }
        }

        return ApiResponse::success([
            'message' => 'Réponses synchronisées avec succès',
            'saved_count' => $savedCount
        ]);
    }
}
