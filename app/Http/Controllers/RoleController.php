<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Role::class);

        $search = $request->get('search', '');

        $roles = Role::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.roles.index', compact('roles', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Role::class);

        return view('app.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Role::class);

        $validated = $request->validated();

        $role = Role::create($validated);

        return redirect()
            ->route('roles.edit', $role)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Role $role): View
    {
        $this->authorize('view', $role);

        return view('app.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Role $role): View
    {
        $this->authorize('update', $role);

        return view('app.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        RoleUpdateRequest $request,
        Role $role
    ): RedirectResponse {
        $this->authorize('update', $role);

        $validated = $request->validated();

        $role->update($validated);

        return redirect()
            ->route('roles.edit', $role)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
