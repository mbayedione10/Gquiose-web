<?php

namespace App\Http\Controllers;

use App\Http\Requests\VilleStoreRequest;
use App\Http\Requests\VilleUpdateRequest;
use App\Models\Ville;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VilleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Ville::class);

        $search = $request->get('search', '');

        $villes = Ville::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.villes.index', compact('villes', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Ville::class);

        return view('app.villes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VilleStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Ville::class);

        $validated = $request->validated();

        $ville = Ville::create($validated);

        return redirect()
            ->route('villes.edit', $ville)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Ville $ville): View
    {
        $this->authorize('view', $ville);

        return view('app.villes.show', compact('ville'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Ville $ville): View
    {
        $this->authorize('update', $ville);

        return view('app.villes.edit', compact('ville'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        VilleUpdateRequest $request,
        Ville $ville
    ): RedirectResponse {
        $this->authorize('update', $ville);

        $validated = $request->validated();

        $ville->update($validated);

        return redirect()
            ->route('villes.edit', $ville)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Ville $ville): RedirectResponse
    {
        $this->authorize('delete', $ville);

        $ville->delete();

        return redirect()
            ->route('villes.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
