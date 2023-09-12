<?php

namespace App\Http\Controllers\Api;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\ResponseCollection;

class QuestionResponsesController extends Controller
{
    public function index(
        Request $request,
        Question $question
    ): ResponseCollection {
        $this->authorize('view', $question);

        $search = $request->get('search', '');

        $responses = $question
            ->responses()
            ->search($search)
            ->latest()
            ->paginate();

        return new ResponseCollection($responses);
    }

    public function store(
        Request $request,
        Question $question
    ): ResponseResource {
        $this->authorize('create', Response::class);

        $validated = $request->validate([
            'reponse' => ['required', 'max:255', 'string'],
            'isValid' => ['required', 'boolean'],
            'utilisateur_id' => ['required', 'exists:utilisateurs,id'],
        ]);

        $response = $question->responses()->create($validated);

        return new ResponseResource($response);
    }
}
