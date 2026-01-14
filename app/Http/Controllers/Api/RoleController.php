<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    public function index(Request $request): RoleCollection
    {
        $this->authorize('view-any', Role::class);

        $search = $request->get('search', '');

        $roles = Role::search($search)
            ->latest()
            ->paginate();

        return new RoleCollection($roles);
    }

    public function store(RoleStoreRequest $request): RoleResource
    {
        $this->authorize('create', Role::class);

        $validated = $request->validated();

        $role = Role::create($validated);

        return new RoleResource($role);
    }

    public function show(Request $request, Role $role): RoleResource
    {
        $this->authorize('view', $role);

        return new RoleResource($role);
    }

    public function update(RoleUpdateRequest $request, Role $role): RoleResource
    {
        $this->authorize('update', $role);

        $validated = $request->validated();

        $role->update($validated);

        return new RoleResource($role);
    }

    public function destroy(Request $request, Role $role): Response
    {
        $this->authorize('delete', $role);

        $role->delete();

        return response()->noContent();
    }
}
