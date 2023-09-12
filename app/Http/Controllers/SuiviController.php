<?php

namespace App\Http\Controllers;

use App\Models\Suivi;
use App\Models\Alerte;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\SuiviStoreRequest;
use App\Http\Requests\SuiviUpdateRequest;

class SuiviController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Suivi::class);

        $search = $request->get('search', '');

        $suivis = Suivi::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.suivis.index', compact('suivis', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Suivi::class);

        $alertes = Alerte::pluck('ref', 'id');

        return view('app.suivis.create', compact('alertes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SuiviStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Suivi::class);

        $validated = $request->validated();

        $suivi = Suivi::create($validated);

        return redirect()
            ->route('suivis.edit', $suivi)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Suivi $suivi): View
    {
        $this->authorize('view', $suivi);

        return view('app.suivis.show', compact('suivi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Suivi $suivi): View
    {
        $this->authorize('update', $suivi);

        $alertes = Alerte::pluck('ref', 'id');

        return view('app.suivis.edit', compact('suivi', 'alertes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        SuiviUpdateRequest $request,
        Suivi $suivi
    ): RedirectResponse {
        $this->authorize('update', $suivi);

        $validated = $request->validated();

        $suivi->update($validated);

        return redirect()
            ->route('suivis.edit', $suivi)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Suivi $suivi): RedirectResponse
    {
        $this->authorize('delete', $suivi);

        $suivi->delete();

        return redirect()
            ->route('suivis.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
