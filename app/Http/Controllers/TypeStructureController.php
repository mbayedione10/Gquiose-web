<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypeStructureStoreRequest;
use App\Http\Requests\TypeStructureUpdateRequest;
use App\Models\TypeStructure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TypeStructureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', TypeStructure::class);

        $search = $request->get('search', '');

        $typeStructures = TypeStructure::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view(
            'app.type_structures.index',
            compact('typeStructures', 'search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', TypeStructure::class);

        return view('app.type_structures.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TypeStructureStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', TypeStructure::class);

        $validated = $request->validated();

        $typeStructure = TypeStructure::create($validated);

        return redirect()
            ->route('type-structures.edit', $typeStructure)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, TypeStructure $typeStructure): View
    {
        $this->authorize('view', $typeStructure);

        return view('app.type_structures.show', compact('typeStructure'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, TypeStructure $typeStructure): View
    {
        $this->authorize('update', $typeStructure);

        return view('app.type_structures.edit', compact('typeStructure'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        TypeStructureUpdateRequest $request,
        TypeStructure $typeStructure
    ): RedirectResponse {
        $this->authorize('update', $typeStructure);

        $validated = $request->validated();

        $typeStructure->update($validated);

        return redirect()
            ->route('type-structures.edit', $typeStructure)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        TypeStructure $typeStructure
    ): RedirectResponse {
        $this->authorize('delete', $typeStructure);

        $typeStructure->delete();

        return redirect()
            ->route('type-structures.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
