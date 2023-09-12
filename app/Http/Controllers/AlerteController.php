<?php

namespace App\Http\Controllers;

use App\Models\Ville;
use App\Models\Alerte;
use Illuminate\View\View;
use App\Models\TypeAlerte;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\AlerteStoreRequest;
use App\Http\Requests\AlerteUpdateRequest;

class AlerteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Alerte::class);

        $search = $request->get('search', '');

        $alertes = Alerte::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.alertes.index', compact('alertes', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Alerte::class);

        $typeAlertes = TypeAlerte::pluck('name', 'id');
        $villes = Ville::pluck('name', 'id');

        return view('app.alertes.create', compact('typeAlertes', 'villes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AlerteStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Alerte::class);

        $validated = $request->validated();

        $alerte = Alerte::create($validated);

        return redirect()
            ->route('alertes.edit', $alerte)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Alerte $alerte): View
    {
        $this->authorize('view', $alerte);

        return view('app.alertes.show', compact('alerte'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Alerte $alerte): View
    {
        $this->authorize('update', $alerte);

        $typeAlertes = TypeAlerte::pluck('name', 'id');
        $villes = Ville::pluck('name', 'id');

        return view(
            'app.alertes.edit',
            compact('alerte', 'typeAlertes', 'villes')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        AlerteUpdateRequest $request,
        Alerte $alerte
    ): RedirectResponse {
        $this->authorize('update', $alerte);

        $validated = $request->validated();

        $alerte->update($validated);

        return redirect()
            ->route('alertes.edit', $alerte)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Alerte $alerte): RedirectResponse
    {
        $this->authorize('delete', $alerte);

        $alerte->delete();

        return redirect()
            ->route('alertes.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
