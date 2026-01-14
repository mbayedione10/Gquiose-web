<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionStoreRequest;
use App\Http\Requests\PermissionUpdateRequest;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Permission::class);

        $search = $request->get('search', '');

        $permissions = Permission::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.permissions.index', compact('permissions', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Permission::class);

        return view('app.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Permission::class);

        $validated = $request->validated();

        $permission = Permission::create($validated);

        return redirect()
            ->route('permissions.edit', $permission)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Permission $permission): View
    {
        $this->authorize('view', $permission);

        return view('app.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Permission $permission): View
    {
        $this->authorize('update', $permission);

        return view('app.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        PermissionUpdateRequest $request,
        Permission $permission
    ): RedirectResponse {
        $this->authorize('update', $permission);

        $validated = $request->validated();

        $permission->update($validated);

        return redirect()
            ->route('permissions.edit', $permission)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Permission $permission
    ): RedirectResponse {
        $this->authorize('delete', $permission);

        $permission->delete();

        return redirect()
            ->route('permissions.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
