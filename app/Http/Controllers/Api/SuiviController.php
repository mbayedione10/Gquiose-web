<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuiviStoreRequest;
use App\Http\Requests\SuiviUpdateRequest;
use App\Http\Resources\SuiviCollection;
use App\Http\Resources\SuiviResource;
use App\Models\Suivi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SuiviController extends Controller
{
    public function index(Request $request): SuiviCollection
    {
        $this->authorize('view-any', Suivi::class);

        $search = $request->get('search', '');

        $suivis = Suivi::search($search)
            ->latest()
            ->paginate();

        return new SuiviCollection($suivis);
    }

    public function store(SuiviStoreRequest $request): SuiviResource
    {
        $this->authorize('create', Suivi::class);

        $validated = $request->validated();

        $suivi = Suivi::create($validated);

        return new SuiviResource($suivi);
    }

    public function show(Request $request, Suivi $suivi): SuiviResource
    {
        $this->authorize('view', $suivi);

        return new SuiviResource($suivi);
    }

    public function update(
        SuiviUpdateRequest $request,
        Suivi $suivi
    ): SuiviResource {
        $this->authorize('update', $suivi);

        $validated = $request->validated();

        $suivi->update($validated);

        return new SuiviResource($suivi);
    }

    public function destroy(Request $request, Suivi $suivi): Response
    {
        $this->authorize('delete', $suivi);

        $suivi->delete();

        return response()->noContent();
    }
}
