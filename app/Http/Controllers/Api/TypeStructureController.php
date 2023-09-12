<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\TypeStructure;
use App\Http\Controllers\Controller;
use App\Http\Resources\TypeStructureResource;
use App\Http\Resources\TypeStructureCollection;
use App\Http\Requests\TypeStructureStoreRequest;
use App\Http\Requests\TypeStructureUpdateRequest;

class TypeStructureController extends Controller
{
    public function index(Request $request): TypeStructureCollection
    {
        $this->authorize('view-any', TypeStructure::class);

        $search = $request->get('search', '');

        $typeStructures = TypeStructure::search($search)
            ->latest()
            ->paginate();

        return new TypeStructureCollection($typeStructures);
    }

    public function store(
        TypeStructureStoreRequest $request
    ): TypeStructureResource {
        $this->authorize('create', TypeStructure::class);

        $validated = $request->validated();

        $typeStructure = TypeStructure::create($validated);

        return new TypeStructureResource($typeStructure);
    }

    public function show(
        Request $request,
        TypeStructure $typeStructure
    ): TypeStructureResource {
        $this->authorize('view', $typeStructure);

        return new TypeStructureResource($typeStructure);
    }

    public function update(
        TypeStructureUpdateRequest $request,
        TypeStructure $typeStructure
    ): TypeStructureResource {
        $this->authorize('update', $typeStructure);

        $validated = $request->validated();

        $typeStructure->update($validated);

        return new TypeStructureResource($typeStructure);
    }

    public function destroy(
        Request $request,
        TypeStructure $typeStructure
    ): Response {
        $this->authorize('delete', $typeStructure);

        $typeStructure->delete();

        return response()->noContent();
    }
}
