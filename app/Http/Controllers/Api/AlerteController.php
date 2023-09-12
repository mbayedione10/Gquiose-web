<?php

namespace App\Http\Controllers\Api;

use App\Models\Alerte;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\AlerteResource;
use App\Http\Resources\AlerteCollection;
use App\Http\Requests\AlerteStoreRequest;
use App\Http\Requests\AlerteUpdateRequest;

class AlerteController extends Controller
{
    public function index(Request $request): AlerteCollection
    {
        $this->authorize('view-any', Alerte::class);

        $search = $request->get('search', '');

        $alertes = Alerte::search($search)
            ->latest()
            ->paginate();

        return new AlerteCollection($alertes);
    }

    public function store(AlerteStoreRequest $request): AlerteResource
    {
        $this->authorize('create', Alerte::class);

        $validated = $request->validated();

        $alerte = Alerte::create($validated);

        return new AlerteResource($alerte);
    }

    public function show(Request $request, Alerte $alerte): AlerteResource
    {
        $this->authorize('view', $alerte);

        return new AlerteResource($alerte);
    }

    public function update(
        AlerteUpdateRequest $request,
        Alerte $alerte
    ): AlerteResource {
        $this->authorize('update', $alerte);

        $validated = $request->validated();

        $alerte->update($validated);

        return new AlerteResource($alerte);
    }

    public function destroy(Request $request, Alerte $alerte): Response
    {
        $this->authorize('delete', $alerte);

        $alerte->delete();

        return response()->noContent();
    }
}
