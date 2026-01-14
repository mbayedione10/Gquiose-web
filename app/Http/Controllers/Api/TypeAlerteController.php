<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TypeAlerteStoreRequest;
use App\Http\Requests\TypeAlerteUpdateRequest;
use App\Http\Resources\TypeAlerteCollection;
use App\Http\Resources\TypeAlerteResource;
use App\Models\TypeAlerte;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TypeAlerteController extends Controller
{
    public function index(Request $request): TypeAlerteCollection
    {
        $this->authorize('view-any', TypeAlerte::class);

        $search = $request->get('search', '');

        $typeAlertes = TypeAlerte::search($search)
            ->latest()
            ->paginate();

        return new TypeAlerteCollection($typeAlertes);
    }

    public function store(TypeAlerteStoreRequest $request): TypeAlerteResource
    {
        $this->authorize('create', TypeAlerte::class);

        $validated = $request->validated();

        $typeAlerte = TypeAlerte::create($validated);

        return new TypeAlerteResource($typeAlerte);
    }

    public function show(
        Request $request,
        TypeAlerte $typeAlerte
    ): TypeAlerteResource {
        $this->authorize('view', $typeAlerte);

        return new TypeAlerteResource($typeAlerte);
    }

    public function update(
        TypeAlerteUpdateRequest $request,
        TypeAlerte $typeAlerte
    ): TypeAlerteResource {
        $this->authorize('update', $typeAlerte);

        $validated = $request->validated();

        $typeAlerte->update($validated);

        return new TypeAlerteResource($typeAlerte);
    }

    public function destroy(Request $request, TypeAlerte $typeAlerte): Response
    {
        $this->authorize('delete', $typeAlerte);

        $typeAlerte->delete();

        return response()->noContent();
    }
}
