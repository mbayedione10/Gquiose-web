<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VilleStoreRequest;
use App\Http\Requests\VilleUpdateRequest;
use App\Http\Resources\VilleCollection;
use App\Http\Resources\VilleResource;
use App\Models\Ville;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VilleController extends Controller
{
    public function index(Request $request): VilleCollection
    {
        $this->authorize('view-any', Ville::class);

        $search = $request->get('search', '');

        $villes = Ville::search($search)
            ->latest()
            ->paginate();

        return new VilleCollection($villes);
    }

    public function store(VilleStoreRequest $request): VilleResource
    {
        $this->authorize('create', Ville::class);

        $validated = $request->validated();

        $ville = Ville::create($validated);

        return new VilleResource($ville);
    }

    public function show(Request $request, Ville $ville): VilleResource
    {
        $this->authorize('view', $ville);

        return new VilleResource($ville);
    }

    public function update(
        VilleUpdateRequest $request,
        Ville $ville
    ): VilleResource {
        $this->authorize('update', $ville);

        $validated = $request->validated();

        $ville->update($validated);

        return new VilleResource($ville);
    }

    public function destroy(Request $request, Ville $ville): Response
    {
        $this->authorize('delete', $ville);

        $ville->delete();

        return response()->noContent();
    }
}
