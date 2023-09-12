<?php

namespace App\Http\Controllers\Api;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\UtilisateurResource;
use App\Http\Resources\UtilisateurCollection;
use App\Http\Requests\UtilisateurStoreRequest;
use App\Http\Requests\UtilisateurUpdateRequest;

class UtilisateurController extends Controller
{
    public function index(Request $request): UtilisateurCollection
    {
        $this->authorize('view-any', Utilisateur::class);

        $search = $request->get('search', '');

        $utilisateurs = Utilisateur::search($search)
            ->latest()
            ->paginate();

        return new UtilisateurCollection($utilisateurs);
    }

    public function store(UtilisateurStoreRequest $request): UtilisateurResource
    {
        $this->authorize('create', Utilisateur::class);

        $validated = $request->validated();

        $utilisateur = Utilisateur::create($validated);

        return new UtilisateurResource($utilisateur);
    }

    public function show(
        Request $request,
        Utilisateur $utilisateur
    ): UtilisateurResource {
        $this->authorize('view', $utilisateur);

        return new UtilisateurResource($utilisateur);
    }

    public function update(
        UtilisateurUpdateRequest $request,
        Utilisateur $utilisateur
    ): UtilisateurResource {
        $this->authorize('update', $utilisateur);

        $validated = $request->validated();

        $utilisateur->update($validated);

        return new UtilisateurResource($utilisateur);
    }

    public function destroy(
        Request $request,
        Utilisateur $utilisateur
    ): Response {
        $this->authorize('delete', $utilisateur);

        $utilisateur->delete();

        return response()->noContent();
    }
}
