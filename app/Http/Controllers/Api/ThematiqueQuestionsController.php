<?php

namespace App\Http\Controllers\Api;

use App\Models\Thematique;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\QuestionCollection;

class ThematiqueQuestionsController extends Controller
{
    public function index(
        Request $request,
        Thematique $thematique
    ): QuestionCollection {
        $this->authorize('view', $thematique);

        $search = $request->get('search', '');

        $questions = $thematique
            ->questions()
            ->search($search)
            ->latest()
            ->paginate();

        return new QuestionCollection($questions);
    }

    public function store(
        Request $request,
        Thematique $thematique
    ): QuestionResource {
        $this->authorize('create', Question::class);

        $validated = $request->validate([
            'name' => [
                'required',
                'unique:questions,name',
                'max:255',
                'string',
            ],
            'reponse' => ['required', 'max:255', 'string'],
            'option1' => ['required', 'max:255', 'string'],
            'status' => ['required', 'boolean'],
        ]);

        $question = $thematique->questions()->create($validated);

        return new QuestionResource($question);
    }
}
