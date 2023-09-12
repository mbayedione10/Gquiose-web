<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Thematique;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ThematiqueStoreRequest;
use App\Http\Requests\ThematiqueUpdateRequest;

class ThematiqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Thematique::class);

        $search = $request->get('search', '');

        $thematiques = Thematique::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.thematiques.index', compact('thematiques', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Thematique::class);

        return view('app.thematiques.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ThematiqueStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Thematique::class);

        $validated = $request->validated();

        $thematique = Thematique::create($validated);

        return redirect()
            ->route('thematiques.edit', $thematique)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Thematique $thematique): View
    {
        $this->authorize('view', $thematique);

        return view('app.thematiques.show', compact('thematique'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Thematique $thematique): View
    {
        $this->authorize('update', $thematique);

        return view('app.thematiques.edit', compact('thematique'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ThematiqueUpdateRequest $request,
        Thematique $thematique
    ): RedirectResponse {
        $this->authorize('update', $thematique);

        $validated = $request->validated();

        $thematique->update($validated);

        return redirect()
            ->route('thematiques.edit', $thematique)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Thematique $thematique
    ): RedirectResponse {
        $this->authorize('delete', $thematique);

        $thematique->delete();

        return redirect()
            ->route('thematiques.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
