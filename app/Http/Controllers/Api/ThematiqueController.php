<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThematiqueStoreRequest;
use App\Http\Requests\ThematiqueUpdateRequest;
use App\Http\Resources\ThematiqueCollection;
use App\Http\Resources\ThematiqueResource;
use App\Models\Thematique;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ThematiqueController extends Controller
{
    public function index(Request $request): ThematiqueCollection
    {
        $this->authorize('view-any', Thematique::class);

        $search = $request->get('search', '');

        $thematiques = Thematique::search($search)
            ->latest()
            ->paginate();

        return new ThematiqueCollection($thematiques);
    }

    public function store(ThematiqueStoreRequest $request): ThematiqueResource
    {
        $this->authorize('create', Thematique::class);

        $validated = $request->validated();

        $thematique = Thematique::create($validated);

        return new ThematiqueResource($thematique);
    }

    public function show(
        Request $request,
        Thematique $thematique
    ): ThematiqueResource {
        $this->authorize('view', $thematique);

        return new ThematiqueResource($thematique);
    }

    public function update(
        ThematiqueUpdateRequest $request,
        Thematique $thematique
    ): ThematiqueResource {
        $this->authorize('update', $thematique);

        $validated = $request->validated();

        $thematique->update($validated);

        return new ThematiqueResource($thematique);
    }

    public function destroy(Request $request, Thematique $thematique): Response
    {
        $this->authorize('delete', $thematique);

        $thematique->delete();

        return response()->noContent();
    }
}
