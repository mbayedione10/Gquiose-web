<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\TypeAlerte;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\TypeAlerteStoreRequest;
use App\Http\Requests\TypeAlerteUpdateRequest;

class TypeAlerteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', TypeAlerte::class);

        $search = $request->get('search', '');

        $typeAlertes = TypeAlerte::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.type_alertes.index', compact('typeAlertes', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', TypeAlerte::class);

        return view('app.type_alertes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TypeAlerteStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', TypeAlerte::class);

        $validated = $request->validated();

        $typeAlerte = TypeAlerte::create($validated);

        return redirect()
            ->route('type-alertes.edit', $typeAlerte)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, TypeAlerte $typeAlerte): View
    {
        $this->authorize('view', $typeAlerte);

        return view('app.type_alertes.show', compact('typeAlerte'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, TypeAlerte $typeAlerte): View
    {
        $this->authorize('update', $typeAlerte);

        return view('app.type_alertes.edit', compact('typeAlerte'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        TypeAlerteUpdateRequest $request,
        TypeAlerte $typeAlerte
    ): RedirectResponse {
        $this->authorize('update', $typeAlerte);

        $validated = $request->validated();

        $typeAlerte->update($validated);

        return redirect()
            ->route('type-alertes.edit', $typeAlerte)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        TypeAlerte $typeAlerte
    ): RedirectResponse {
        $this->authorize('delete', $typeAlerte);

        $typeAlerte->delete();

        return redirect()
            ->route('type-alertes.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
