<?php

namespace App\Http\Controllers\Api;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\QuestionCollection;
use App\Http\Requests\QuestionStoreRequest;
use App\Http\Requests\QuestionUpdateRequest;

class QuestionController extends Controller
{
    public function index(Request $request): QuestionCollection
    {
        $this->authorize('view-any', Question::class);

        $search = $request->get('search', '');

        $questions = Question::search($search)
            ->latest()
            ->paginate();

        return new QuestionCollection($questions);
    }

    public function store(QuestionStoreRequest $request): QuestionResource
    {
        $this->authorize('create', Question::class);

        $validated = $request->validated();

        $question = Question::create($validated);

        return new QuestionResource($question);
    }

    public function show(Request $request, Question $question): QuestionResource
    {
        $this->authorize('view', $question);

        return new QuestionResource($question);
    }

    public function update(
        QuestionUpdateRequest $request,
        Question $question
    ): QuestionResource {
        $this->authorize('update', $question);

        $validated = $request->validated();

        $question->update($validated);

        return new QuestionResource($question);
    }

    public function destroy(Request $request, Question $question): Response
    {
        $this->authorize('delete', $question);

        $question->delete();

        return response()->noContent();
    }
}
