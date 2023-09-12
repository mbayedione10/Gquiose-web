<?php

namespace App\Http\Controllers\Api;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\ResponseCollection;

class UtilisateurResponsesController extends Controller
{
    public function index(
        Request $request,
        Utilisateur $utilisateur
    ): ResponseCollection {
        $this->authorize('view', $utilisateur);

        $search = $request->get('search', '');

        $responses = $utilisateur
            ->responses()
            ->search($search)
            ->latest()
            ->paginate();

        return new ResponseCollection($responses);
    }

    public function store(
        Request $request,
        Utilisateur $utilisateur
    ): ResponseResource {
        $this->authorize('create', Response::class);

        $validated = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'reponse' => ['required', 'max:255', 'string'],
            'isValid' => ['required', 'boolean'],
        ]);

        $response = $utilisateur->responses()->create($validated);

        return new ResponseResource($response);
    }
}
