<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UtilisateurStoreRequest;
use App\Http\Requests\UtilisateurUpdateRequest;

class UtilisateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Utilisateur::class);

        $search = $request->get('search', '');

        $utilisateurs = Utilisateur::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view(
            'app.utilisateurs.index',
            compact('utilisateurs', 'search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Utilisateur::class);

        return view('app.utilisateurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UtilisateurStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Utilisateur::class);

        $validated = $request->validated();

        $utilisateur = Utilisateur::create($validated);

        return redirect()
            ->route('utilisateurs.edit', $utilisateur)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Utilisateur $utilisateur): View
    {
        $this->authorize('view', $utilisateur);

        return view('app.utilisateurs.show', compact('utilisateur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Utilisateur $utilisateur): View
    {
        $this->authorize('update', $utilisateur);

        return view('app.utilisateurs.edit', compact('utilisateur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UtilisateurUpdateRequest $request,
        Utilisateur $utilisateur
    ): RedirectResponse {
        $this->authorize('update', $utilisateur);

        $validated = $request->validated();

        $utilisateur->update($validated);

        return redirect()
            ->route('utilisateurs.edit', $utilisateur)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Utilisateur $utilisateur
    ): RedirectResponse {
        $this->authorize('delete', $utilisateur);

        $utilisateur->delete();

        return redirect()
            ->route('utilisateurs.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
