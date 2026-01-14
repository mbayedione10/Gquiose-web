<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StructureStoreRequest;
use App\Http\Requests\StructureUpdateRequest;
use App\Http\Resources\StructureCollection;
use App\Http\Resources\StructureResource;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StructureController extends Controller
{
    public function index(Request $request): StructureCollection
    {
        $this->authorize('view-any', Structure::class);

        $search = $request->get('search', '');

        $structures = Structure::search($search)
            ->latest()
            ->paginate();

        return new StructureCollection($structures);
    }

    public function store(StructureStoreRequest $request): StructureResource
    {
        $this->authorize('create', Structure::class);

        $validated = $request->validated();

        $structure = Structure::create($validated);

        return new StructureResource($structure);
    }

    public function show(
        Request $request,
        Structure $structure
    ): StructureResource {
        $this->authorize('view', $structure);

        return new StructureResource($structure);
    }

    public function update(
        StructureUpdateRequest $request,
        Structure $structure
    ): StructureResource {
        $this->authorize('update', $structure);

        $validated = $request->validated();

        $structure->update($validated);

        return new StructureResource($structure);
    }

    public function destroy(Request $request, Structure $structure): Response
    {
        $this->authorize('delete', $structure);

        $structure->delete();

        return response()->noContent();
    }
}
