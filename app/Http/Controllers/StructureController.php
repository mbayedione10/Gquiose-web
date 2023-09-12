<?php

namespace App\Http\Controllers;

use App\Models\Ville;
use App\Models\Structure;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\TypeStructure;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StructureStoreRequest;
use App\Http\Requests\StructureUpdateRequest;

class StructureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Structure::class);

        $search = $request->get('search', '');

        $structures = Structure::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.structures.index', compact('structures', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Structure::class);

        $typeStructures = TypeStructure::pluck('name', 'id');
        $villes = Ville::pluck('name', 'id');

        return view(
            'app.structures.create',
            compact('typeStructures', 'villes')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StructureStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Structure::class);

        $validated = $request->validated();

        $structure = Structure::create($validated);

        return redirect()
            ->route('structures.edit', $structure)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Structure $structure): View
    {
        $this->authorize('view', $structure);

        return view('app.structures.show', compact('structure'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Structure $structure): View
    {
        $this->authorize('update', $structure);

        $typeStructures = TypeStructure::pluck('name', 'id');
        $villes = Ville::pluck('name', 'id');

        return view(
            'app.structures.edit',
            compact('structure', 'typeStructures', 'villes')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        StructureUpdateRequest $request,
        Structure $structure
    ): RedirectResponse {
        $this->authorize('update', $structure);

        $validated = $request->validated();

        $structure->update($validated);

        return redirect()
            ->route('structures.edit', $structure)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Structure $structure
    ): RedirectResponse {
        $this->authorize('delete', $structure);

        $structure->delete();

        return redirect()
            ->route('structures.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
