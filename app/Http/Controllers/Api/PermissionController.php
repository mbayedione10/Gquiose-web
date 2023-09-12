<?php

namespace App\Http\Controllers\Api;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\PermissionCollection;
use App\Http\Requests\PermissionStoreRequest;
use App\Http\Requests\PermissionUpdateRequest;

class PermissionController extends Controller
{
    public function index(Request $request): PermissionCollection
    {
        $this->authorize('view-any', Permission::class);

        $search = $request->get('search', '');

        $permissions = Permission::search($search)
            ->latest()
            ->paginate();

        return new PermissionCollection($permissions);
    }

    public function store(PermissionStoreRequest $request): PermissionResource
    {
        $this->authorize('create', Permission::class);

        $validated = $request->validated();

        $permission = Permission::create($validated);

        return new PermissionResource($permission);
    }

    public function show(
        Request $request,
        Permission $permission
    ): PermissionResource {
        $this->authorize('view', $permission);

        return new PermissionResource($permission);
    }

    public function update(
        PermissionUpdateRequest $request,
        Permission $permission
    ): PermissionResource {
        $this->authorize('update', $permission);

        $validated = $request->validated();

        $permission->update($validated);

        return new PermissionResource($permission);
    }

    public function destroy(Request $request, Permission $permission): Response
    {
        $this->authorize('delete', $permission);

        $permission->delete();

        return response()->noContent();
    }
}
