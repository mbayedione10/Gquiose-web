<?php

namespace App\Http\Controllers;

use App\Models\Rubrique;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\RubriqueStoreRequest;
use App\Http\Requests\RubriqueUpdateRequest;

class RubriqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Rubrique::class);

        $search = $request->get('search', '');

        $rubriques = Rubrique::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.rubriques.index', compact('rubriques', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Rubrique::class);

        return view('app.rubriques.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RubriqueStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Rubrique::class);

        $validated = $request->validated();

        $rubrique = Rubrique::create($validated);

        return redirect()
            ->route('rubriques.edit', $rubrique)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Rubrique $rubrique): View
    {
        $this->authorize('view', $rubrique);

        return view('app.rubriques.show', compact('rubrique'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Rubrique $rubrique): View
    {
        $this->authorize('update', $rubrique);

        return view('app.rubriques.edit', compact('rubrique'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        RubriqueUpdateRequest $request,
        Rubrique $rubrique
    ): RedirectResponse {
        $this->authorize('update', $rubrique);

        $validated = $request->validated();

        $rubrique->update($validated);

        return redirect()
            ->route('rubriques.edit', $rubrique)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Rubrique $rubrique
    ): RedirectResponse {
        $this->authorize('delete', $rubrique);

        $rubrique->delete();

        return redirect()
            ->route('rubriques.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
