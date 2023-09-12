<?php

namespace App\Http\Controllers\Api;

use App\Models\Rubrique;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\RubriqueResource;
use App\Http\Resources\RubriqueCollection;
use App\Http\Requests\RubriqueStoreRequest;
use App\Http\Requests\RubriqueUpdateRequest;

class RubriqueController extends Controller
{
    public function index(Request $request): RubriqueCollection
    {
        $this->authorize('view-any', Rubrique::class);

        $search = $request->get('search', '');

        $rubriques = Rubrique::search($search)
            ->latest()
            ->paginate();

        return new RubriqueCollection($rubriques);
    }

    public function store(RubriqueStoreRequest $request): RubriqueResource
    {
        $this->authorize('create', Rubrique::class);

        $validated = $request->validated();

        $rubrique = Rubrique::create($validated);

        return new RubriqueResource($rubrique);
    }

    public function show(Request $request, Rubrique $rubrique): RubriqueResource
    {
        $this->authorize('view', $rubrique);

        return new RubriqueResource($rubrique);
    }

    public function update(
        RubriqueUpdateRequest $request,
        Rubrique $rubrique
    ): RubriqueResource {
        $this->authorize('update', $rubrique);

        $validated = $request->validated();

        $rubrique->update($validated);

        return new RubriqueResource($rubrique);
    }

    public function destroy(Request $request, Rubrique $rubrique): Response
    {
        $this->authorize('delete', $rubrique);

        $rubrique->delete();

        return response()->noContent();
    }
}
